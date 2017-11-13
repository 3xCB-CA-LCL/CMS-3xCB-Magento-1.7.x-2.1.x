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

class Fianet_Payment3xcb_Model_Payment_Method_Fianet3xcb extends Mage_Payment_Model_Method_Abstract
{
    protected static $isApiAvailable = null;

    protected $_code = 'fianet3xcb';
    protected $_formBlockType = 'fianetpayment3xcb/payment_form';
    protected $_infoBlockType = 'fianetpayment3xcb/payment_info';

    protected $_canUseInternal = true;
    protected $_canUseForMultishipping = false;

    public function __construct()
    {
        $this->_infoBlockType = 'fianetpayment3xcb/payment_info';
        $this->_canManageRecurringProfiles = false;
    }

    /**
     * @return Fianet_Payment3xcb_Model_Logger
     */
    protected function logger()
    {
        /** @var Fianet_Payment3xcb_Model_Logger $logger */
        $logger = Mage::getSingleton('fianetpayment3xcb/logger');
        return $logger;
    }

    /**
     * @param Mage_Sales_Model_Quote $quote | null
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        $storeId = $quote ? $quote->getStoreId() : null;
        if (!$this->getStoreConfig('payment/' . $this->_code . '/active', $storeId)) {
            return false;
        }

        if (!$quote || !($quote->getBaseGrandTotal() > 0)) {
            return false;
        }

        $availableFor = $this->getStoreConfig("payment/{$this->_code}/available_for", $storeId);
        if ($availableFor != Fianet_Payment3xcb_Model_Adminhtml_System_Config_Source_AvailableFor::ALL_CUSTOMERS) {
            $customerGroups = $this->getStoreConfig("payment/{$this->_code}/customer_groups", $storeId);
            $customerGroupIds = explode(',', $customerGroups);
            /** @var Mage_Customer_Model_Session $customerSession */
            $customerSession = Mage::getSingleton('customer/session');
            $currentCustomerGroupId = $customerSession->getCustomerGroupId();
            if (!$customerGroupIds || !in_array($currentCustomerGroupId, $customerGroupIds)) {
                return false;
            }
        }

        if (!$this->isApiAvailable($quote)) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    /**
     * @param Mage_Sales_Model_Quote $quote | null
     * @return bool
     */
    protected function isApiAvailable($quote)
    {
        if (!isset(static::$isApiAvailable)) {
            /** @var Fianet_Payment3xcb_Model_Fianet_Api $api */
            $api = Mage::getSingleton('fianetpayment3xcb/fianet_api');
            $response = $api->isAvailable($quote);

            if (empty($response)) {
                $isApiAvailable = false;
            } elseif ($response->getStatus() != 200) {
                $this->logger()->error($response->toString());
                $isApiAvailable = false;
            } else {
                $isApiAvailable = $this->extractApiAvailability($response->getResult());
            }

            static::$isApiAvailable = $isApiAvailable;
        }

        return static::$isApiAvailable;
    }

    /**
     * @param Fianet_Payment3xcb_Model_Fianet_Result $result
     * @return bool
     */
    protected function extractApiAvailability(Fianet_Payment3xcb_Model_Fianet_Result $result)
    {
        $code = $result->get('code');
        $label = Mage::helper('fianetpayment3xcb')->__('3xCB availability: %s', $result->get('libelle'));
        switch ($code) {
            case 'MERCHANT_IS_INACTIVE':
            case 'MERCHANT_BALANCE_TOO_HIGH':
                $this->logger()->warning($label);
                return false;
            case 'COMMAND_AMOUNT_TOO_LOW':
            case 'COMMAND_AMOUNT_TOO_HIGH':
            case 'INVALID_COUNTRY':
                $this->logger()->debug($label);
                return false;
            case 'OK':
                $this->logger()->debug($label);
                return true;
            default:
                $this->logger()->error(
                    Mage::helper('fianetpayment3xcb')->__('Unknown 3xCB availability code: `%s`', $code)
                );
                return false;
        }
    }

    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('fianetpayment3xcb/payment/redirect');
    }

    protected function getStoreConfig($path, $storeId = null)
    {
        $storeId = $storeId ? $storeId : Mage::app()->getStore()->getId();
        return Mage::getStoreConfig($path, $storeId);
    }

    /**
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        /** @var Mage_Checkout_Model_Session $session */
        $session = Mage::getSingleton('checkout/session');
        return $session;
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckoutSession()->getQuote();
    }

    /**
     * @return Fianet_Payment3xcb_Model_Fianet_Xml_Control
     */
    public function getFianetXml()
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($this->getCheckoutSession()->getLastRealOrderId());
        /** @var Fianet_Payment3xcb_Model_Fianet_Xml_Control $xml */
        $xml = Mage::getModel('fianetpayment3xcb/fianet_xml_control');
        return $xml->setOrder($order)
            ->load();
    }
}
