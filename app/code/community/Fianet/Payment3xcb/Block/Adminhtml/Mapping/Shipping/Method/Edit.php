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

class Fianet_Payment3xcb_Block_Adminhtml_Mapping_Shipping_Method_Edit extends Mage_Adminhtml_Block_Template
{
    /**
     * @return Fianet_Payment3xcb_Model_Mapping_Shipping_Method
     */
    public function getMappingModel()
    {
        /** @var Fianet_Payment3xcb_Model_Mapping_Shipping_Method $mapping */
        $mapping = Mage::getModel('fianetpayment3xcb/mapping_shipping_method');
        return $mapping;
    }

    /**
     * @return Fianet_Payment3xcb_Model_Mapping_Shipping_Method[]
     */
    public function getAllShippingMethods()
    {
        return $this->getMappingModel()
            ->getAllShippingMethods();
    }

    /**
     * @param object $method
     * @return Fianet_Payment3xcb_Model_Mapping_Shipping_Method
     */
    public function getShippingMethodMapping($method)
    {
        return $this->getMappingModel()
            ->loadByCarrierCodeAndMethodCode($method->carrierCode, $method->code);
    }

    /**
     * @return array
     */
    public function getShippingTypeOptionArray()
    {
        /** @var Fianet_Payment3xcb_Model_Source_Fianet_ShippingType $source */
        $source = Mage::getModel('fianetpayment3xcb/source_fianet_shippingType');
        return $source->toOptionArray();
    }

    /**
     * @return array
     */
    public function getShippingSpeedOptionArray()
    {
        /** @var Fianet_Payment3xcb_Model_Source_Fianet_ShippingSpeed $source */
        $source = Mage::getModel('fianetpayment3xcb/source_fianet_shippingSpeed');
        return $source->toOptionArray();
    }
}
