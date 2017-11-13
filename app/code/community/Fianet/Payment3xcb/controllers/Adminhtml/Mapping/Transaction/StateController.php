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

class Fianet_Payment3xcb_Adminhtml_Mapping_Transaction_StateController
    extends Fianet_Payment3xcb_Controller_Adminhtml_Action
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->getAdminSession()
            ->isAllowed('admin/system/fianetpayment3xcb/mapping_transaction_state');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('adminfianetpayment3xcb');
        $this->setHeadTitle($this->__('Status'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        $post = $this->getRequest()->getPost();
        try {
            if (empty($post['mapping'])) {
                Mage::throwException($this->__('Invalid form data.'));
            }

            foreach ($post['mapping'] as $transactionState => $mapping) {
                if (!empty($mapping['order_status'])) {
                    $this->saveMapping(
                        $transactionState,
                        $mapping['order_status'],
                        isset($mapping['notify']) ? '1' : '0'
                    );
                }
            }

            $message = $this->__('Data successfully saved.');
            $this->_getSession()->addSuccess($message);
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    /**
     * @param string $transactionState
     * @param string $orderStatus
     * @param bool $notifyCustomer
     */
    protected function saveMapping($transactionState, $orderStatus, $notifyCustomer)
    {
        /** @var Fianet_Payment3xcb_Model_Mapping_Transaction_State $mapping */
        $mapping = Mage::getModel('fianetpayment3xcb/mapping_transaction_state');
        $mapping->setTransactionState($transactionState)
            ->setOrderStatus($orderStatus)
            ->setNotifyCustomer($notifyCustomer)
            ->save();
    }
}
