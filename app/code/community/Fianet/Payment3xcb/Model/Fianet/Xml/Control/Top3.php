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

class Fianet_Payment3xcb_Model_Fianet_Xml_Control_Top3 extends Fianet_Payment3xcb_Model_Fianet_Xml_Node
{
    public function __construct()
    {
        parent::__construct(/** @lang XML */'<top3 version="1.0"/>');
    }

    /**
     * @return $this
     */
    public function load()
    {
        $order = $this->getOrder();
        // TODO: hardcoded delivery date
        $deliveryDate = new DateTime($order->getCreatedAt());
        $deliveryDate->modify('+ 3 days');
        $datelivr = $deliveryDate->format('Y-m-d');

        $this->getChild('datelivr')
            ->setContent($datelivr);

        $root = $this->getRootNode();
        /** @var Fianet_Payment3xcb_Model_Fianet_Checksum $checksum */
        $checksum = Mage::getSingleton('fianetpayment3xcb/fianet_checksum');
        $dataToHash = array(
            'merchantreference' => (string) $root->getValue('infocommande/merchantreference'),
            'refid'             => (string) $root->getValue('infocommande/refid'),
            'montant'           => (string) $root->getValue('infocommande/montant'),
            'email'             => (string) $root->getValue('utilisateur[@type="facturation"]/email'),
            'datelivr'          => (string) $root->getValue('top3/datelivr'),
        );

        $this->getChild('crypt')
            ->setAttribute('version', '3.0')
            ->setContent($checksum->getHash($dataToHash));

        return $this;
    }
}
