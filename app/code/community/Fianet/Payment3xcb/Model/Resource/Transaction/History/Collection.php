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

class Fianet_Payment3xcb_Model_Resource_Transaction_History_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('fianetpayment3xcb/transaction_history');
    }

    /**
     * @param $orderIncrementId
     * @return $this
     */
    public function addFilterOnOrderIncrementId($orderIncrementId)
    {
        $this->getSelect()
            ->where('order_increment_id = ?', $orderIncrementId);
        return $this;
    }

    /**
     * @param $mode
     * @return $this
     */
    public function addFilterOnMode($mode)
    {
        $this->getSelect()
            ->where('mode = ?', $mode);
        return $this;
    }

    /**
     * @param $states
     * @return $this
     */
    public function addFilterOnState($states)
    {
        $this->getSelect()
            ->where('state in (?)', $states);
        return $this;
    }

    /**
     * @param string $sort
     * @return $this
     */
    public function sortOnCreatedAt($sort = 'DESC')
    {
        $this->getSelect()
            ->order('created_at ' . $sort);
        return $this;
    }

    /**
     * @param $transactionReference
     * @return $this
     */
    public function filterLastPertinentTransactionHistory($transactionReference)
    {
        $this->getSelect()
            ->where('top3reference = ?', $transactionReference)
            ->order('created_at desc')
            ->limit(1);
        return $this;
    }

    /**
     * @param $orderIncrementId
     * @return $this
     */
    public function filterLastOrderTransactionHistory($orderIncrementId)
    {
        $this->getSelect()
            ->where('order_increment_id = ?', $orderIncrementId)
            ->order('created_at desc')
            ->limit(1);
        return $this;
    }
}
