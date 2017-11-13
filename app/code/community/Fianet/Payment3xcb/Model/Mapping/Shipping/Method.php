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

/**
 * Class Fianet_Payment3xcb_Model_Mapping_Shipping_Method
 *
 * @method string getCarrierCode()
 * @method string getFianetShippingName()
 * @method int getFianetShippingTypeId()
 * @method int getFianetShippingSpeedId()
 * @method string getMethodCode()
 * @method Fianet_Payment3xcb_Model_Mapping_Shipping_Method setCarrierCode(string $carrierCode)
 * @method Fianet_Payment3xcb_Model_Mapping_Shipping_Method setFianetShippingName(string $shippingName)
 * @method Fianet_Payment3xcb_Model_Mapping_Shipping_Method setFianetShippingTypeId(int $typeId)
 * @method Fianet_Payment3xcb_Model_Mapping_Shipping_Method setFianetShippingSpeedId(int $speedId)
 * @method Fianet_Payment3xcb_Model_Mapping_Shipping_Method setMethodCode(string $methodCode)
 */
class Fianet_Payment3xcb_Model_Mapping_Shipping_Method extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('fianetpayment3xcb/mapping_shipping_method');
    }

    /**
     * @param string $carrierCode
     * @param string $methodCode
     * @return Fianet_Payment3xcb_Model_Mapping_Shipping_Method
     */
    public function loadByCarrierCodeAndMethodCode($carrierCode, $methodCode)
    {
        $item = $this->getCollection()
            ->addFieldToFilter('carrier_code', $carrierCode)
            ->addFieldToFilter('method_code', $methodCode)
            ->fetchItem();
        if (empty($item)) {
            $item = Mage::getModel('fianetpayment3xcb/mapping_shipping_method');
        }

        return $item;
    }

    public function getAllShippingMethods()
    {
        /** @var Mage_Shipping_Model_Config $config */
        $config = Mage::getSingleton('shipping/config');
        /** @var Mage_Shipping_Model_Carrier_Interface[] $methods */
        $methods = $config->getActiveCarriers();

        $result = array();
        foreach ($methods as $carrierCode => $carrier) {
            $methods = $carrier->getAllowedMethods();
            if (!$methods) continue;

            if (!$carrierTitle = Mage::getStoreConfig("carriers/$carrierCode/title")) {
                $carrierTitle = $carrierCode;
            }

            $availableMethods = array();
            foreach ($methods as $methodCode => $methodLabel) {
                $availableMethods[] = (object) array(
                    'carrierCode' => $carrierCode,
                    'carrierTitle' => $carrierTitle,
                    'code' => $methodCode,
                    'label' => $methodLabel,
                    'disabled' => $carrierCode == 'socolissimosimplicite',
                );
            }

            $result[] = (object) array(
                'code' => $carrierCode,
                'title' => $carrierTitle,
                'methods' => $availableMethods,
            );
        }

        return $result;
    }
}
