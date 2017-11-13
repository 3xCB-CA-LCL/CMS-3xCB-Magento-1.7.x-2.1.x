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

interface Fianet_Payment3xcb_Api_ShippingInterface
{
    const TYPE_PICKUP_AT_STORE = 1;
    const TYPE_PICKUP_NETWORK = 2;
    const TYPE_PICKUP_AT_AIRPORT_TRAIN_STATION_OR_TRAVEL_AGENCY = 3;
    const TYPE_CARRIER = 4;
    const TYPE_VIRTUAL_PRODUCTS = 5;

    const SPEED_EXPRESS = 1;
    const SPEED_STANDARD = 2;

    /**
     * @param int $type     Use constant TYPE_*
     * @return Fianet_Payment3xcb_Api_ShippingInterface
     */
    public function setType($type);

    /**
     * @param string $name
     * @return Fianet_Payment3xcb_Api_ShippingInterface
     */
    public function setName($name);

    /**
     * @param int $speed    Use constant SPEED_*
     * @return Fianet_Payment3xcb_Api_ShippingInterface
     */
    public function setSpeed($speed);

    /**
     * @return Fianet_Payment3xcb_Api_PickupPointInterface
     */
    public function addPickupPoint();

    /**
     * @return Fianet_Payment3xcb_Api_CustomerInterface
     */
    public function getShippingCustomer();
}
