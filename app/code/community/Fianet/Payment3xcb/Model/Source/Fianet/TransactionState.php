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

class Fianet_Payment3xcb_Model_Source_Fianet_TransactionState extends Fianet_Payment3xcb_Model_Source_Abstract
{
    const CANCELLATION_ASKED          = 'CANCELLATION_ASKED';
    const CONTRACT_ACCEPTED           = 'CONTRACT_ACCEPTED';
    const CONTRACT_REFUSED            = 'CONTRACT_REFUSED';
    const CONTRACT_REVIEW_IN_PROGRESS = 'CONTRACT_REVIEW_IN_PROGRESS';
    const CONTRACT_SENT               = 'CONTRACT_SENT';
    const DEBIT_SENT                  = 'DEBIT_SENT';
    const PARTIAL_VALIDATION_ASKED    = 'PARTIAL_VALIDATION_ASKED';
    const PAYMENT_ABORTED             = 'PAYMENT_ABORTED';
    const PAYMENT_CANCELLED           = 'PAYMENT_CANCELLED';
    const PAYMENT_IN_PROGRESS         = 'PAYMENT_IN_PROGRESS'; // Undocumented
    const PAYMENT_KO                  = 'PAYMENT_KO';
    const PAYMENT_STORED              = 'PAYMENT_STORED';
    const PAYMENT_VALIDATED           = 'PAYMENT_VALIDATED';
    const REQUEST_KO                  = 'REQUEST_KO';
    const VALIDATION_ASKED            = 'VALIDATION_ASKED';

    /**
     * @return array
     */
    public function toArray()
    {
        $helper = Mage::helper('fianetpayment3xcb');
        return array(
            self::PAYMENT_IN_PROGRESS         => $helper->__('In Progress'),
            self::PAYMENT_ABORTED             => $helper->__('Aborted'),
            self::PAYMENT_KO                  => $helper->__('Refused'),
            self::PAYMENT_STORED              => $helper->__('Stored'),
            self::CONTRACT_REVIEW_IN_PROGRESS => $helper->__('Contract Review In Progress'),
            self::CONTRACT_REFUSED            => $helper->__('Contract Refused'),
            self::CONTRACT_ACCEPTED           => $helper->__('Contract Accepted'),
            self::VALIDATION_ASKED            => $helper->__('Validation Asked'),
            self::PARTIAL_VALIDATION_ASKED    => $helper->__('Partial Validation Asked'),
            self::CANCELLATION_ASKED          => $helper->__('Cancellation Asked'),
            self::PAYMENT_CANCELLED           => $helper->__('Cancelled'),
            self::PAYMENT_VALIDATED           => $helper->__('Validated'),
            self::DEBIT_SENT                  => $helper->__('Debit Sent'),
            self::CONTRACT_SENT               => $helper->__('Contract Sent'),
        );
    }

    /**
     * @param $state
     * @return string|null
     */
    public function getLabel($state)
    {
        foreach ($this->toArray() as $value => $label) {
            if ($state == $value) {
                return $label;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getPendingMerchantStates()
    {
        return array(
            self::CONTRACT_ACCEPTED,
        );
    }

    /**
     * @return array
     */
    public function getFinalStates()
    {
        return array(
            self::PAYMENT_ABORTED,
            self::PAYMENT_KO,
            self::CONTRACT_REFUSED,
            self::PAYMENT_CANCELLED,
            self::CONTRACT_SENT,
        );
    }

    /**
     * @param $state
     * @return bool
     */
    public function isAborted($state)
    {
        return $state == self::PAYMENT_ABORTED;
    }

    /**
     * @param $state
     * @return bool
     */
    public function isPendingMerchant($state)
    {
        return in_array($state, $this->getPendingMerchantStates());
    }

    /**
     * @param $state
     * @return bool
     */
    public function isPendingFianet($state)
    {
        return in_array(
            $state,
            array(
                self::PAYMENT_IN_PROGRESS,
                self::PAYMENT_STORED,
                self::CONTRACT_REVIEW_IN_PROGRESS,
                self::VALIDATION_ASKED,
                self::PARTIAL_VALIDATION_ASKED,
                self::CANCELLATION_ASKED,
                self::PAYMENT_VALIDATED,
                self::DEBIT_SENT,
            )
        );
    }

    /**
     * @param $state
     * @return bool
     */
    public function isFinal($state)
    {
        return in_array($state, $this->getFinalStates());
    }

    /**
     * @param $state
     * @return bool
     */
    public function isValid($state)
    {
        return in_array(
            $state,
            array(
                self::CONTRACT_ACCEPTED,
                self::PAYMENT_VALIDATED,
                self::DEBIT_SENT,
                self::CONTRACT_SENT,
            )
        );
    }

    /**
     * @param $state
     * @return bool
     */
    public function isInvalid($state)
    {
        return in_array(
            $state,
            array(
                self::REQUEST_KO,
                self::PAYMENT_ABORTED,
                self::PAYMENT_KO,
                self::CONTRACT_REFUSED,
                self::PAYMENT_CANCELLED,
            )
        );
    }
}
