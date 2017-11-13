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

class Fianet_Payment3xcb_Adminhtml_Sales_OrderController extends Fianet_Payment3xcb_Controller_Adminhtml_Action
{
    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Sales');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->getAdminSession()
            ->isAllowed('admin/sales/order/actions/view');
    }

    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order | false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }

    /**
     * Generate transactions grid for ajax request
     */
    public function transactionHistoryAction()
    {
        $this->_initOrder();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('fianetpayment3xcb/adminhtml_sales_order_view_tab_transactionHistory')
                ->toHtml()
        );
    }
}
