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

/**
 * Class Fianet_Payment3xcb_Model_Transaction_History
 *
 * @method double getAmount()
 * @method string getMode()
 * @method string getOrderIncrementId()
 * @method string getState()
 * @method string getTop3reference()
 * @method Fianet_Payment3xcb_Model_Transaction_History setAmount(float $amount)
 * @method Fianet_Payment3xcb_Model_Transaction_History setCreatedAt(string $createdAt)
 * @method Fianet_Payment3xcb_Model_Transaction_History setLastRefresh(string $lastRefresh)
 * @method Fianet_Payment3xcb_Model_Transaction_History setMode(string $mode)
 * @method Fianet_Payment3xcb_Model_Transaction_History setOrderIncrementId(string $orderIncrementId)
 * @method Fianet_Payment3xcb_Model_Transaction_History setState(string $state)
 * @method Fianet_Payment3xcb_Model_Transaction_History setTop3reference(string $transactionReference)
 */
class Fianet_Payment3xcb_Model_Transaction_History extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('fianetpayment3xcb/transaction_history');
    }

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
     * @param Mage_Core_Controller_Request_Http $request
     * @return Fianet_Payment3xcb_Model_Transaction_History|null
     */
    public function loadFromRequest(Mage_Core_Controller_Request_Http $request)
    {
        return $this->loadFromData(
            $request->getPost('RefID'),
            $request->getPost('Top3Reference'),
            $request->getPost('Montant'),
            $request->getPost('State'),
            $request->getPost('mode', Fianet_Payment3xcb_Model_Source_Fianet_Mode::TEST)
        );
    }

    /**
     * @param Fianet_Payment3xcb_Model_Fianet_Result $result
     * @return Fianet_Payment3xcb_Model_Transaction_History|null
     */
    public function loadFromResult(Fianet_Payment3xcb_Model_Fianet_Result $result)
    {
        return $this->loadFromData(
            $result->get('refid'),
            $result->get('top3reference'),
            $result->get('currentamount'),
            $result->get('state'),
            $result->get('xmlparam/mode', Fianet_Payment3xcb_Model_Source_Fianet_Mode::TEST)
        );
    }

    /**
     * @param $orderIncrementId
     * @param $transactionReference
     * @param $amount
     * @param $state
     * @param $mode
     * @return null|Fianet_Payment3xcb_Model_Transaction_History
     */
    protected function loadFromData($orderIncrementId, $transactionReference, $amount, $state, $mode)
    {
        /** @var Mage_Core_Model_Date $date */
        $date = Mage::getSingleton('core/date');
        $now = $date->gmtDate();

        if (empty($orderIncrementId)) {
            Mage::throwException(
                Mage::helper('fianetpayment3xcb')->__('Order ID is undefined')
            );
            return null;
        }

        if (empty($amount)) {
            Mage::throwException(
                Mage::helper('fianetpayment3xcb')->__('Transaction amount is undefined')
            );
            return null;
        }

        if (empty($mode)) {
            Mage::throwException(
                Mage::helper('fianetpayment3xcb')->__('Transaction mode is undefined')
            );
            return null;
        }

        $lastPertinentTransactionHistory = $this->getLastPertinentTransactionHistory($transactionReference);

        /** @var Fianet_Payment3xcb_Model_Transaction_History $newTransactionHistory */
        $newTransactionHistory = Mage::getModel('fianetpayment3xcb/transaction_history');
        $newTransactionHistory->setTop3reference($transactionReference)
            ->setOrderIncrementId($orderIncrementId)
            ->setCreatedAt($now)
            ->setAmount($amount / 100)
            ->setState($state)
            ->setMode($mode);

        /** @var Fianet_Payment3xcb_Helper_Transaction $helper */
        $helper = Mage::helper('fianetpayment3xcb/transaction');
        if ($helper->areSame($lastPertinentTransactionHistory, $newTransactionHistory)) {
            $newTransactionHistory = $lastPertinentTransactionHistory;
        }

        $newTransactionHistory->setLastRefresh($now);
        $newTransactionHistory->save();

        // Reload last pertinent transaction history
        $lastPertinentTransactionHistory = $this->getLastPertinentTransactionHistory($transactionReference);
        if (!empty($lastPertinentTransactionHistory)) {
            $order = $lastPertinentTransactionHistory->getOrder();
            $oldState = $order->getFianet3xcbState();
            $lastState = $lastPertinentTransactionHistory->getState();
            $lastMode = $lastPertinentTransactionHistory->getMode();
            if ($oldState != $lastState || $order->getFianet3xcbMode() != $lastMode) {
                $order->setFianet3xcbState($lastState)
                    ->setFianet3xcbMode($lastMode)
                    ->save();
                $this->logStateChange($order, $oldState, $lastState);
                $lastPertinentTransactionHistory->updateOrderState();
            }
        }

        return $newTransactionHistory;
    }

    protected function logStateChange($order, $oldState, $newState)
    {
        /** @var Fianet_Payment3xcb_Helper_Data $helper */
        $helper = Mage::helper('fianetpayment3xcb');
        $this->logger()->notice(
            $helper->__(
                'Order #%s: %s',
                $order->getIncrementId(),
                ($oldState ? $helper->getTransactionStateLabel($oldState) . ' (' . $oldState . ') => ' : '')
                    . $helper->getTransactionStateLabel($newState) . ' (' . $newState . ')'
            )
        );
    }

    /**
     * @param string $transactionReference
     * @return Fianet_Payment3xcb_Model_Transaction_History|false
     */
    public function getLastPertinentTransactionHistory($transactionReference)
    {
        /** @var Fianet_Payment3xcb_Model_Resource_Transaction_History_Collection $collection */
        $collection = Mage::getModel('fianetpayment3xcb/transaction_history')
            ->getCollection()
            ->filterLastPertinentTransactionHistory($transactionReference);
        return $collection->fetchItem();
    }

    /**
     * @param string $orderIncrementId
     * @return Fianet_Payment3xcb_Model_Transaction_History|false
     */
    public function getLastOrderTransactionHistory($orderIncrementId)
    {
        /** @var Fianet_Payment3xcb_Model_Resource_Transaction_History_Collection $collection */
        $collection = Mage::getModel('fianetpayment3xcb/transaction_history')
            ->getCollection()
            ->filterLastOrderTransactionHistory($orderIncrementId);
        return $collection->fetchItem();
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->hasData('order')) {
            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order');
            $this->setData(
                'order',
                $order->loadByIncrementId($this->getOrderIncrementId())
            );
        }

        return $this->getData('order');
    }

    public function updateOrderState()
    {
        $transactionState = $this->getState();
        // If validating state, then create invoice if needed
        if ($transactionState == Fianet_Payment3xcb_Model_Source_Fianet_TransactionState::CONTRACT_ACCEPTED) {
            $this->createInvoice();
        }

        $order = $this->getOrder();
        switch ($transactionState) {
            case Fianet_Payment3xcb_Model_Source_Fianet_TransactionState::PAYMENT_ABORTED:
                $this->applyStateToOrder($transactionState);
                break;
            case Fianet_Payment3xcb_Model_Source_Fianet_TransactionState::PAYMENT_STORED:
                $this->applyStateToOrder($transactionState);
                if (Mage::getSingleton('checkout/session')->getQuoteId()) {
                    Mage::getSingleton('checkout/session')->unsQuoteId();
                }

                $order->sendNewOrderEmail();
                break;
            default:
                $this->applyStateToOrder($transactionState);
                break;
        }

        $eventData = array(
            'order' => $order,
            'transaction' => $this,
        );
        Mage::dispatchEvent('fianet3xcb_payment_' . strtolower($transactionState), $eventData);
    }

    protected function applyStateToOrder($transactionState)
    {
        $stateLabels = Mage::getSingleton('fianetpayment3xcb/source_fianet_transactionState')->toArray();
        $message = isset($stateLabels[$transactionState]) ? $stateLabels[$transactionState] : null;

        /** @var Fianet_Payment3xcb_Model_Mapping_Transaction_State $mapping */
        $mapping = Mage::getModel('fianetpayment3xcb/mapping_transaction_state')
            ->load($transactionState);
        $orderStatus = $mapping->getOrderStatus();
        $orderState = $this->getOrderState($orderStatus);
        $notifyCustomer = (bool) $mapping->getNotifyCustomer();
        $order = $this->getOrder();

        if ($orderState == Mage_Sales_Model_Order::STATE_CANCELED) {
            $order->cancel();
        } else {
            $this->logger()->debug(
                'order->setState: ' . $orderState . ', ' . $orderStatus . ', ' . $message . ', ' . $notifyCustomer
            );
            $order->setState($orderState, $orderStatus, $message, $notifyCustomer);
        }

        $order->save();
    }

    protected function getOrderState($orderStatus)
    {
        $orderStates = Mage::getConfig()->getNode(Mage_Sales_Model_Config::XML_PATH_ORDER_STATES)->asArray();
        foreach ($orderStates as $stateCode => $state) {
            if (isset($state['statuses'])
                && is_array($state['statuses'])
                && isset($state['statuses'][$orderStatus])
            ) {
                return $stateCode;
            }
        }

        return false;
    }

    protected function createInvoice()
    {
        $automaticInvoicing = Mage::getStoreConfig('payment/fianet3xcb/automatic_invoicing');

        $order = $this->getOrder();
        // TODO: auto-invoicing
        if (false && $order->canInvoice() && $automaticInvoicing == true) {
            $this->logger()->debug('order->prepareInvoice');
            $invoice = $order->prepareInvoice();
            $invoice->register();

            /** @var Mage_Core_Model_Resource_Transaction $transaction */
            $transaction = Mage::getModel('core/resource_transaction');
            $transaction->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        }
    }
}
