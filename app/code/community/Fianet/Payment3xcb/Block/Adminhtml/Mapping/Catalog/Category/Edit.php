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

class Fianet_Payment3xcb_Block_Adminhtml_Mapping_Catalog_Category_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_mapping_catalog_category';
        $this->_mode = 'edit';
        $this->_blockGroup = 'fianetpayment3xcb';

        $this->_updateButton('save', 'label', $this->__('Save'));
        $this->_updateButton('save', 'url', $this->getFormActionUrl());

        $categoryId = (int) $this->getRequest()->getParam('id');

        if ($categoryId > 0) {
            /** @var Fianet_Payment3xcb_Model_Mapping_Catalog_Category $mapping */
            $mapping = Mage::getModel('fianetpayment3xcb/mapping_catalog_category');
            $mapping->loadByCategoryId($categoryId);
            if ($mapping->getId() == 0) {
                $this->_removeButton('delete');
            }
        } else {
            $this->_removeButton('save');
        }

        $this->_removeButton('reset');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        $categoryId = (int) $this->getRequest()->getParam('id');

        if ($categoryId <= 0) {
            return $this->__('Catalog Category to FIA-NET Category Mapping');
        }

        /** @var Mage_Catalog_Model_Category $category */
        $category = Mage::getModel('catalog/category')
            ->load($categoryId);
        return $this->__("\"%\" Catalog Category to FIA-NET Category Mapping", $category->getName());
    }
}
