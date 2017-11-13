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

class Fianet_Payment3xcb_Model_Fianet_Xml_Control extends Fianet_Payment3xcb_Model_Fianet_Xml_Node
{
    public function __construct()
    {
        parent::__construct(/** @lang XML */'<?xml version="1.0" encoding="UTF-8" standalone="yes"?><control/>');
        $this->setRootNode($this);
    }

    /**
     * @return $this
     */
    public function load()
    {
        $order = $this->getOrder();

        $this->appendChildNode('control_utilisateur_facturation')->init();
        $this->appendChildNode('control_adresse_facturation')->init();

        $this->appendChildNode('control_utilisateur_livraison')->init();
        $this->appendChildNode('control_adresse_livraison')->init();

        $this->appendChildNode('control_infocommande')->init();

        $sameAddresses = $this->getHelper()->hasSameAddresses($order);
        if ($sameAddresses) {
            $element = $this->getElement()->xpath('utilisateur[@type="livraison"]');
            unset($element[0][0]);
        }

        if ($sameAddresses
            || $this->getValue('infocommande/transport/type') != Fianet_Payment3xcb_Api_ShippingInterface::TYPE_CARRIER
        ) {
            $element = $this->getElement()->xpath('adresse[@type="livraison"]');
            unset($element[0][0]);
        }

        $this->appendChildNode('control_top3')->init();

        return $this;
    }
}
