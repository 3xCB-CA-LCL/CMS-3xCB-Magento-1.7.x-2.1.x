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

class Fianet_Payment3xcb_Helper_Transaction
{
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
     * Called from Magento Backend, sales order grid massaction
     * @param array $orderIds
     */
    public function manualUpdate($orderIds = array())
    {
        $this->logger()->setEvent(
            Mage::helper('fianetpayment3xcb')->__('Manual Update')
        );
        $this->logger()->debug(count($orderIds) . " items");
        /** @var Mage_Sales_Model_Order[] $collection */
        $collection = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('entity_id', array('in' => $orderIds));
        foreach ($collection as $order) {
            if ($order->getPayment()->getMethod() != 'fianet3xcb') {
                $this->logger()->notice(
                    Mage::helper('fianetpayment3xcb')->__(
                        'Order #%s - Invalid payment method: %s',
                        $order->getIncrementId(),
                        $order->getPayment()->getMethod()
                    )
                );
                continue;
            }

            $this->processOrder($order);
        }

        /** @var Mage_Adminhtml_Model_Session $adminhtmlSession */
        $adminhtmlSession = Mage::getSingleton('adminhtml/session');
        $adminhtmlSession->addSuccess(
            Mage::helper('fianetpayment3xcb')->__('%s order(s) have been processed.', count($orderIds))
        );
    }

    public function automaticUpdate()
    {
        $localeCode = Mage::getStoreConfig('general/locale/code');

        // Re-init locale to prevent/fix any wrong translations
        // cron files are run in 'admin' area and thus static blocks (and other stuff) is not properly translated
        Mage::app()->getTranslator()->setLocale($localeCode)->init(Mage_Core_Model_App_Area::AREA_FRONTEND, true);

        $this->logger()->setEvent(
            Mage::helper('fianetpayment3xcb')->__('Planified Update')
        );
        Mage::app('admin')->setUseSessionInUrl(false);

        /** @var Fianet_Payment3xcb_Model_Source_Fianet_TransactionState $source */
        $source = Mage::getModel('fianetpayment3xcb/source_fianet_transactionState');
        // Get orders that must not be updated
        $collection = Mage::getResourceModel('fianetpayment3xcb/transaction_history_collection')
            ->addFieldToSelect('order_increment_id')
            ->addFilterOnState($source->getFinalStates());

        $orderIncrementIds = array();
        foreach ($collection as $history) {
            /** @var Fianet_Payment3xcb_Model_Transaction_History $history */
            $orderIncrementIds[] = $history->getOrderIncrementId();
        }

        $orderCollection = Mage::getResourceModel('sales/order_collection');
        $orderCollection->addFieldToSelect('entity_id')
            ->addFieldToSelect('increment_id')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('store_id')
            ->addFieldToFilter('state', array('nin' => array('canceled', 'complete', 'holded', 'closed')))
            ->addFieldToFilter('increment_id', array('nin' => $orderIncrementIds))
            ->addFieldToSelect('fianet3xcb_state')
            ->getSelect()
            ->join(
                array(
                    'payment_method' => $orderCollection->getTable('sales/order_payment')
                ),
                "main_table.entity_id = payment_method.parent_id AND payment_method.method = 'fianet3xcb'",
                array()
            );
        $this->logger()->debug("{$orderCollection->getSize()} items");
        foreach ($orderCollection as $order) {
            $this->processOrder($order);
        }
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return null|string
     */
    protected function processOrder(Mage_Sales_Model_Order $order)
    {
        /** @var Fianet_Payment3xcb_Model_Fianet_Api $api */
        $api = Mage::getSingleton('fianetpayment3xcb/fianet_api');
        $response = $api->getTransaction($order->getIncrementId());
        if (!$response) {
            return null;
        }

        $this->logger()->debug($response->toString());

        switch ($response->getStatus()) {
            case 404:
                $this->logger()->warning(
                    Mage::helper('fianetpayment3xcb')->__('Order #%s - Transaction Not Found', $order->getIncrementId())
                );
                break;
            case 200:
                /** @var Fianet_Payment3xcb_Model_Transaction_History $history */
                $history = Mage::getModel('fianetpayment3xcb/transaction_history');
                $history = $history->loadFromResult($response->getResult());
                return $history->getState();
            default:
                $this->logger()->error($response->getMessage());
                break;
        }

        return null;
    }


    /**
     * @param Fianet_Payment3xcb_Model_Transaction_History $transactionOne
     * @param Fianet_Payment3xcb_Model_Transaction_History $transactionTwo
     * @return bool
     */
    public function areSame($transactionOne, $transactionTwo)
    {
        return !empty($transactionOne)
            && !empty($transactionTwo)
            && $transactionOne->getTop3reference() == $transactionTwo->getTop3reference()
            && $transactionOne->getAmount() == $transactionTwo->getAmount()
            && $transactionOne->getState() == $transactionTwo->getState()
            && $transactionOne->getMode() == $transactionTwo->getMode();
    }
}
