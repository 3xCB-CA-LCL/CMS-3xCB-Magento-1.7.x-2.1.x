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

class Fianet_Payment3xcb_Helper_Shipping_Colissimo extends Fianet_Payment3xcb_Helper_Shipping_Abstract
{
    /**
     * @module_name      LaPoste_Colissimo
     * @module_author    Magentix (www.magentix.fr)
     * @module_version   1.0.0 (2016)
     * @carrier_code     pickup
     *
     * Populate data from external module
     *
     * @param Fianet_Payment3xcb_Api_ShippingInterface $shipping
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    public function populate(Fianet_Payment3xcb_Api_ShippingInterface $shipping, Mage_Sales_Model_Order $order)
    {
        // Check order shipping_method
        if ($order->getShippingMethod() !== 'pickup_colissimo') {
            return false;
        }

        $shipping->setType(Fianet_Payment3xcb_Api_ShippingInterface::TYPE_PICKUP_NETWORK);
        $shipping->setSpeed(Fianet_Payment3xcb_Api_ShippingInterface::SPEED_STANDARD);
        $shipping->setName('Colissimo');

        $shippingAddress = $order->getShippingAddress();

        $shipping->addPickupPoint()
            ->setId($shippingAddress->getColissimoPickupId())
            ->setName($shippingAddress->getCompany())
            ->getAddress()
                ->copyFrom($shippingAddress);

        $shipping->getShippingCustomer()
            ->setMobilePhone($shippingAddress->getTelephone());

        return true;
    }
}
