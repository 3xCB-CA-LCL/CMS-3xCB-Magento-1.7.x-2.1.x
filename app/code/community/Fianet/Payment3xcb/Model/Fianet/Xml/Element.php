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

class Fianet_Payment3xcb_Model_Fianet_Xml_Element extends Varien_Simplexml_Element
{
    /**
     * Appends $source to current node
     *
     * @param Varien_Simplexml_Element $source
     * @return SimpleXMLElement
     */
    public function appendChild($source)
    {
        if ($source->children()) {
            /**
             * @see http://bugs.php.net/bug.php?id=41867 , fixed in 5.2.4
             */
            if (version_compare(phpversion(), '5.2.4', '<')===true) {
                $name = $source->children()->getName();
            } else {
                $name = $source->getName();
            }

            $child = $this->addChild($name);
        } else {
            $child = $this->addChild($source->getName(), $this->xmlentities($source));
        }

        /** @var Varien_Simplexml_Element $child */
        $child->setParent($this);

        $attributes = $source->attributes();
        foreach ($attributes as $key=>$value) {
            $child->addAttribute($key, $this->xmlentities($value));
        }

        foreach ($source->children() as $sourceChild) {
            $child->appendChild($sourceChild);
        }

        return $child;
    }

    /**
     * @param string $input
     * @return string
     */
    protected function cleanValue($input)
    {
        return $input;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setAttribute($name, $value)
    {
        $this[$name] = $value;
        return $this;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this[0] = $this->cleanValue($content);
        return $this;
    }
}
