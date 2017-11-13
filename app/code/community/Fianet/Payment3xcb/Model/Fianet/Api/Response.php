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

class Fianet_Payment3xcb_Model_Fianet_Api_Response
{
    /**
     * @var string
     */
    protected $_uri;

    /**
     * @var string
     */
    protected $_request;

    /**
     * @var Zend_Http_Response
     */
    protected $_httpResponse;

    /**
     * @var Fianet_Payment3xcb_Model_Fianet_Result
     */
    protected $_result;

    /**
     * @param string $uri
     * @return $this
     */
    public function setUri($uri)
    {
        $this->_uri = $uri;
        return $this;
    }

    /**
     * @param string $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * @param Zend_Http_Response $httpResponse
     * @return $this
     */
    public function setHttpResponse($httpResponse)
    {
        $this->_httpResponse = $httpResponse;
        return $this;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->_uri;
    }

    /**
     * @return Fianet_Payment3xcb_Model_Fianet_Result
     */
    public function getResult()
    {
        if (!isset($this->_result)) {
            /** @var Fianet_Payment3xcb_Model_Fianet_Result $result */
            $result = Mage::getModel('fianetpayment3xcb/fianet_result');
            $this->_result = $result->setBody($this->_httpResponse->getBody());
        }

        return $this->_result;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->_httpResponse->getStatus();
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->_httpResponse->getMessage();
    }

    /**
     * @return string
     */
    public function toString()
    {
        return "{$this->getUri()}: [{$this->getStatus()}] {$this->getMessage()}";
    }
}
