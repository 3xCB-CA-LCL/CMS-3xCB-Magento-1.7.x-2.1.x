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

class Fianet_Payment3xcb_Adminhtml_LogController extends Fianet_Payment3xcb_Controller_Adminhtml_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('adminfianetpayment3xcb')
            ->_addBreadcrumb(
                $this->__('Log'),
                $this->__('Records Manager')
            );
        $this->setHeadTitle($this->__('Log'));

        return $this;
    }

    protected function _isAllowed()
    {
        return $this->getAdminSession()
            ->isAllowed('admin/system/fianetpayment3xcb/log');
    }

    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

    public function exportCsvAction()
    {
        $fileName = 'fianetpayment3xcb_log.csv';
        /** @var Fianet_Payment3xcb_Block_Adminhtml_Log_Grid $grid */
        $grid = $this->getLayout()->createBlock('fianetpayment3xcb/adminhtml_log_grid');
        $content = $grid->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'fianetpayment3xcb_log.xml';
        /** @var Fianet_Payment3xcb_Block_Adminhtml_Log_Grid $grid */
        $grid = $this->getLayout()->createBlock('fianetpayment3xcb/adminhtml_log_grid');
        $content = $grid->getXml();
        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream')
    {
        /** @var Mage_Core_Model_Date $date */
        $date = Mage::getSingleton('core/date');
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', $date->gmtDate('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        exit;
    }

    public function massDeleteAction()
    {
        $logIds = $this->getRequest()->getParam('log_ids');
        if (!is_array($logIds)) {
            $this->_getSession()->addError(
                Mage::helper('adminhtml')->__('Please select record(s).')
            );
        } else {
            try {
                // TODO: optimize
                foreach ($logIds as $logId) {
                    $this->deleteLog($logId);
                }

                $this->_getSession()->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) have been deleted.', count($logIds)
                    )
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * @param int $logId
     */
    protected function deleteLog($logId)
    {
        Mage::getModel('fianetpayment3xcb/log')->load($logId)->delete();
    }
}
