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

class Fianet_Payment3xcb_Block_Adminhtml_Mapping_Transaction_State_Edit extends Mage_Adminhtml_Block_Template
{
    /**
     * @param $transactionState
     * @return Fianet_Payment3xcb_Model_Mapping_Transaction_State
     */
    public function getMapping($transactionState)
    {
        /** @var Fianet_Payment3xcb_Model_Mapping_Transaction_State $mapping */
        $mapping = Mage::getModel('fianetpayment3xcb/mapping_transaction_state')->load($transactionState);
        return $mapping;
    }

    /**
     * @return array
     */
    public function getTransactionStateOptionArray()
    {
        /** @var Fianet_Payment3xcb_Model_Source_Fianet_TransactionState $source */
        $source = Mage::getModel('fianetpayment3xcb/source_fianet_transactionState');
        return $source->toOptionArray();
    }

    /**
     * @return array
     */
    public function getOrderStatuses()
    {
        /** @var Mage_Sales_Model_Order_Config $config */
        $config = Mage::getSingleton('sales/order_config');
        return $config->getStatuses();
    }
}
