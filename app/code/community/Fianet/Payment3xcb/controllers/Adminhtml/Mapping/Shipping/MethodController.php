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

class Fianet_Payment3xcb_Adminhtml_Mapping_Shipping_MethodController
    extends Fianet_Payment3xcb_Controller_Adminhtml_Action
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->getAdminSession()
            ->isAllowed('admin/system/fianetpayment3xcb/mapping_shipping_method');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('adminfianetpayment3xcb');
        $this->setHeadTitle($this->__('Shipping Method to FIA-NET Shipping Type Mapping'));
        $this->renderLayout();
    }

    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        
        try {
            if (empty($post)) {
                Mage::throwException($this->__('Invalid form data.'));
            }

            if (isset($post['store_location']) && is_array($post['store_location'])) {
                foreach ($post['store_location'] as $key => $value) {
                    $this->saveStoreInformation($key, $value);
                }
            }

            $shippingNameToDefine = false;
            if (isset($post['mapping']) && is_array($post['mapping'])) {
                foreach ($post['mapping'] as $carrierCode => $carrierData) {
                    foreach ($carrierData as $methodCode => $methodData) {
                        if (empty($methodData['fianet_shipping_name'])) {
                            $shippingNameToDefine = true;
                        }

                        $this->saveMapping($carrierCode, $methodCode, $methodData);
                    }
                }
            }

            if ($shippingNameToDefine) {
                $this->_getSession()->addError(
                    $this->__("Data successfully saved but the name of some shipping methods must be defined.")
                );
            } else {
                $this->_getSession()->addSuccess($this->__('Data successfully saved.'));
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect('*/*');
    }

    /**
     * @param string $key
     * @param string $value
     */
    protected function saveStoreInformation($key, $value)
    {
        /** @var Fianet_Payment3xcb_Model_Store_Information $storeInformation */
        $storeInformation = Mage::getModel('fianetpayment3xcb/store_information')->load($key);
        $storeInformation->setInformation($key)
            ->setValue($value)
            ->save();
    }

    /**
     * @param string $carrierCode
     * @param string $methodCode
     * @param array $methodData
     */
    protected function saveMapping($carrierCode, $methodCode, $methodData)
    {
        /** @var Fianet_Payment3xcb_Model_Mapping_Shipping_Method $mapping */
        $mapping = Mage::getModel('fianetpayment3xcb/mapping_shipping_method');
        $mapping->loadByCarrierCodeAndMethodCode($carrierCode, $methodCode)
            ->setCarrierCode($carrierCode)
            ->setMethodCode($methodCode)
            ->setFianetShippingTypeId($methodData['fianet_shipping_type_id'])
            ->setFianetShippingSpeedId($methodData['fianet_shipping_speed_id'])
            ->setFianetShippingName($methodData['fianet_shipping_name'])
            ->save();
    }
}
