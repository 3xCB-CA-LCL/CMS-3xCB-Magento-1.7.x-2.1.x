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

class Fianet_Payment3xcb_Model_Fianet_Client extends Zend_Rest_Client
{
    public function __construct($uri = null)
    {
        parent::__construct($uri);
        if (!$this->getUri()) {
            $this->setUri($this->getApiUrl());
        }

        $this->setHttpClientConfig(
            array(
                'maxredirects' => 0,
                'timeout' => 2,
            )
        );
    }

    /**
     * Performs an HTTP POST request to $path.
     *
     * @param string $path
     * @param array $data Data to send
     * @throws Zend_Http_Client_Exception
     * @return Zend_Http_Response
     */
    public function restPostJson($path, $data)
    {
        $this->customPrepareRest($path);
        $client = $this->getHttpClient();
        $data = Zend_Json::encode($data);
        $client->setRawData($data, 'application/json');
        return $client->request('POST');
    }

    /**
     * Call a remote REST web service URI and return the Zend_Http_Response object
     *
     * @param  string $path            The path to append to the URI
     * @throws Zend_Rest_Client_Exception
     * @return void
     */
    protected function customPrepareRest($path)
    {
        // Get the URI object and configure it
        if (!$this->_uri instanceof Zend_Uri_Http) {
            throw new Zend_Rest_Client_Exception('URI object must be set before performing call');
        }

        $uri = $this->_uri->getUri();

        if ($path[0] != '/' && $uri[strlen($uri)-1] != '/') {
            $path = '/' . $path;
        }

        // Prepend existing path
        if (!empty($this->_uri->getPath()) && $this->_uri->getPath() != '/' && $uri[strlen($uri) - 1] == '/') {
            $path = $this->_uri->getPath() . $path;
        }

        $this->_uri->setPath($path);

        /**
         * Get the HTTP client and configure it for the endpoint URI.  Do this each time
         * because the Zend_Http_Client instance is shared among all Zend_Service_Abstract subclasses.
         */
        if (isset($this->_noReset) && $this->_noReset === true) {
            // if $_noReset we do not want to reset on this request, 
            // but we do on any subsequent request
            $this->_noReset = false;
        } else {
            $this->getHttpClient()->resetParameters();
        }

        $this->getHttpClient()->setUri($this->_uri);
    }

    /**
     * @param string $path
     * @return mixed
     */
    protected function getStoreConfig($path)
    {
        $storeId = Mage::app()->getStore()->getId();
        return Mage::getStoreConfig($path, $storeId);
    }

    /**
     * @return string
     */
    protected function getApiMode()
    {
        /** @var Fianet_Payment3xcb_Helper_Data $helper */
        $helper = Mage::helper('fianetpayment3xcb');
        return $helper->getPaymentMode();
    }

    /**
     * @return mixed
     */
    protected function getApiUrl()
    {
        $mode = $this->getApiMode();
        return $this->getStoreConfig('fianetpayment3xcb/' . $mode . '/url_webapp_ws');
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setHttpClientConfig($config)
    {
        $this->getHttpClient()
            ->setConfig($config);
        return $this;
    }
}
