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

class Fianet_Payment3xcb_Adminhtml_Transaction_ControlController extends Fianet_Payment3xcb_Controller_Adminhtml_Action
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->getAdminSession()
            ->isAllowed('admin/sales/fianet3xcb_transaction_control');
    }

    /**
     * @param string $message
     * @param Exception $exception
     */
    protected function addError($message, Exception $exception)
    {
        $this->logger()->error($message . ' ' . $exception->getMessage());
        $this->_getSession()->addError($message);
    }

    /**
     * @return Fianet_Payment3xcb_Model_Fianet_Api
     */
    protected function getApi()
    {
        /** @var Fianet_Payment3xcb_Model_Fianet_Api $api */
        $api = Mage::getSingleton('fianetpayment3xcb/fianet_api');
        return $api;
    }

    /**
     * @param Fianet_Payment3xcb_Model_Fianet_Api_Response $response
     * @return bool
     */
    protected function getActionResult(Fianet_Payment3xcb_Model_Fianet_Api_Response $response)
    {
        $status = $response->getStatus();
        $message = '[' . $status . '] ' . $response->getMessage();
        switch ($status) {
            case 200:
                $result = $response->getResult();
                $code = $result->get('code');
                $label = $result->get('libelle');
                if (!$label) {
                    Mage::throwException(
                        $this->__('API: invalid response') . ' - ' . $result->toString()
                    );
                    return false;
                } elseif ($code != 'OK') {
                    Mage::throwException(
                        $this->__($label)
                    );
                    return false;
                }

                $this->_getSession()->addSuccess($this->__($label));
                return true;
            case 400:
                Mage::throwException(
                    $this->__('API: invalid request') . ' - ' . $message
                );
                return false;
            case 401:
                Mage::throwException(
                    $this->__('API: unauthorized merchant or bad checksum') . ' - ' . $message
                );
                return false;
            case 404:
                Mage::throwException(
                    $this->__('API: transaction not found') . ' - ' . $message
                );
                return false;
            case 500:
                Mage::throwException(
                    $this->__('API: internal error') . ' - ' . $message
                );
                return false;
            default:
                Mage::throwException(
                    $this->__('API: error') . ' - ' . $message
                );
                return false;
        }
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    protected function getOrder()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            Mage::throwException(
                $this->__('Order ID is empty')
            );
            return null;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);
        if (!$order->getId()) {
            Mage::throwException(
                $this->__('Order not found')
            );
            return null;
        }

        return $order;
    }

    /**
     * @param $action = 'validate' || 'cancel' || 'partiallyvalidate'
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    protected function _sendAction($action, Mage_Sales_Model_Order $order)
    {
        /** @var Fianet_Payment3xcb_Model_Transaction_History $history */
        $history = Mage::getModel('fianetpayment3xcb/transaction_history');
        $history = $history->getLastOrderTransactionHistory($order->getIncrementId());

        if (empty($history)) {
            Mage::throwException(
                $this->__('Transaction not found')
            );
            return false;
        }

        $transactionReference = $history->getTop3reference();
        $api = $this->getApi();
        if ($action == 'cancel') {
            $response = $api->cancelTransaction($transactionReference);
        } elseif ($action == 'validate') {
            $response = $api->validateTransaction($transactionReference);
        } elseif ($action == 'partiallyvalidate') {
            $finalAmount = floatval(str_replace(',', '.', $this->getRequest()->getParam('amount')));
            $response = $api->partiallyValidateTransaction($transactionReference, $finalAmount);
        } else {
            Mage::throwException(
                $this->__('API: invalid action')
            );
            return false;
        }

        return $this->getActionResult($response);
    }

    //Load grid with transactions from sales/order
    public function indexAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        if ($storeId) {
            Mage::register('store_id_payment', $storeId);
        }

        $this->loadLayout();
        $this->setHeadTitle($this->__('Transaction Control'));
        $this->renderLayout();
    }

    public function validateAction()
    {
        try {
            $order = $this->getOrder();
            $this->logger()->notice(
                $this->__('Order #%s - Merchant validate the transaction.', $order->getIncrementId())
            );
            $this->_sendAction('validate', $order);
        } catch (Exception $exception) {
            $this->addError(
                $this->__(
                    'Order #%s - Unable to validate the transaction.',
                    isset($order) ? $order->getIncrementId() : '?'
                ),
                $exception
            );
        }

        return $this->redirectBack();
    }

    public function cancelAction()
    {
        try {
            $order = $this->getOrder();
            $this->logger()->notice(
                $this->__('Order #%s - Merchant cancel the transaction.', $order->getIncrementId())
            );
            $this->_sendAction('cancel', $order);
        } catch (Exception $exception) {
            $this->addError(
                $this->__(
                    'Order #%s - Unable to cancel the transaction.',
                    isset($order) ? $order->getIncrementId() : '?'
                ),
                $exception
            );
        }

        return $this->redirectBack();
    }

    public function partiallyvalidateAction()
    {
        try {
            $order = $this->getOrder();
            $this->logger()->notice(
                $this->__('Order #%s - Merchant partially validate the transaction.', $order->getIncrementId())
            );
            $this->_sendAction('partiallyvalidate', $order);
        } catch (Exception $exception) {
            $this->addError(
                $this->__(
                    'Order #%s - Unable to partially validate the transaction.',
                    isset($order) ? $order->getIncrementId() : '?'
                ),
                $exception
            );
        }

        return $this->redirectBack();
    }

    public function partiallyvalidateformAction()
    {
        if ($this->getRequest()->getParam('order_id')) {
            //load new page
            $this->loadLayout();
            $this->_setActiveMenu('adminfianetpayment3xcb');
            $this->getHeadBlock()->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('fianetpayment3xcb/adminhtml_transaction_control_edit'));
            $this->renderLayout();
        } else {
            $this->redirectBack();
        }
    }

    protected function redirectBack()
    {
        $this->_redirectReferer(Mage::getUrl('*/*/'));
    }
}
