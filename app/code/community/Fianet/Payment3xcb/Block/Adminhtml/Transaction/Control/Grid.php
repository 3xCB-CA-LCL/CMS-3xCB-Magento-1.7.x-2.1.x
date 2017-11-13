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

class Fianet_Payment3xcb_Block_Adminhtml_Transaction_Control_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('fianetpayment3xcb_adminhtml_transaction_control');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
        $collection = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter(
                'fianet3xcb_state',
                array(
                    'in' => array(
                        Fianet_Payment3xcb_Model_Source_Fianet_TransactionState::CONTRACT_ACCEPTED,
                        Fianet_Payment3xcb_Model_Source_Fianet_TransactionState::VALIDATION_ASKED,
                        Fianet_Payment3xcb_Model_Source_Fianet_TransactionState::PARTIAL_VALIDATION_ASKED,
                        Fianet_Payment3xcb_Model_Source_Fianet_TransactionState::CANCELLATION_ASKED,
                    ),
                )
            )
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('fianet3xcb_state')
            ->addAttributeToSelect('fianet3xcb_mode')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('order_currency_code')
            ->addAttributeToSelect('grand_total');

        if (Mage::registry('store_id_payment')) {
            $collection = $collection->addAttributeToSelect('store_id')
                ->addAttributeToFilter('store_id', array('eq' => Mage::registry('store_id_payment')));
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'real_order_id',
            array(
                'header' => $this->__('Order #'),
                'width' => 50,
                'type' => 'text',
                'index' => 'increment_id',
            )
        );
        $this->addColumn(
            'last_tag',
            array(
                'header' => $this->__('Last State'),
                'type' => 'text',
                'index' => 'fianet3xcb_state',
                'renderer' => 'fianetpayment3xcb/adminhtml_widget_grid_column_renderer_transactionState',
                'filter' => 'fianetpayment3xcb/adminhtml_widget_grid_column_filter_transactionState',
            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => $this->__('Purchased On'),
                'width' => 150,
                'index' => 'created_at',
                'type' => 'datetime',
            )
        );
        $this->addColumn(
            'mode',
            array(
                'header' => $this->__('Mode'),
                'width' => 50,
                'type' => 'text',
                'index' => 'fianet3xcb_mode',
            )
        );
        $this->addColumn(
            'grand_total',
            array(
                'header' => $this->__('G.T. (Purchased)'),
                'width' => 100,
                'index' => 'grand_total',
                'type' => 'currency',
                'currency' => 'order_currency_code',
            )
        );

        /** @var Mage_Adminhtml_Helper_Data $helper */
        $helper = Mage::helper('adminhtml');
        $backParam = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $backUrl = $helper->urlEncode($helper->getUrl('*/*/'));
        $urlParams = array($backParam => $backUrl, 'order_id' => $this->getRequest()->getParam('order_id'));

        $this->addColumn(
            'validate',
            array(
                'header' => '',
                'width' => 70,
                'align' => 'center',
                'frame_callback' => array($this, 'decorateRow'),
                'type' => 'action',
                'getter' => 'getEntityId',
                'actions' => array(
                    array(
                        'confirm' => $this->__('Are you sure you want to validate the 3xCB transaction?'),
                        'caption' => $this->__('Validate'),
                        'url' => array(
                            'base' => '*/*/validate/',
                            'params' => $urlParams,
                        ),
                        'field' => 'order_id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
            )
        );

        $this->addColumn(
            'partially_validate_form',
            array(
                'header' => '',
                'width' => 150,
                'align' => 'center',
                'frame_callback' => array($this, 'decorateRow'),
                'type' => 'action',
                'getter' => 'getEntityId',
                'actions' => array(
                    array(
                        'caption' => $this->__('Partially Validate'),
                        'url' => array(
                            'base' => '*/*/partiallyvalidateform/',
                            'params' => $urlParams,
                        ),
                        'field' => 'order_id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
            )
        );

        $this->addColumn(
            'cancel',
            array(
                'header' => '',
                'width' => 70,
                'align' => 'center',
                'frame_callback' => array($this, 'decorateRow'),
                'type' => 'action',
                'getter' => 'getEntityId',
                'actions' => array(
                    array(
                        'confirm' => $this->__('Are you sure you want to cancel the 3xCB transaction?'),
                        'caption' => $this->__('Cancel'),
                        'url' => array(
                            'base' => '*/*/cancel/',
                            'params' => $urlParams,
                        ),
                        'field' => 'order_id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * @param string $html
     * @param Mage_Sales_Model_Order $row
     * @return string
     */
    public function decorateRow($html, $row)
    {
        return $row->getFianet3xcbState() != Fianet_Payment3xcb_Model_Source_Fianet_TransactionState::CONTRACT_ACCEPTED
            ? '' : $html;
    }

    /**
     * @param $row
     * @return bool
     */
    public function getRowUrl($row)
    {
        return false;
    }
}
