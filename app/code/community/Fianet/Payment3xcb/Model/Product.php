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

class Fianet_Payment3xcb_Model_Product extends Mage_Catalog_Model_Product
{
    public function getFianetProductTypeId()
    {
        // TODO: use helper? requires getCategoryCollection method
        $result = null;

        $categoriesCollection = $this->getCategoryCollection();
        $configuredCategories = Mage::getModel('fianetpayment3xcb/mapping_catalog_category')
            ->getCollection()
            ->getConfiguredCategoriesCollection();

        $list = array();
        foreach ($categoriesCollection as $category) {
            $id = $category->getId();
            if (isset($configuredCategories[$id])) {
                $list[$id] = $configuredCategories[$id];
            }
        }

        $count = array();
        $max = 0;
        foreach ($list as $catId => $fianetProductTypeId) {
            $count[$fianetProductTypeId] = isset($count[$fianetProductTypeId]) ? $count[$fianetProductTypeId] + 1 : 1;
            if ($count[$fianetProductTypeId] > $max) {
                $max = $count[$fianetProductTypeId];
                $result = $fianetProductTypeId;
            }
        }

        return $result;
    }
}
