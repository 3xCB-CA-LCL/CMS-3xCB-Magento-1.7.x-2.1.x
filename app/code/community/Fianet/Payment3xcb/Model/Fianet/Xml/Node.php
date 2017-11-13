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

class Fianet_Payment3xcb_Model_Fianet_Xml_Node
{
    /**
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * @var Fianet_Payment3xcb_Model_Fianet_Xml_Node
     */
    protected $_rootNode;

    /**
     * @var Fianet_Payment3xcb_Model_Fianet_Xml_Element
     */
    protected $_element;

    /**
     * @var Fianet_Payment3xcb_Model_Fianet_Xml_Node[]
     */
    protected $_childNodes;

    public function __construct($data)
    {
        $this->_childNodes = array();
        $this->_element = new Fianet_Payment3xcb_Model_Fianet_Xml_Element($data);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return $this
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * @param Fianet_Payment3xcb_Model_Fianet_Xml_Node $rootNode
     * @return $this
     */
    public function setRootNode(Fianet_Payment3xcb_Model_Fianet_Xml_Node $rootNode)
    {
        $this->_rootNode = $rootNode;
        return $this;
    }

    /**
     * @return Fianet_Payment3xcb_Model_Fianet_Xml_Node
     */
    public function getRootNode()
    {
        return $this->_rootNode;
    }

    /**
     * @param SimpleXMLElement $element
     * @return $this
     */
    public function setElement(SimpleXMLElement $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * @return SimpleXMLElement
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * @return Fianet_Payment3xcb_Helper_Data
     */
    public function getHelper()
    {
        /** @var Fianet_Payment3xcb_Helper_Data $helper */
        $helper = Mage::helper('fianetpayment3xcb');
        return $helper;
    }

    /**
     * @return $this
     */
    public function load()
    {
        // Do nothing
        return $this;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        // Do nothing
        return true;
    }

    /**
     * @return $this
     */
    public function init()
    {
        $this->load();
        $this->validate();
        return $this;
    }

    /**
     * @param string $telephone
     * @return string
     */
    protected function cleanTelephoneNumber($telephone)
    {
        return preg_replace('/[^0-9]/', '', $telephone);
    }

    /**
     * @param string $name
     * @return Fianet_Payment3xcb_Model_Fianet_Xml_Node
     */
    public function getChild($name)
    {
        if (!isset($this->getElement()->$name)) {
            return $this->createGenericNode($name)
                ->bindToParent($this);
        } else {
            return $this->createGenericNode($name)
                ->setElement($this->getElement()[0]->$name);
        }
    }

    /**
     * @param Fianet_Payment3xcb_Model_Fianet_Xml_Node $parentNode
     * @return $this
     */
    protected function bindToParent(Fianet_Payment3xcb_Model_Fianet_Xml_Node $parentNode)
    {
        $this->_element = $parentNode->_element->appendChild($this->_element);
        return $this;
    }

    /**
     * @param string $path
     * @return null|SimpleXMLElement
     */
    public function getValue($path)
    {
        return $this->getFirstXpathResult($path);
    }

    /**
     * @param string $path
     * @return null|SimpleXMLElement
     */
    public function getFirstXpathResult($path)
    {
        $result = $this->_element->xpath($path);
        return $result ? $result[0] : null;
    }

    /**
     * @return string
     */
    public function asXml()
    {
        return $this->_element->asXml();
    }

    /**
     * @return string
     */
    public function asNiceXml()
    {
        return $this->_element->asNiceXml();
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    protected function setAttribute($name, $value)
    {
        $this->_element->setAttribute($name, $value);
        return $this;
    }

    /**
     * @param string $content
     * @return $this
     */
    protected function setContent($content)
    {
        $this->_element->setContent($content);
        return $this;
    }

    /**
     * @param string $message
     */
    protected function reportError($message)
    {
        $prefix = str_replace(array('Fianet_Payment3xcb_Model_Fianet_Xml_', '_'), array('/', '/'), get_class($this));
        Mage::throwException(
            '[XML ' . strtolower($prefix) . '] ' . $message
        );
    }

    /**
     * @param string $nodeName
     * @return Fianet_Payment3xcb_Model_Fianet_Xml_Node
     */
    protected function appendChildNode($nodeName)
    {
        $node = $this->createNode($nodeName)
            ->bindToParent($this);
        $this->_childNodes[$nodeName] = $node;
        return $node;
    }

    /**
     * @param string $nodeName
     * @return Fianet_Payment3xcb_Model_Fianet_Xml_Node|null
     */
    protected function getChildNode($nodeName)
    {
        return isset($this->_childNodes[$nodeName]) ? $this->_childNodes[$nodeName] : null;
    }

    /**
     * @param string $nodeName
     * @return Fianet_Payment3xcb_Model_Fianet_Xml_Node
     */
    protected function createNode($nodeName)
    {
        /** @var Fianet_Payment3xcb_Model_Fianet_Xml_Node $node */
        $node = Mage::getModel('fianetpayment3xcb/fianet_xml_' . $nodeName);
        return $this->initNode($node);
    }

    /**
     * @param string $elementName
     * @return Fianet_Payment3xcb_Model_Fianet_Xml_Node
     */
    protected function createGenericNode($elementName)
    {
        /** @var Fianet_Payment3xcb_Model_Fianet_Xml_Node $node */
        $node = Mage::getModel('fianetpayment3xcb/fianet_xml_node', "<$elementName/>");
        return $this->initNode($node);
    }

    /**
     * @param Fianet_Payment3xcb_Model_Fianet_Xml_Node $node
     * @return Fianet_Payment3xcb_Model_Fianet_Xml_Node
     */
    protected function initNode(Fianet_Payment3xcb_Model_Fianet_Xml_Node $node)
    {
        $node->setOrder($this->_order);
        $node->setRootNode($this->_rootNode);
        return $node;
    }
}
