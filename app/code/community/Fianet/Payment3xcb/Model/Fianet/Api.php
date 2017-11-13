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

class Fianet_Payment3xcb_Model_Fianet_Api
{
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
     * @return Fianet_Payment3xcb_Helper_Data
     */
    protected function getHelper()
    {
        /** @var Fianet_Payment3xcb_Helper_Data $helper */
        $helper = Mage::helper('fianetpayment3xcb');
        return $helper;
    }

    /**
     * @return string
     */
    protected function getMerchantReference()
    {
        return $this->getHelper()->getMerchantReference();
    }

    /**
     * @param int $timeout
     * @return Fianet_Payment3xcb_Model_Fianet_Client
     */
    protected function getClient($timeout = 10)
    {
        /** @var Fianet_Payment3xcb_Model_Fianet_Client $client */
        $client = Mage::getModel('fianetpayment3xcb/fianet_client');
        return $client->setHttpClientConfig(
            array(
                'maxredirects' => 0,
                'timeout' => $timeout,
            )
        );
    }

    /**
     * @param array $data
     * @return string
     */
    public function getChecksum($data)
    {
        /** @var Fianet_Payment3xcb_Model_Fianet_Checksum $checksum */
        $checksum = Mage::getSingleton('fianetpayment3xcb/fianet_checksum');
        return $checksum->getHash($data);
    }

    /**
     * @param string $url
     * @param array $data
     * @param int $timeout
     * @return Fianet_Payment3xcb_Model_Fianet_Api_Response
     */
    protected function callUrl($url, $data, $timeout = 10)
    {
        $client = $this->getClient($timeout);

        $response = $client->restPostJson($url, $data);
        $httpClient = $client->getHttpClient();
        $lastRequest = $httpClient->getLastRequest();

        $this->logger()->debug(
            Mage::helper('fianetpayment3xcb')->__(
                "API: request: %s\n API: response: %s",
                $lastRequest,
                '[' . $response->getStatus() . '] ' . $response->getMessage() . ' - body: ' . $response->getBody()
            )
        );

        /** @var Fianet_Payment3xcb_Model_Fianet_Api_Response $apiResponse */
        $apiResponse = Mage::getModel('fianetpayment3xcb/fianet_api_response');
        return $apiResponse->setUri($httpClient->getUri($asString = true))
            ->setRequest($lastRequest)
            ->setHttpResponse($response);
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return Fianet_Payment3xcb_Model_Fianet_Api_Response|null
     */
    public function isAvailable(Mage_Sales_Model_Quote $quote)
    {
        $merchantReference = $this->getMerchantReference();

        $amount = (string) round($quote->getBaseGrandTotal() * 100);
        $billingAddress = $quote->getBillingAddress();
        $countryId = $billingAddress ? $billingAddress->getCountryId() : null;
        $data = array(
            'commandamount' => $amount,
            'country' => $countryId ? $this->getHelper()->getCountryIso3Code($countryId) : null,
        );
        $data['checksum'] = $this->getChecksum(
            array(
                'merchantreference' => $merchantReference,
                'commandamount' => $amount,
                'country' => $data['country'],
            )
        );

        try {
            return $this->callUrl(
                'eligible/' . $merchantReference,
                $data,
                $timeout = 2
            );
        } catch (Exception $e) {
            $this->logger()->error(
                Mage::helper('fianetpayment3xcb')->__('Unable to retrieve 3xCB availability: %s', $e->getMessage())
            );
            return null;
        }
    }

    /**
     * @param string $orderIncrementId
     * @return Fianet_Payment3xcb_Model_Fianet_Api_Response|null
     */
    public function getTransaction($orderIncrementId)
    {
        $merchantReference = $this->getMerchantReference();
        $refId = $orderIncrementId;

        $data = array(
            'checksum' => $this->getChecksum(
                array(
                    'merchantreference' => $merchantReference,
                    'top3reference' => '',
                    'refid' => $refId,
                )
            )
        );

        try {
            return $this->callUrl(
                'gettransaction/' . $merchantReference . '/refid/' . $refId,
                $data
            );
        } catch (Exception $e) {
            $this->logger()->error(
                Mage::helper('fianetpayment3xcb')->__(
                    'Order #%s - Unable to retrieve 3xCB transaction: %s',
                    $orderIncrementId,
                    $e->getMessage()
                )
            );
            return null;
        }
    }

    /**
     * @param string $action
     * @param string $transactionReference
     * @return Fianet_Payment3xcb_Model_Fianet_Api_Response
     */
    protected function callMerchantAction($action, $transactionReference)
    {
        $merchantReference = $this->getMerchantReference();

        $data = array(
            'checksum' => $this->getChecksum(
                array(
                    'merchantreference' => $this->getMerchantReference(),
                    'top3reference' => $transactionReference,
                    'action' => $action,
                )
            )
        );

        return $this->callUrl(
            $action . '/' . $merchantReference . '/top3reference/' . $transactionReference,
            $data
        );
    }

    /**
     * @param string $transactionReference
     * @return Fianet_Payment3xcb_Model_Fianet_Api_Response
     */
    public function cancelTransaction($transactionReference)
    {
        return $this->callMerchantAction('canceltransaction', $transactionReference);
    }

    /**
     * @param string $transactionReference
     * @return Fianet_Payment3xcb_Model_Fianet_Api_Response
     */
    public function validateTransaction($transactionReference)
    {
        return $this->callMerchantAction('validatetransaction', $transactionReference);
    }

    /**
     * @param string $transactionReference
     * @param double $finalAmount
     * @return Fianet_Payment3xcb_Model_Fianet_Api_Response
     */
    public function partiallyValidateTransaction($transactionReference, $finalAmount)
    {
        $merchantReference = $this->getMerchantReference();

        $action = 'partiallyvalidatetransaction';
        $data = array(
            'montantfinal' => $finalAmount * 100,
            'checksum' => $this->getChecksum(
                array(
                    'merchantreference' => $merchantReference,
                    'top3reference' => $transactionReference,
                    'montantfinal' => $finalAmount * 100,
                    'action' => $action,
                )
            )
        );

        return $this->callUrl(
            $action . '/' . $merchantReference . '/top3reference/' . $transactionReference,
            $data
        );
    }
}
