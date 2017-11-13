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

class Fianet_Payment3xcb_Model_Observer extends Varien_Event_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function updateSalesOrderGrid(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $block = $event->getData('block');

        if (preg_match('/sales_order.grid.child[0-9]+/', $block->getNameInLayout())
            && (
                $block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract
                || is_subclass_of($block, 'Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Massaction', false)
            )
        ) {
            $block->addItem(
                'fianet3xcb_transaction_state',
                array(
                    'label' => Mage::helper('fianetpayment3xcb')->__("FIA-NET 3xCB: Get Transactions' State"),
                    'url' => Mage::getUrl('fianetpayment3xcb/adminhtml_order/getTransactionState')
                )
            );
        }
    }

    /**
     * @param string $action
     * @param Mage_Sales_Model_Order $order
     * @return mixed
     */
    protected function getMerchantActionUrl($action, Mage_Sales_Model_Order $order)
    {
        /** @var Mage_Adminhtml_Helper_Data $helper */
        $helper = Mage::helper('adminhtml');
        $orderId = $order->getId();
        $backUrl = $helper->getUrl('adminhtml/sales_order/view', array('order_id' => $orderId));
        return $helper->getUrl(
            'fianetpayment3xcb/adminhtml_transaction_control/' . $action,
            array(
                'order_id' => $orderId,
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $helper->urlEncode($backUrl),
            )
        );
    }

    /**
     * @param Mage_Adminhtml_Block_Widget_Form_Container $block
     * @param Mage_Sales_Model_Order $order
     * @param $action
     * @param $label
     * @param $confirmationMessage
     * @param $class
     */
    protected function addOrderButton($block, $order, $action, $label, $confirmationMessage, $class)
    {
        $url = $this->getMerchantActionUrl($action, $order);
        $confirmationMessage = Mage::helper('core')->jsQuoteEscape($confirmationMessage);
        $block->addButton(
            'fianetpayment3xcb_' . $action,
            array(
                'label'     => $label,
                'onclick'   => "confirmSetLocation('{$confirmationMessage}', '{$url}')",
                'class'     => $class,
            )
        );
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function addSalesOrderViewButtons(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Widget_Form_Container $block */
        $block = Mage::app()->getLayout()->getBlock('sales_order_edit');
        if (!$block) {
            return;
        }

        $order = Mage::registry('current_order');
        /** @var Mage_Sales_Model_Order_Payment $payment */
        $payment = $order ? $order->getPayment() : null;
        $method = $payment ? $payment->getMethod() : null;
        if ($method != 'fianet3xcb') {
            return;
        }

        /** @var Fianet_Payment3xcb_Model_Transaction_History $history */
        $history = Mage::getModel('fianetpayment3xcb/transaction_history');
        $history = $history->getLastOrderTransactionHistory($order->getIncrementId());
        $contractAccepted = Fianet_Payment3xcb_Model_Source_Fianet_TransactionState::CONTRACT_ACCEPTED;
        if (!$history || $history->getState() != $contractAccepted) {
            return;
        }

        $helper = Mage::helper('sales');

        $this->addOrderButton(
            $block,
            $order,
            'validate',
            $helper->__('3xCB: Validate'),
            $helper->__('Are you sure you want to validate the 3xCB transaction?'),
            'save'
        );

        $this->addOrderButton(
            $block,
            $order,
            'partiallyValidateForm',
            $helper->__('3xCB: Partially Validate'),
            $helper->__('Are you sure you want to partially validate the 3xCB transaction?'),
            'save'
        );

        $this->addOrderButton(
            $block,
            $order,
            'cancel',
            $helper->__('3xCB: Cancel'),
            $helper->__('Are you sure you want to cancel the 3xCB transaction?'),
            'delete'
        );
    }
}
