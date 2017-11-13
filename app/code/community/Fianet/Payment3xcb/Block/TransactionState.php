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

/**
 * Class Fianet_Payment3xcb_Block_Transaction_State
 *
 * @method string|null getMode()
 * @method string getState()
 * @method Fianet_Payment3xcb_Block_TransactionState setState(string $state)
 * @method Fianet_Payment3xcb_Block_TransactionState setMode(string $mode)
 */
class Fianet_Payment3xcb_Block_TransactionState extends Mage_Core_Block_Abstract
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';

        $state = $this->getState();
        if (!$state) {
            return $html;
        }

        /** @var Fianet_Payment3xcb_Model_Source_Fianet_TransactionState $source */
        $source = Mage::getModel('fianetpayment3xcb/source_fianet_transactionState');
        $title = $source->getLabel($state);

        if ($source->isAborted($state)) {
            $className = 'fianet3xcb-state-aborted';
        } elseif ($source->isPendingFianet($state)) {
            $className = 'fianet3xcb-state-pending-fianet';
        } elseif ($source->isPendingMerchant($state)) {
            $className = 'fianet3xcb-state-pending-merchant';
        } elseif ($source->isValid($state)) {
            $className = 'fianet3xcb-state-valid';
        } else {
            $className = 'fianet3xcb-state-invalid';
        }

        $mode = $this->getMode();
        return '<span class="fianet3xcb-state ' . $this->escapeHtml($className) . '">'
            . ($mode == Fianet_Payment3xcb_Model_Source_Fianet_Mode::TEST ? 'TEST &bull; ' : '')
            . $this->escapeHtml($title)
            . '</span>';
    }
}
