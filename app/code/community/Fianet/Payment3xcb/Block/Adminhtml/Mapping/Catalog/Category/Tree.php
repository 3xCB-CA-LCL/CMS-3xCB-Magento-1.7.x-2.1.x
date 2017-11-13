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

class Fianet_Payment3xcb_Block_Adminhtml_Mapping_Catalog_Category_Tree
    extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    /**
     * Use __construct and not _construct
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('fianetpayment3xcb/mapping/catalog/category/tree.phtml');
    }

    public function getSwitchTreeUrl()
    {
        return $this->getUrl(
            '*/*/tree',
            array(
                '_current' => true,
                'store' => null,
                '_query' => false,
                'id' => null,
                'parent' => null,
            )
        );
    }

    public function getNodesUrl()
    {
        return $this->getUrl('fianetpayment3xcb/adminhtml_mapping_catalog_category/jsonTree');
    }

    public function getEditUrl()
    {
        return $this->getUrl('*/*/edit', array('_current' => true, '_query' => false, 'id' => null, 'parent' => null));
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @param Varien_Data_Tree_Node $node
     * @param int $level
     * @return string
     */
    protected function _getNodeJson($node, $level = 0)
    {
        $item = array();
        $item['text'] = $this->buildNodeName($node);
        $item['id'] = $node->getId();
        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        $item['store'] = (int) $this->getStore()->getId();
        $item['path'] = $node->getData('path');
        $item['allowDrop'] = false;
        $item['allowDrag'] = false;
        if ((int) $node->getChildrenCount() > 0) {
            $item['children'] = array();
        }

        $isParent = $this->_isParentSelectedCategory($node);
        if ($node->hasChildren()) {
            $item['children'] = array();
            if (!($this->getUseAjax() && $node->getLevel() > 1 && !$isParent)) {
                foreach ($node->getChildren() as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level + 1);
                }
            }
        }

        if ($isParent || $node->getLevel() < 2) {
            $item['expanded'] = true;
        }

        return $item;
    }

    protected function getFianetProductTypeName($fianetProductTypeId)
    {
        /** @var Fianet_Payment3xcb_Model_Source_Fianet_ProductType $source */
        $source = Mage::getModel('fianetpayment3xcb/source_fianet_productType');
        $fianetProductTypes = $source->toOptionArray();
        foreach ($fianetProductTypes as $fianetProductType) {
            if ($fianetProductType['value'] == $fianetProductTypeId) {
                return $fianetProductType['label'];
            }
        }

        return '';
    }

    /**
     * @param Varien_Data_Tree_Node $node
     * @return string
     */
    public function buildNodeName($node)
    {
        $result = $this->escapeHtml($node->getName());
        /** @var Fianet_Payment3xcb_Model_Mapping_Catalog_Category $categoryMapping */
        $categoryMapping = Mage::getModel('fianetpayment3xcb/mapping_catalog_category');
        $categoryMapping = $categoryMapping->loadByCategoryId($node->getEntityId());
        if ($categoryMapping->getId() > 0) {
            $result .= ' ('
                . Fianet_Payment3xcb_Helper_Data::initials(
                    $this->getFianetProductTypeName($categoryMapping->getFianetProductTypeId())
                )
                . ')';
        }

        return $result;
    }
}
