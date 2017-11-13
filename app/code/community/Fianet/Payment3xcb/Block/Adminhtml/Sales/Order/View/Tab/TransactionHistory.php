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

class Fianet_Payment3xcb_Block_Adminhtml_Sales_Order_View_Tab_TransactionHistory
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('fianetpayment3xcb_adminhtml_sales_order_view_tab_transaction_history');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('fianetpayment3xcb/transaction_history')->getCollection()
            ->addFilterOnOrderIncrementId($this->getOrder()->getIncrementId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'top3reference',
            array(
                'header' => $this->__('Transaction #'),
                'index' => 'top3reference',
                'width' => 200,
            )
        );

        $this->addColumn(
            'state',
            array(
                'header' => $this->__('State'),
                'index' => 'state',
                'renderoptions' => array('mode' => 'full'),
                'renderer' => 'fianetpayment3xcb/adminhtml_widget_grid_column_renderer_transactionState',
                'filter' => 'fianetpayment3xcb/adminhtml_widget_grid_column_filter_transactionState',
            )
        );

        $this->addColumn(
            'amount',
            array(
                'header' => $this->__('Amount'),
                'index' => 'amount',
                'type' => 'price',
                'width' => 100,
                'currency_code' => $this->getOrder()->getOrderCurrencyCode(),
            )
        );

        $this->addColumn(
            'mode',
            array(
                'header' => $this->__('Mode'),
                'index' => 'mode',
                'type' => 'options',
                'align' => 'center',
                'width' => 100,
                'options' => Mage::getModel('fianetpayment3xcb/source_fianet_mode')->toArray(),
            )
        );

        $this->addColumn(
            'created_at',
            array(
                'header' => $this->__('Created At'),
                'index' => 'created_at',
                'type' => 'date',
                'width' => 150,
                'align' => 'left',
                'format' => 'd MMM yyyy H:mm:ss',
            )
        );

        $this->addColumn(
            'last_refresh',
            array(
                'header' => $this->__('Last Refresh'),
                'index' => 'last_refresh',
                'type' => 'date',
                'width' => 150,
                'align' => 'left',
                'format' => 'd MMM yyyy H:mm:ss',
            )
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('fianetpayment3xcb/adminhtml_sales_order/transactionHistory', array('_current' => true));
    }

    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getTabLabel()
    {
        return $this->__("FIA-NET 3xCB Transaction");
    }

    public function getTabTitle()
    {
        return $this->__("FIA-NET 3xCB Transaction");
    }

    public function canShowTab()
    {
        $code = $this->getOrder()->getPayment()->getMethodInstance()->getCode();
        return $code == 'fianet3xcb';
    }

    public function isHidden()
    {
        return false;
    }

    public function getRowUrl($row)
    {
        return false;
    }
}
