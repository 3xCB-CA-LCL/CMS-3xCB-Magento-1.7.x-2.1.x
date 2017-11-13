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

class Fianet_Payment3xcb_Model_Fianet_Xml_Control_Infocommande_List extends Fianet_Payment3xcb_Model_Fianet_Xml_Node
{
    public function __construct()
    {
        parent::__construct(/** @lang XML */'<list/>');
    }

    /**
     * @return $this
     */
    public function load()
    {
        $order = $this->getOrder();
        $items = $order->getAllVisibleItems();

        $itemsCount = 0;
        foreach ($items as $item) {
            /** @var Fianet_Payment3xcb_Model_Fianet_Xml_Control_Infocommande_List_Product $node */
            $node = $this->appendChildNode('control_infocommande_list_product');
            $node->setItem($item)->init();
            $itemsCount += (int) $node->getValue('@nb');
        }

        $this->setAttribute('nbproduit', $itemsCount);

        return $this;
    }
}
