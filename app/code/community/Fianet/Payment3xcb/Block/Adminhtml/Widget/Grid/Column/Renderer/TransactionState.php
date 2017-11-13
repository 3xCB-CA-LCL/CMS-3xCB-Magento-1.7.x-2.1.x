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

class Fianet_Payment3xcb_Block_Adminhtml_Widget_Grid_Column_Renderer_TransactionState
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $state = null;
        $mode = null;
        if ($row instanceof Fianet_Payment3xcb_Model_Transaction_History) {
            $state = $row->getState();
            $mode = $row->getMode();
        } elseif ($row instanceof Mage_Sales_Model_Order) {
            $state = $row->getFianet3xcbState();
            $mode = $row->getFianet3xcbMode();
        }

        $options = $this->getColumn()->getRenderoptions();

        /** @var Fianet_Payment3xcb_Block_TransactionState $block */
        $block = $this->getLayout()->createBlock('fianetpayment3xcb/transactionState');
        return $block->setState($state)
            ->setMode($options['mode'] != 'full' ? $mode : null)
            ->toHtml();
    }
}
