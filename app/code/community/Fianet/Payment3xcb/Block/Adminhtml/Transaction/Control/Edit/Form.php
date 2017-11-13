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

class Fianet_Payment3xcb_Block_Adminhtml_Transaction_Control_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('edit_form');
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/partiallyvalidate', array('order_id' => $orderId)),
                'method' => 'post',
            )
        );

        $backParam = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $form->addField(
            $backParam,
            'hidden',
            array(
                'name' => $backParam,
                'value' => $this->getRequest()->getParam($backParam),
            )
        );

        /** @var Fianet_Payment3xcb_Helper_Data $helper */
        $helper = Mage::helper('fianetpayment3xcb');

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);

        $fldInfo = $form->addFieldset('main', array('legend' => $helper->__('Amount')));

        $fldInfo->addField(
            'order_increment_id',
            'text',
            array(
                'label' => $helper->__('Order #'),
                'class' => 'text',
                'name' => 'order_increment_id',
                'value' => $order->getIncrementId(),
                'disabled' => true,
            )
        );

        $fldInfo->addField(
            'amount',
            'text',
            array(
                'label' => $helper->__('Amount'),
                'class' => 'validate-number',
                'name' => 'amount',
                'required' => true,
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
