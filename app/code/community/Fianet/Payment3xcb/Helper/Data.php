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

class Fianet_Payment3xcb_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @param $input
     * @return string
     */
    public static function initials($input)
    {
        $input = trim($input);
        $init = strtoupper($input[0]);
        for ($i = 1, $n = strlen($input); $i < $n; $i++) {
            if ($input[$i - 1] == ' ' && $input[$i] != ' ') {
                $init .= strtoupper($input[$i]);
            }
        }

        return $init;
    }

    /**
     * @param string $countryId
     * @return mixed
     */
    public function getCountryIso3Code($countryId)
    {
        /** @var Mage_Directory_Model_Country $country */
        $country = Mage::getModel('directory/country');
        $country->loadByCode($countryId);
        return $country->getIso3Code();
    }

    /**
     * @param string $path
     * @return mixed
     */
    protected function getStoreConfig($path)
    {
        $storeId = Mage::app()->getStore()->getId();
        return Mage::getStoreConfig($path, $storeId);
    }

    /**
     * @return array|bool
     */
    public function getStoreInformation()
    {
        /** @var Fianet_Payment3xcb_Model_Resource_Store_Information_Collection $collection */
        $collection = Mage::getModel('fianetpayment3xcb/store_information')->getCollection()->load();
        $storeInformation = array();
        foreach ($collection as $information) {
            $key = $information->getInformation();
            $value = $information->getValue();
            if (!$value) {
                $storeInformation = false;
                break;
            } else {
                $storeInformation[$key] = $value;
            }
        }

        return $storeInformation;
    }

    /**
     * Returns transaction state label
     *
     * @param string $state
     * @return string
     */
    public function getTransactionStateLabel($state)
    {
        /** @var Fianet_Payment3xcb_Model_Source_Fianet_TransactionState $source */
        $source = Mage::getSingleton('fianetpayment3xcb/source_fianet_transactionState');
        return $source->getLabel($state);
    }

    /**
     * Returns Merchant Reference in 3xCB's configuration
     *
     * @return string
     */
    public function getMerchantReference()
    {
        return $this->getStoreConfig('payment/fianet3xcb/merchant_reference');
    }

    /**
     * Returns 3xCB payment mode
     *
     * @return string
     */
    public function getPaymentMode()
    {
        return $this->getStoreConfig('payment/fianet3xcb/test')
            ? Fianet_Payment3xcb_Model_Source_Fianet_Mode::TEST
            : Fianet_Payment3xcb_Model_Source_Fianet_Mode::PRODUCTION;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    public function hasSameAddresses(Mage_Sales_Model_Order $order)
    {
        $billing = $order->getBillingAddress()->getData();
        $shipping = $order->getShippingAddress()->getData();
        $diff = array_diff($billing, $shipping);
        unset($diff['entity_id']);
        unset($diff['address_type']);
        return count($diff) == 0;
    }
}
