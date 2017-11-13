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

class Fianet_Payment3xcb_Model_Fianet_Xml_Control_Utilisateur extends Fianet_Payment3xcb_Model_Fianet_Xml_Node
    implements Fianet_Payment3xcb_Api_CustomerInterface
{
    public function __construct()
    {
        parent::__construct(/** @lang XML */'<utilisateur/>');
    }

    /**
     * @return $this
     */
    public function load()
    {
        $this->setAttribute('type', '');
        $this->setAttribute('qualite', 2); // particulier
        return $this;
    }

    /**
     * @param string $lastname
     * @return Fianet_Payment3xcb_Api_CustomerInterface
     */
    public function setLastname($lastname)
    {
        $this->getChild('nom')
            ->setContent($lastname);
        return $this;
    }

    /**
     * @param string $firstname
     * @return Fianet_Payment3xcb_Api_CustomerInterface
     */
    public function setFirstname($firstname)
    {
        $this->getChild('prenom')
            ->setContent($firstname);
        return $this;
    }

    /**
     * @param string $email
     * @return Fianet_Payment3xcb_Api_CustomerInterface
     */
    public function setEmail($email)
    {
        $this->getChild('email')
            ->setContent($email);
        return $this;
    }

    /**
     * @param string $telephone
     * @return Fianet_Payment3xcb_Api_CustomerInterface
     */
    public function setTelephone($telephone)
    {
        $this->getChild('telhome')
            ->setContent($this->cleanTelephoneNumber($telephone));
        return $this;
    }

    /**
     * @param string $mobilePhone
     * @return Fianet_Payment3xcb_Api_CustomerInterface
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->getChild('telmobile')
            ->setContent(
                $this->cleanTelephoneNumber($mobilePhone)
            );
        return $this;
    }

    /**
     * @param string $fax
     * @return Fianet_Payment3xcb_Api_CustomerInterface
     */
    public function setFax($fax)
    {
        $this->getChild('telfax')
            ->setContent($this->cleanTelephoneNumber($fax));
        return $this;
    }

    /**
     * @param string $company
     * @return Fianet_Payment3xcb_Api_CustomerInterface
     */
    public function setCompany($company)
    {
        $this->getChild('societe')
            ->setContent($company);
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Order_Address $address
     * @return Fianet_Payment3xcb_Api_CustomerInterface
     */
    public function copyFrom(Mage_Sales_Model_Order_Address $address)
    {
        $this->setLastname($address->getLastname());
        $this->setFirstname($address->getFirstname());

        if ($value = $address->getTelephone()) {
            $this->setTelephone($value);
        }

        if ($value = $address->getFax()) {
            $this->setFax($value);
        }

        if ($value = $address->getCompany()) {
            $this->setCompany($value);
        }

        return $this;
    }
}
