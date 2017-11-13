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

class Fianet_Payment3xcb_Model_Adminhtml_System_Config_Source_AvailableFor
{
    const ALL_CUSTOMERS = 0;
    const SPECIFIC_CUSTOMER_GROUPS = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('adminhtml');
        return array(
            array('value' => self::ALL_CUSTOMERS,            'label' => $helper->__('All Customers')),
            array('value' => self::SPECIFIC_CUSTOMER_GROUPS, 'label' => $helper->__('Specific Customer Groups')),
        );
    }
}
