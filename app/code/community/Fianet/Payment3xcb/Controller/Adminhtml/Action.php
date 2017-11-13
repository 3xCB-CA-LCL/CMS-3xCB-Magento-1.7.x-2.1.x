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

class Fianet_Payment3xcb_Controller_Adminhtml_Action extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->getAdminSession()
            ->isAllowed('admin');
    }

    /**
     * @return Mage_Admin_Model_Session
     */
    protected function getAdminSession()
    {
        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');
        return $session;
    }

    /**
     * @return Fianet_Payment3xcb_Helper_Data
     */
    protected function _getHelper()
    {
        /** @var Fianet_Payment3xcb_Helper_Data $helper */
        $helper = Mage::helper('fianetpayment3xcb');
        return $helper;
    }

    /**
     * @return Fianet_Payment3xcb_Model_Logger
     */
    protected function logger()
    {
        /** @var Fianet_Payment3xcb_Model_Logger $logger */
        $logger = Mage::getSingleton('fianetpayment3xcb/logger');
        return $logger;
    }

    /**
     * @return Mage_Adminhtml_Block_Page_Head
     */
    protected function getHeadBlock()
    {
        /** @var Mage_Adminhtml_Block_Page_Head $head */
        $head = $this->getLayout()
            ->getBlock('head');
        return $head;
    }

    /**
     * @param string $title
     */
    protected function setHeadTitle($title)
    {
        $this->getHeadBlock()->setTitle($title);
    }
}
