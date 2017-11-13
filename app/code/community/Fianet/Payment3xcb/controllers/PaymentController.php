<?php

/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please contact us
 * via http://www.fia-net-group.com/formulaire.php so we can send you a copy immediately.
 *
 * @author     FIA-NET <support-boutique@fia-net.com>
 * @copyright  Copyright (c) 2017 FIA-NET
 * @version    Release: $Revision: 0.1.0 $
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Fianet_Payment3xcb_PaymentController extends Mage_Core_Controller_Front_Action
{
    const AUTO_SEND = true;

    /**
     * @return Fianet_Payment3xcb_Model_Logger
     */
    protected function logger()
    {
        /** @var Fianet_Payment3xcb_Model_Logger $logger */
        $logger = Mage::getSingleton('fianetpayment3xcb/logger');
        return $logger;
    }

    /**
     * @return string
     */
    protected function getPostQuery()
    {
        return http_build_query($this->getRequest()->getPost(), '', '&');
    }

    /**
     * @param string $receivedChecksum
     * @param array $data
     */
    protected function controlChecksum($receivedChecksum, $data)
    {
        /** @var Fianet_Payment3xcb_Model_Fianet_Checksum $checksum */
        $checksum = Mage::getSingleton('fianetpayment3xcb/fianet_checksum');
        $calculatedChecksum = $checksum->getHash($data);

        if ($calculatedChecksum != $receivedChecksum) {
            Mage::throwException(Mage::helper('fianetpayment3xcb')->__('Checksum error'));
        }
    }

    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function getCheckoutSession()
    {
        /** @var Mage_Checkout_Model_Session $session */
        $session = Mage::getSingleton('checkout/session');
        return $session;
    }

    /**
     * @return Mage_Core_Model_Session
     */
    protected function getCoreSession()
    {
        /** @var Mage_Core_Model_Session $session */
        $session = Mage::getSingleton('core/session');
        return $session;
    }

    /**
     * Redirection to 3xCB
     */
    public function redirectAction()
    {
        $this->logger()->setEvent(
            Mage::helper('fianetpayment3xcb')->__('Redirect To 3xCB Platform')
        );
        if (!$this->getCheckoutSession()->getLastRealOrderId()) {
            $this->_redirect('checkout/onepage/failure');
        } else {
            $this->logger()->notice(
                Mage::helper('fianetpayment3xcb')->__('Order #%s', $this->getCheckoutSession()->getLastRealOrderId())
            );

            /** @var Fianet_Payment3xcb_Block_Payment_Redirect $block */
            $block = $this->getLayout()->createBlock('fianetpayment3xcb/payment_redirect');

            $block->setAutoSend(self::AUTO_SEND);
            $block->setHasError(false);

            try {
                $storeCode = Mage::app()->getStore(true)->getCode();
                /** @var Fianet_Payment3xcb_Helper_Data $helper */
                $helper = Mage::helper('fianetpayment3xcb');
                $mode = $helper->getPaymentMode();

                /** @var Fianet_Payment3xcb_Model_Payment_Method_Fianet3xcb $paymentMethod */
                $paymentMethod = Mage::getModel('fianetpayment3xcb/payment_method_fianet3xcb');
                $session = $this->getCheckoutSession();
                $params = array(
                    '_forced_secure' => true,
                    '___store' => $storeCode, 
                    $session->getSessionIdQueryParam() => $session->getEncryptedSessionId(),
                );
                $block->setUrlCall(Mage::getUrl('fianetpayment3xcb/payment/urlcall', $params));
                $block->setUrlSys(Mage::getUrl('fianetpayment3xcb/payment/urlsys', $params));
                $block->setFormUrl(Mage::getStoreConfig("fianetpayment3xcb/{$mode}/url_webapp_payment") . 'xmlfeed');

                /** @var Fianet_Payment3xcb_Model_Fianet_Xml_Param $xmlParam */
                $xmlParam = Mage::getModel('fianetpayment3xcb/fianet_xml_param');
                $xmlParam->addData('mode', $mode);
                $block->setXmlParam($xmlParam->asXml());

                $xml = $paymentMethod->getFianetXml();
                $block->setXmlInfo($xml->asXml());

                /** @var Fianet_Payment3xcb_Model_Fianet_Checksum $checksum */
                $checksum = Mage::getSingleton('fianetpayment3xcb/fianet_checksum');
                $block->setChecksum(
                    $checksum->getHash(
                        array(
                            'URLCall' => $block->getUrlCall(),
                            'URLSys' => $block->getUrlSys(),
                        )
                    )
                );
            } catch (Exception $e) {
                $block->setHasError(true);
                $this->logger()->error($e->getMessage());
                /** @var Mage_AdminNotification_Model_Inbox $notification */
                $notification = Mage::getModel('adminnotification/inbox');
                $notification->setSeverity(Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR);
                $notification->setTitle($this->__('3xCB error'));
                /** @var Mage_Core_Model_Date $date */
                $date = Mage::getSingleton('core/date');
                $notification->setDateAdded($date->gmtDate());
                $notification->setDescription(
                    $this->__('An error occurred during payment redirection: %s', $e->getMessage())
                );
                /** @var Mage_Adminhtml_Helper_Data $helper */
                $helper = Mage::helper('adminhtml');
                $notification->setUrl($helper->getUrl("fianetpayment3xcb/adminhtml_log/"));
                $notification->save();

                $this->restoreCart('');
            }

            $this->getResponse()->setBody(
                $block->toHtml()
            );
        }
    }

    public function urlsysAction()
    {
        $this->logger()->setEvent(
            Mage::helper('fianetpayment3xcb')->__('Automatic Update')
        );

        /** @var Fianet_Payment3xcb_Helper_Data $helper */
        $helper = Mage::helper('fianetpayment3xcb');

        try {
            $body = $this->getRequest()->getRawBody();
            $this->logger()->debug($body);

            /** @var Fianet_Payment3xcb_Model_Fianet_Result $result */
            $result = Mage::getModel('fianetpayment3xcb/fianet_result');
            $result->setBody($body);

            $this->controlChecksum(
                $result->get('checksum'),
                array(
                    'refid'         => $result->get('refid'),
                    'top3reference' => $result->get('top3reference'),
                    'currentamount' => $result->get('currentamount'),
                    'state'         => $result->get('state'),
                    'event'         => $result->get('event'),
                )
            );
            $state = $result->get('state');
            $this->logger()->notice(
                $helper->__(
                    'Order #%s: %s',
                    $result->get('refid'),
                    $helper->getTransactionStateLabel($state) . ' (' . $state . ')'
                )
            );

            /** @var Fianet_Payment3xcb_Model_Transaction_History $history */
            $history = Mage::getModel('fianetpayment3xcb/transaction_history');
            $history->loadFromResult($result);
        } catch (Exception $e) {
            $this->logger()->error($e->getMessage());
        }

        return $this->_redirect('');
    }

    public function urlcallAction()
    {
        $this->logger()->setEvent(
            Mage::helper('fianetpayment3xcb')->__('Back From 3xCB Platform')
        );

        $request = $this->getRequest();

        /** @var Fianet_Payment3xcb_Helper_Data $helper */
        $helper = Mage::helper('fianetpayment3xcb');

        try {
            $this->logger()->debug($this->getPostQuery());

            $this->controlChecksum(
                $request->getPost('CheckSum'),
                array(
                    'Montant'       => $request->getPost('Montant'),
                    'RefID'         => $request->getPost('RefID'),
                    'Top3Reference' => $request->getPost('Top3Reference'),
                    'State'         => $request->getPost('State'),
                )
            );
            $state = $request->getPost('State');
            $this->logger()->notice(
                $this->__('Order #%s: %s',
                    $request->getPost('RefID'),
                    $helper->getTransactionStateLabel($state) . ' (' . $state . ')'
                )
            );

            if ($state == 'PAYMENT_ABORTED') {
                if ($this->restoreCart($state)) {
                    $this->getCoreSession()->addNotice(
                        $this->__('Payment aborted, you can try to place your order again')
                    );
                    return $this->_redirect('checkout/onepage');
                }

                $this->getCoreSession()->addError(
                    $this->__('Payment aborted')
                );
                return $this->_redirect('checkout/onepage');
            } elseif ($this->isSuccessState($state)) {
                if ($this->getCheckoutSession()->getQuoteId()) {
                    $this->getCheckoutSession()->unsQuoteId();
                }

                return $this->_redirect('checkout/onepage/success');
            } elseif ($this->isFailureState($state)) {
                return $this->_redirect('checkout/onepage/failure');
            } else {
                $this->restoreCart('UNKNOWN_ERROR');
                $this->getCoreSession()->addError(
                    $this->__('Unknown Error')
                );
                return $this->_redirect('checkout/onepage');
            }
        } catch (Exception $e) {
            $this->logger()->error($e->getMessage());
            return $this->_redirect('checkout/onepage');
        }
    }

    /**
     * @param $state
     * @return bool
     */
    protected function isSuccessState($state)
    {
        return in_array(
            $state,
            array(
                'PAYMENT_STORED',
            )
        );
    }

    /**
     * @param $state
     * @return bool
     */
    protected function isFailureState($state)
    {
        return in_array(
            $state,
            array(
                'REQUEST_KO',
                'PAYMENT_KO',
                'CANCELLATION_ASKED',
                'PAYMENT_CANCELLED',
                'CONTRACT_REVIEW_IN_PROGRESS',
                'CONTRACT_REFUSED',
                'CONTRACT_ACCEPTED',
                'CONTRACT_SENT',
                'VALIDATION_ASKED',
                'PARTIAL_VALIDATION_ASKED',
                'PAYMENT_VALIDATED',
                'DEBIT_SENT',
            )
        );
    }

    /**
     * @param string $state
     * @return bool
     */
    protected function restoreCart($state)
    {
        $session = $this->getCheckoutSession();
        $lastQuoteId = $session->getLastQuoteId();
        $lastOrderId = $session->getLastOrderId();
        if ($lastQuoteId && $lastOrderId) {
            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order')->load($lastOrderId);
            $payment = $order->getPayment();
            if ($order->canCancel() && $payment && $payment->getMethod() == 'fianet3xcb') {
                /** @var Mage_Sales_Model_Quote $quote */
                $quote = Mage::getModel('sales/quote')->load($lastQuoteId);
                $quote->setIsActive(true)
                    ->setReservedOrderId(null)
                    ->save();

                $order->cancel();
                $order->setStatus('canceled');
                $order->setFianet3xcbState($state);
                $order->save();
                return true;
            }
        }

        return false;
    }
}
