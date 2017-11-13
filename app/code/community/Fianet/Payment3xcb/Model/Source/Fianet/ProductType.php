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

class Fianet_Payment3xcb_Model_Source_Fianet_ProductType
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('fianetpayment3xcb');
        return array(
            array('value' => 1, 'label' => $helper->__('Alimentation & gastronomie')),
            array('value' => 2, 'label' => $helper->__('Auto & moto')),
            array('value' => 3, 'label' => $helper->__('Culture & divertissements')),
            array('value' => 4, 'label' => $helper->__('Maison & jardin')),
            array('value' => 5, 'label' => $helper->__('Electroménager')),
            array('value' => 6, 'label' => $helper->__('Enchères et achats groupés')),
            array('value' => 7, 'label' => $helper->__('Fleurs & cadeaux')),
            array('value' => 8, 'label' => $helper->__('Informatique & logiciels')),
            array('value' => 9, 'label' => $helper->__('Santé & beauté')),
            array('value' => 10, 'label' => $helper->__('Services aux particuliers')),
            array('value' => 11, 'label' => $helper->__('Services aux professionnels')),
            array('value' => 12, 'label' => $helper->__('Sport')),
            array('value' => 13, 'label' => $helper->__('Vêtements & accessoires')),
            array('value' => 14, 'label' => $helper->__('Voyage & tourisme')),
            array('value' => 15, 'label' => $helper->__('Hifi, photo & vidéos')),
            array('value' => 16, 'label' => $helper->__('Téléphonie & communication')),
            array('value' => 17, 'label' => $helper->__('Bijoux et métaux précieux')),
            array('value' => 18, 'label' => $helper->__('Articles et accessoires pour bébé')),
            array('value' => 19, 'label' => $helper->__('Sonorisation & lumière'))
        );
    }
}
