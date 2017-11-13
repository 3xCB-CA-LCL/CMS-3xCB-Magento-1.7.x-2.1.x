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
 * Class Fianet_Payment3xcb_Block_Payment_Redirect
 *
 * @method string getAutoSend()
 * @method string getChecksum()
 * @method string getFormUrl()
 * @method string getHasError()
 * @method string getUrlCall()
 * @method string getUrlSys()
 * @method string getXmlInfo()
 * @method string getXmlParam()
 * @method Fianet_Payment3xcb_Block_Payment_Redirect setAutoSend(bool $autoSend)
 * @method Fianet_Payment3xcb_Block_Payment_Redirect setChecksum(string $checksum)
 * @method Fianet_Payment3xcb_Block_Payment_Redirect setFormUrl(string $url)
 * @method Fianet_Payment3xcb_Block_Payment_Redirect setHasError(bool $hasError)
 * @method Fianet_Payment3xcb_Block_Payment_Redirect setUrlCall(string $url)
 * @method Fianet_Payment3xcb_Block_Payment_Redirect setUrlSys(string $url)
 * @method Fianet_Payment3xcb_Block_Payment_Redirect setXmlInfo(string $xml)
 * @method Fianet_Payment3xcb_Block_Payment_Redirect setXmlParam(string $xml)
 */
class Fianet_Payment3xcb_Block_Payment_Redirect extends Mage_Core_Block_Template
{
    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        $this->setTemplate('fianetpayment3xcb/payment/redirect.phtml');
        parent::_construct();
    }
}
