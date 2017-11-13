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

interface Fianet_Payment3xcb_Api_AddressInterface
{
    /**
     * @param string $street
     * @return Fianet_Payment3xcb_Api_AddressInterface
     */
    public function setStreet($street);

    /**
     * @param string $postcode
     * @return Fianet_Payment3xcb_Api_AddressInterface
     */
    public function setPostcode($postcode);

    /**
     * @param string $city
     * @return Fianet_Payment3xcb_Api_AddressInterface
     */
    public function setCity($city);

    /**
     * @param string $country
     * @return Fianet_Payment3xcb_Api_AddressInterface
     */
    public function setCountry($country);

    /**
     * @param Mage_Sales_Model_Order_Address $address
     * @return Fianet_Payment3xcb_Api_AddressInterface
     */
    public function copyFrom(Mage_Sales_Model_Order_Address $address);
}
