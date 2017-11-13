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

class Fianet_Payment3xcb_Model_Fianet_Xml_Control_Infocommande extends Fianet_Payment3xcb_Model_Fianet_Xml_Node
{
    public function __construct()
    {
        parent::__construct(/** @lang XML */'<infocommande/>');
    }

    /**
     * @return $this
     */
    public function load()
    {
        $order = $this->getOrder();
        $this->getChild('merchantreference')
            ->setContent($this->getHelper()->getMerchantReference());

        $this->getChild('refid')
            ->setContent($order->getRealOrderId());

        $this->getChild('montant')
            ->setContent(
                number_format($this->_order->getBaseTotalDue() * 100, 2, '.', '') // centimes
            )
            ->setAttribute('devise', $order->getBaseCurrencyCode());

        $this->appendChildNode('control_infocommande_transport')->init();
        $this->appendChildNode('control_infocommande_list')->init();

        return $this;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        if ($this->getValue('merchantreference') === null) {
            $this->reportError(
                Mage::helper('fianetpayment3xcb')->__('`merchantreference` is undefined')
            );
        }

        if ($this->getValue('refid') === null) {
            $this->reportError(
                Mage::helper('fianetpayment3xcb')->__('`refid` is undefined')
            );
        }

        if ($this->getValue('montant') === null) {
            $this->reportError(
                Mage::helper('fianetpayment3xcb')->__('`montant` is undefined')
            );
        }

        if ($this->getValue('montant/@devise') === null) {
            $this->reportError(
                Mage::helper('fianetpayment3xcb')->__('`devise` is undefined')
            );
        }

        return parent::validate();
    }
}
