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

class Fianet_Payment3xcb_Model_Fianet_Result
{
    /**
     * @var string
     */
    protected $_body;

    /**
     * @var mixed
     */
    protected $_data;

    /**
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if (!isset($this->_data)) {
            $this->_data = Zend_Json::decode($this->_body);
        }

        return $this->_data;
    }

    /**
     * @param string $path
     * @param null $defaultValue
     * @return mixed
     */
    public function get($path, $defaultValue = null)
    {
        return $this->getValue($this->toArray(), explode('/', $path), $defaultValue);
    }

    /**
     * @param array $data
     * @param array $indexes
     * @param mixed $defaultValue
     * @return mixed
     */
    protected function getValue($data, $indexes, $defaultValue)
    {
        $index = $indexes[0];
        if (count($indexes) > 1) {
            return $this->getValue($data[$index], array_slice($indexes, 1), $defaultValue);
        } else {
            return isset($data[$index]) ? $data[$index] : $defaultValue;
        }
    }

    /**
     * @return string
     */
    public function toString()
    {
        return (string) $this->_body;
    }
}
