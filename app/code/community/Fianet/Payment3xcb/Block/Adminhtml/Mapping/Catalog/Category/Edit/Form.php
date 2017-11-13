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

class Fianet_Payment3xcb_Block_Adminhtml_Mapping_Catalog_Category_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save'),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $categoryId = (int) $this->getRequest()->getParam('id');
        if ($categoryId <= 0) {
            return parent::_prepareForm();
        }

        $fieldset = $form->addFieldset(
            'fianetpayment3xcb_form',
            array(
                'legend' => $this->__('Map Catalog Category'),
            )
        );

        $fieldset->addField(
            'storeId',
            'hidden',
            array(
                'required' => true,
                'name' => 'storeId',
                'value' => (int) $this->getRequest()
                    ->getParam('store'),
            )
        );

        $fieldset->addField(
            'category_id',
            'hidden',
            array(
                'required' => true,
                'name' => 'category_id',
                'value' => $categoryId,
            )
        );

        /** @var Fianet_Payment3xcb_Model_Mapping_Catalog_Category $mapping */
        $mapping = Mage::getModel('fianetpayment3xcb/mapping_catalog_category');
        /** @var Fianet_Payment3xcb_Model_Source_Fianet_ProductType $productTypeSource */
        $productTypeSource = Mage::getModel('fianetpayment3xcb/source_fianet_productType');
        $fieldset->addField(
            'fianet_product_type_id',
            'select',
            array(
                'label' =>$this->__('FIA-NET Category'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'fianet_product_type_id',
                'value' => $mapping->loadByCategoryId($categoryId)
                    ->getFianetProductTypeId(),
                'values' => $productTypeSource->toOptionArray(),
            )
        );

        $fieldset->addField(
            'apply_to_sub_categories',
            'checkbox',
            array(
                'label' => $this->__('Apply to sub-categories'),
                'name' => 'apply_to_sub_categories',
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
