<?php

/**
 * Remplissage des informations sur le point relais depuis un module tiers pour Fianet_Payment3xcb
 *
 * Dans cet exemple, le code transporteur (carrier_code) est 'exemple', il va définir :
 * - le nom de la classe : Fianet_Payment3xcb_Helper_Shipping_Exemple
 * - l'emplacement du fichier : app/code/community/Fianet/Payment3xcb/Helper/Shipping/Exemple.php
 *
 * @ModuleName                   Exemple_Pointrelais
 * @ShippingMethod.carrier_code  exemple
 * @ShippingMethod.method_code   pointrelais
 */

class Fianet_Payment3xcb_Helper_Shipping_Exemple extends Fianet_Payment3xcb_Helper_Shipping_Abstract
{
    /**
     * Remplissage des informations depuis un module tiers
     *
     * @param Fianet_Payment3xcb_Api_ShippingInterface $shipping
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    public function populate(Fianet_Payment3xcb_Api_ShippingInterface $shipping, Mage_Sales_Model_Order $order)
    {
        // Vérification de la méthode de livraison
        if ($order->getShippingMethod() !== 'exemple_pointrelais') {
            return false;
        }

        // Type de livraison
        // - Fianet_Payment3xcb_Api_ShippingInterface::TYPE_PICKUP_AT_STORE
        // - Fianet_Payment3xcb_Api_ShippingInterface::TYPE_PICKUP_NETWORK
        // - Fianet_Payment3xcb_Api_ShippingInterface::TYPE_PICKUP_AT_AIRPORT_TRAIN_STATION_OR_TRAVEL_AGENCY
        // - Fianet_Payment3xcb_Api_ShippingInterface::TYPE_CARRIER
        // - Fianet_Payment3xcb_Api_ShippingInterface::TYPE_VIRTUAL_PRODUCTS
        $shipping->setType(Fianet_Payment3xcb_Api_ShippingInterface::TYPE_PICKUP_NETWORK);

        // Rapidité de livraison
        // - Fianet_Payment3xcb_Api_ShippingInterface::SPEED_STANDARD
        // - Fianet_Payment3xcb_Api_ShippingInterface::SPEED_EXPRESS
        $shipping->setSpeed(Fianet_Payment3xcb_Api_ShippingInterface::SPEED_STANDARD);

        // Nom du transporteur
        $shipping->setName('Montransporteur');

        // Création du point relais
        $pointrelais = $shipping->addPickupPoint();

        // Identifiant du point relais
        $pointrelais->setId('...');

        // Nom de l'enseigne du point relais
        $pointrelais->setName('...');

        // Adresse du point relais
        $pointrelais->getAddress()
            ->setStreet('...')
            ->setPostcode('...')
            ->setCity('...')
            ->setCountry('...'); // Code pays (ex: FR)

        /*
        // Autre possibilité : copie de l'adresse de livraison si les informations ont été stockées dedans
        $shippingAddress = $order->getShippingAddress();
        $pointrelais->getAddress()
            ->copyFrom($shippingAddress);
        */

        // Téléphone portable
        $shipping->getShippingCustomer()
            ->setMobilePhone('...');

        return true;
    }
}
