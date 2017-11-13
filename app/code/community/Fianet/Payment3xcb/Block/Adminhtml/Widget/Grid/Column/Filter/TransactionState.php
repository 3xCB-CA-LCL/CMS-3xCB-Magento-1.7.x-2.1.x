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

class Fianet_Payment3xcb_Block_Adminhtml_Widget_Grid_Column_Filter_TransactionState
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    /**
     * @var array|bool
     */
    protected $_options = false;

    /**
     * @return array
     */
    protected function _getOptions()
    {
        if (!$this->_options) {
            /** @var Fianet_Payment3xcb_Model_Source_Fianet_TransactionState $source */
            $source = Mage::getModel('fianetpayment3xcb/source_fianet_transactionState');
            $options = $source->toOptionArray();
            array_unshift($options, array('value' =>  '', 'label' =>  ''));
            $this->_options = $options;
        }

        return $this->_options;
    }
}
