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

class Fianet_Payment3xcb_Adminhtml_Mapping_Catalog_CategoryController
    extends Fianet_Payment3xcb_Controller_Adminhtml_Action
{
    /**
     * @var string
     */
    protected $_block;

    protected function _construct()
    {
        parent::_construct();
        $this->_block = 'fianetpayment3xcb/adminhtml_mapping_catalog_category_tree';
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->getAdminSession()
            ->isAllowed('admin/system/fianetpayment3xcb/mapping_catalog_category');
    }

    /**
     * @param bool $getRootInstead
     * @return Mage_Catalog_Model_Category|null
     */
    protected function _initCategory($getRootInstead = false)
    {
        $categoryId = (int) $this->getRequest()->getParam('id', false);
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        /** @var Mage_Catalog_Model_Category $category */
        $category = Mage::getModel('catalog/category');
        $category->setStoreId($storeId);

        if ($categoryId) {
            $category->load($categoryId);
            if ($storeId) {
                $rootId = Mage::app()->getStore($storeId)->getRootCategoryId();
                if (!in_array($rootId, $category->getPathIds())) {
                    // load root category instead wrong one
                    if ($getRootInstead) {
                        $category->load($rootId);
                    } else {
                        $this->_redirect('*/*/', array('_current' => true, 'id' => null));
                        return null;
                    }
                }
            }
        }

        Mage::register('category', $category);
        Mage::register('current_category', $category);
        return $category;
    }

    public function treeAction()
    {
        $categoryId = (int) $this->getRequest()->getParam('id');
        $storeId = $this->getRequest()->getParam('store', 0);
        if ($storeId) {
            if (!$categoryId) {
                $store = Mage::app()->getStore($storeId);
                $rootId = $store->getRootCategoryId();
                $this->getRequest()->setParam('id', $rootId);
            }
        }

        $category = $this->_initCategory(true);

        /** @var Fianet_Payment3xcb_Block_Adminhtml_Mapping_Catalog_Category_Tree $block */
        $block = $this->getLayout()->createBlock($this->_block);
        $root = $block->getRoot();
        $this->getResponse()->setBody(
            Zend_Json::encode(
                array(
                    'data' => $block->getTree(),
                    'parameters' => array(
                        'text' => $block->buildNodeName($root),
                        'draggable' => false,
                        'allowDrop' => false,
                        'id' => (int) $root->getId(),
                        'expanded' => (int) $block->getIsWasExpanded(),
                        'store_id' => (int) $block->getStore()->getId(),
                        'category_id' => (int) $category->getId(),
                        'root_visible' => (int) $root->getIsVisible(),
                    ),
                )
            )
        );
    }

    public function categoriesJsonAction()
    {
        $this->getAdminSession()->setIsTreeWasExpanded(
            (bool) $this->getRequest()->getParam('expand_all')
        );

        if (($categoryId = (int) $this->getRequest()->getPost('id'))) {
            $this->getRequest()->setParam('id', $categoryId);

            if (!$category = $this->_initCategory()) {
                return;
            }

            /** @var Fianet_Payment3xcb_Block_Adminhtml_Mapping_Catalog_Category_Tree $block */
            $block = $this->getLayout()->createBlock($this->_block);
            $this->getResponse()->setBody(
                $block->getTreeJson($category)
            );
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('adminfianetpayment3xcb');
        $this->getHeadBlock()
            ->setCanLoadExtJs(true)
            ->setContainerCssClass('catalog-categories');

        $selectedCategory = $this->getAdminSession()
            ->getLastEditedCategory(true);
        if ($selectedCategory) {
            $this->getRequest()
                ->setParam('id', $selectedCategory);
        }

        $selectedCategory = (int) $this->getRequest()
            ->getParam('id', 0);
        $this->_initCategory(true);

        if ($selectedCategory > 0) {
            $this->getLayout()
                ->getBlock('mapping_catalog_category_tree')
                ->setData('selectedCategory', $selectedCategory);
        }

        $this->setHeadTitle($this->__('Product type'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $params = array('_current' => true);
        $redirect = false;

        $adminSession = $this->getAdminSession();
        $storeId = (int) $this->getRequest()
            ->getParam('store');
        $parentId = (int) $this->getRequest()
            ->getParam('parent');
        $prevStoreId = $adminSession->getLastViewedStore(true);

        if ($prevStoreId != null && !$this->getRequest()->getQuery('isAjax')) {
            $params['store'] = $prevStoreId;
            $redirect = true;
        }

        $prevCategoryId = $adminSession->getLastEditedCategory(true);
        if ($prevCategoryId && !$this->getRequest()->getQuery('isAjax')) {
            $this->getRequest()
                ->setParam('id', $prevCategoryId);
        }

        if ($redirect) {
            $this->_redirect('*/*/edit', $params);
            return;
        }

        $categoryId = (int) $this->getRequest()
            ->getParam('id');
        if ($storeId && !$categoryId && !$parentId) {
            $store = Mage::app()->getStore($storeId);
            $prevCategoryId = (int) $store->getRootCategoryId();
            $this->getRequest()
                ->setParam('id', $prevCategoryId);
        }

        if (!($category = $this->_initCategory(true))) {
            return;
        }

        $data = $adminSession->getCategoryData(true);
        if (isset($data['general'])) {
            $category->addData($data['general']);
        }

        if ($this->getRequest()->getQuery('isAjax')) {
            $adminSession->setLastViewedStore($this->getRequest()->getParam('store'));
            $adminSession->setLastEditedCategory($category->getId());
            $this->_initLayoutMessages('adminhtml/session');
            /** @var Mage_Adminhtml_Block_Catalog_Category_Tree $tree */
            $tree = $this->getLayout()
                ->getBlockSingleton('adminhtml/catalog_category_tree');
            $this->getResponse()->setBody(
                $this->getLayout()
                    ->getMessagesBlock()
                    ->getGroupedHtml()
                . $this->getLayout()
                    ->createBlock('fianetpayment3xcb/adminhtml_mapping_catalog_category_edit')
                    ->toHtml()
                . $tree->getBreadcrumbsJavascript($this->getBreadcrumbPath($category), 'editingCategoryBreadcrumbs')
            );
            return;
        }

        $this->_redirect('*/*/index');
    }

    protected function getBreadcrumbPath(Mage_Catalog_Model_Category $category)
    {
        $breadcrumbPath = $category->getPath();
        if (empty($breadcrumbPath)) {
            // but if no category, and it is deleted - prepare breadcrumbs from path, saved in session
            $breadcrumbPath = $this->getAdminSession()->getDeletedPath(true);
            if (!empty($breadcrumbPath)) {
                $breadcrumbPath = explode('/', $breadcrumbPath);
                // no need to get parent breadcrumbs if deleting category level 1
                if (count($breadcrumbPath) <= 1) {
                    $breadcrumbPath = '';
                } else {
                    array_pop($breadcrumbPath);
                    $breadcrumbPath = implode('/', $breadcrumbPath);
                }
            }
        }

        return $breadcrumbPath;
    }

    public function saveAction()
    {
        $post = $this->getRequest()->getPost();
        $storeId = $post['storeId'];

        try {
            if (empty($post)) {
                Mage::throwException($this->__('Invalid form data.'));
            }

            if (isset($post['fianet_product_type_id'])) {
                if ($post['fianet_product_type_id'] != "" && $post['fianet_product_type_id'] != "0") {
                    $this->saveCategoryMapping($post['category_id'], $post['fianet_product_type_id']);
                    $message = $this->__('Data successfully saved.');
                    $this->_getSession()->addSuccess($message);
                }

                if (isset($post['apply_to_sub_categories'])) {
                    /** @var Mage_Catalog_Model_Category $category */
                    $category = Mage::getModel('catalog/category')->load($post['category_id']);
                    $subcat = $category->getAllChildren(true);
                    foreach ($subcat as $categoryId) {
                        $this->saveCategoryMapping($categoryId, $post['fianet_product_type_id']);
                    }
                }
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect(
            '*/*/index/',
            array(
                '_current' => true,
                'id' => $post['category_id'],
                'store' => $storeId,
            )
        );
    }

    protected function saveCategoryMapping($categoryId, $fianetProductTypeId)
    {
        /** @var Fianet_Payment3xcb_Model_Mapping_Catalog_Category $mapping */
        $mapping = Mage::getModel('fianetpayment3xcb/mapping_catalog_category');
        $mapping = $mapping->loadByCategoryId($categoryId);
        if ($mapping->getId() > 0) {
            $mapping->delete();
            $mapping = Mage::getModel('fianetpayment3xcb/mapping_catalog_category');
        }

        $mapping->setCategoryId($categoryId)
            ->setFianetProductTypeId($fianetProductTypeId)
            ->save();
        return $mapping;
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id', 0);
        $storeId = $this->getRequest()->getParam('store', 0);
        try {
            /** @var Fianet_Payment3xcb_Model_Mapping_Catalog_Category $mapping */
            $mapping = Mage::getModel('fianetpayment3xcb/mapping_catalog_category');
            $mapping->loadByCategoryId($id)
                ->delete();
            $message = $this->__('Data successfully deleted.');
            $this->_getSession()->addSuccess($message);
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect(
            '*/*/index/',
            array(
                'id' => $id,
                'store' => $storeId,
            )
        );
    }
}
