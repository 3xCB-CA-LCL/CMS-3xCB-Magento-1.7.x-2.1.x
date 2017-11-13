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

class Fianet_Payment3xcb_Model_Fianet_Xml_Control_Adresse extends Fianet_Payment3xcb_Model_Fianet_Xml_Node
    implements Fianet_Payment3xcb_Api_AddressInterface
{
    public function __construct()
    {
        parent::__construct(/** @lang XML */'<adresse/>');
    }

    /**
     * @param string $street
     * @return Fianet_Payment3xcb_Api_AddressInterface
     */
    public function setStreet($street)
    {
        $this->getChild('rue1')
            ->setContent($street);
        return $this;
    }

    /**
     * @param string $postcode
     * @return Fianet_Payment3xcb_Api_AddressInterface
     */
    public function setPostcode($postcode)
    {
        $this->getChild('cpostal')
            ->setContent($postcode);
        return $this;
    }

    /**
     * @param string $city
     * @return Fianet_Payment3xcb_Api_AddressInterface
     */
    public function setCity($city)
    {
        $this->getChild('ville')
            ->setContent($city);
        return $this;
    }

    /**
     * @param string $country
     * @return Fianet_Payment3xcb_Api_AddressInterface
     */
    public function setCountry($country)
    {
        $this->getChild('pays')
            ->setContent($country);
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Order_Address $address
     * @return Fianet_Payment3xcb_Api_AddressInterface
     */
    public function copyFrom(Mage_Sales_Model_Order_Address $address)
    {
        $this->setStreet($address->getStreet(1));

        if ($value = $address->getStreet(2)) {
            $this->getChild('rue2')
                ->setContent($value);
        }

        $this->setPostcode($address->getPostcode());
        $this->setCity($address->getCity());
        $this->setCountry($address->getCountry());

        return $this;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        if ($this->getValue('rue1') === null) {
            $this->reportError(
                Mage::helper('fianetpayment3xcb')->__('`rue1` is undefined')
            );
        }

        if ($this->getValue('cpostal') === null) {
            $this->reportError(
                Mage::helper('fianetpayment3xcb')->__('`cpostal` is undefined')
            );
        }

        if ($this->getValue('ville') === null) {
            $this->reportError(
                Mage::helper('fianetpayment3xcb')->__('`ville` is undefined')
            );
        }

        if ($this->getValue('pays') === null) {
            $this->reportError(
                Mage::helper('fianetpayment3xcb')->__('`pays` is undefined')
            );
        }

        return parent::validate();
    }
}
