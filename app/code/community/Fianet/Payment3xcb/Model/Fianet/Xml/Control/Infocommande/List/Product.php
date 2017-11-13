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

class Fianet_Payment3xcb_Model_Fianet_Xml_Control_Infocommande_List_Product
    extends Fianet_Payment3xcb_Model_Fianet_Xml_Node
{
    /**
     * @var Mage_Sales_Model_Order_Item
     */
    protected $_item;

    public function __construct()
    {
        parent::__construct(/** @lang XML */'<produit/>');
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return $this
     */
    public function setItem(Mage_Sales_Model_Order_Item $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * @return $this
     */
    public function load()
    {
        $item = $this->_item;
        $productName = $item->getName();
        $productSku = $item->getSku();
        if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            /** @var Mage_Sales_Model_Order_Item[] $childrenItems */
            $childrenItems = $item->getChildrenItems();
            if (count($childrenItems) == 1) {
                $productName = $childrenItems[0]->getName();
                $productSku = $childrenItems[0]->getSku();
            }
        }

        /** @var Fianet_Payment3xcb_Model_Product $product */
        $product = Mage::getModel('fianetpayment3xcb/product')->load($item->getProductId());
        $this->setContent($productName)
            ->setAttribute(
                'type',
                $product->getFianetProductTypeId()
            )
            ->setAttribute(
                'ref',
                trim(str_replace("'", "`", $productSku))
            )
            ->setAttribute(
                'nb',
                (int) $item->getQtyOrdered()
            );

        return $this;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        if ($this->getValue('@type') === null) {
            $this->reportError(
                Mage::helper('fianetpayment3xcb')->__('Product `type` is undefined')
            );
        }

        if ($this->getValue('@ref') === null) {
            $this->reportError(
                Mage::helper('fianetpayment3xcb')->__('Product `ref` is undefined')
            );
        }

        if ($this->getValue('.') === null) {
            $this->reportError(
                Mage::helper('fianetpayment3xcb')->__('Product name is undefined')
            );
        }

        return parent::validate();
    }
}
