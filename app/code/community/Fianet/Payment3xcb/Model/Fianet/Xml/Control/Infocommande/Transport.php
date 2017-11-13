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

class Fianet_Payment3xcb_Model_Fianet_Xml_Control_Infocommande_Transport
    extends Fianet_Payment3xcb_Model_Fianet_Xml_Node
    implements Fianet_Payment3xcb_Api_ShippingInterface
{
    public function __construct()
    {
        parent::__construct(/** @lang XML */'<transport/>');
    }

    public function load()
    {
        $order = $this->getOrder();
        $shippingCarrier = $order->getShippingCarrier();

        if (!$shippingCarrier) {
            if (Mage::getStoreConfig('fianetpayment3xcb/config/allow_downloadable_products')) {
                $this->setType(self::TYPE_VIRTUAL_PRODUCTS)
                    ->setName(Mage::helper('fianetpayment3xcb')->__('Downloadable product and/or virtual product'))
                    ->setSpeed(self::SPEED_EXPRESS);
            }
        } else {
            $carrierCode = $shippingCarrier ? $shippingCarrier->getCarrierCode() : null;
            $methodCode = $order->getShippingMethod();
            if (substr($methodCode, 0, strlen($carrierCode) + 1) == $carrierCode . '_') {
                $methodCode = substr($methodCode, strlen($carrierCode) + 1);
            }

            /** @var Fianet_Payment3xcb_Model_Mapping_Shipping_Method $shippingMethodMapping */
            $shippingMethodMapping = Mage::getModel('fianetpayment3xcb/mapping_shipping_method');
            $shippingMethodMapping = $shippingMethodMapping->loadByCarrierCodeAndMethodCode($carrierCode, $methodCode);
        
            //initialisation du type de transport
            $type = $shippingMethodMapping->getFianetShippingTypeId();
            $this->setType($type);
            $this->setName($shippingMethodMapping->getFianetShippingName());
            $this->setSpeed($shippingMethodMapping->getFianetShippingSpeedId());

            if (class_exists('Fianet_Payment3xcb_Helper_Shipping_Alias' . ucfirst($carrierCode))) {
                /** @var Fianet_Payment3xcb_Helper_Shipping_Abstract $helper */
                $helper = Mage::helper('fianetpayment3xcb/shipping_alias' . ucfirst($carrierCode));
                $helper->populate($this, $order);
            } elseif (class_exists('Fianet_Payment3xcb_Helper_Shipping_' . ucfirst($carrierCode))) {
                /** @var Fianet_Payment3xcb_Helper_Shipping_Abstract $helper */
                $helper = Mage::helper('fianetpayment3xcb/shipping_' . $carrierCode);
                $helper->populate($this, $order);
            } elseif ($type == self::TYPE_PICKUP_AT_STORE) {
                $pointrelais = $this->addPickupPoint()
                    ->setName(Mage::getStoreConfig('general/store_information/name'));

                $info = $this->getHelper()->getStoreInformation();
                if ($info) {
                    $pointrelais->getAddress()
                        ->setStreet($info['store_address'])
                        ->setPostcode($info['store_postal_code'])
                        ->setCity($info['store_city'])
                        ->setCountry(preg_match('/^97/', $info['store_postal_code']) ? 'FR' : $info['store_country']);
                }
            }
        }

        return $this;
    }

    /**
     * @param int $type     Use constant TYPE_*
     * @return Fianet_Payment3xcb_Api_ShippingInterface
     */
    public function setType($type)
    {
        $this->getChild('type')
            ->setContent($type);
        return $this;
    }

    /**
     * @param string $name
     * @return Fianet_Payment3xcb_Api_ShippingInterface
     */
    public function setName($name)
    {
        $this->getChild('nom')
            ->setContent($name);
        return $this;
    }

    /**
     * @param int $speed    Use constant SPEED_*
     * @return Fianet_Payment3xcb_Api_ShippingInterface
     */
    public function setSpeed($speed)
    {
        $this->getChild('rapidite')
            ->setContent($speed);
        return $this;
    }

    /**
     * @return Fianet_Payment3xcb_Api_PickupPointInterface
     */
    public function addPickupPoint()
    {
        /** @var Fianet_Payment3xcb_Model_Fianet_Xml_Control_Infocommande_Transport_Pointrelais $node */
        $node = $this->appendChildNode('control_infocommande_transport_pointrelais')->init();
        return $node;
    }

    /**
     * @return Fianet_Payment3xcb_Api_CustomerInterface
     */
    public function getShippingCustomer()
    {
        /** @var Fianet_Payment3xcb_Model_Fianet_Xml_Control_Utilisateur_Livraison $node */
         $node = $this->getRootNode()
            ->getChildNode('control_utilisateur_livraison');
        return $node;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        if ($this->getValue('type') === null) {
            $this->reportError(
                Mage::helper('fianetpayment3xcb')->__('`type` is undefined')
            );
        }

        if ($this->getValue('nom') === null) {
            $this->reportError(
                Mage::helper('fianetpayment3xcb')->__('`nom` is undefined')
            );
        }

        if ($this->getValue('rapidite') === null) {
            $this->reportError(
                Mage::helper('fianetpayment3xcb')->__('`rapidite` is undefined')
            );
        }

        if ($pointrelais = $this->getChildNode('control_infocommande_transport_pointrelais')) {
            $pointrelais->validate();
        }

        return parent::validate();
    }
}
