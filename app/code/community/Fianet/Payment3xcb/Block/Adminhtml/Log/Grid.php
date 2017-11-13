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

class Fianet_Payment3xcb_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('fianetpayment3xcb_adminhtml_log');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('fianetpayment3xcb/log')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _setCollectionOrder($column)
    {
        $result = parent::_setCollectionOrder($column);
        $collection = $this->getCollection();
        if ($collection) {
            $columnIndex = $column->getFilterIndex() ?
                $column->getFilterIndex() : $column->getIndex();
            // When sorting by created_at, add a second sort on id
            if ($columnIndex == 'created_at' || $columnIndex == 'level') {
                $collection->setOrder('id', strtoupper($column->getDir()));
            }
        }

        return $result;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('fianetpayment3xcb')->__('Created At'),
                'align' => 'left',
                'index' => 'created_at',
                'type' => 'datetime',
                'width' => '150px',
            )
        );

        /** @var Fianet_Payment3xcb_Model_Logger $logger */
        $logger = Mage::getSingleton('fianetpayment3xcb/logger');
        $this->addColumn(
            'level',
            array(
                'header' => Mage::helper('fianetpayment3xcb')->__('Severity'),
                'align' => 'center',
                'index' => 'level',
                'type' => 'options',
                'options' => $logger->getLevels(),
                'width' => '100px',
            )
        );

        $this->addColumn(
            'event',
            array(
                'header' => Mage::helper('fianetpayment3xcb')->__('Event'),
                'align' => 'left',
                'index' => 'event',
                'width' => '200px',
            )
        );

        $this->addColumn(
            'message',
            array(
                'header' => Mage::helper('fianetpayment3xcb')->__('Message'),
                'align' => 'left',
                'index' => 'message',
                'column_css_class' => 'message-cell',
            )
        );

        $this->addExportType('*/*/exportCsv', Mage::helper('fianetpayment3xcb')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('fianetpayment3xcb')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('log_ids');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => Mage::helper('fianetpayment3xcb')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('fianetpayment3xcb')->__('Are you sure?')
            )
        );

        return $this;
    }

    public function getRowUrl($row)
    {
        return false;
    }
}
