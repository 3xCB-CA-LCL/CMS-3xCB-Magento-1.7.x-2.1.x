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

class Fianet_Payment3xcb_Model_Fianet_Xml_Control_Adresse_Livraison
    extends Fianet_Payment3xcb_Model_Fianet_Xml_Control_Adresse
{
    /**
     * @return $this
     */
    public function load()
    {
        $this->setAttribute('type', 'livraison');
        $this->setAttribute('format', '1');
        $this->copyFrom(
            $this->getOrder()
                ->getShippingAddress()
        );
        return $this;
    }
}
