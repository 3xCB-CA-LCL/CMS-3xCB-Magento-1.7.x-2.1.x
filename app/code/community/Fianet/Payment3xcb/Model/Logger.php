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

class Fianet_Payment3xcb_Model_Logger
{
    const UNKNOWN = 0;
    const DEBUG = 100;
    const NOTICE = 250;
    const WARNING = 300;
    const ERROR = 400;

    protected $_constants;
    protected $_event = '';

    /**
     * @return array
     */
    protected function getConstants()
    {
        if (!isset($this->_constants)) {
            $this->_constants = array();
            $reflectionClass = new ReflectionClass(__CLASS__);
            foreach ($reflectionClass->getConstants() as $constantName => $constantValue) {
                $this->_constants[$constantValue] = $constantName;
            }
        }

        return $this->_constants;
    }

    /**
     * @param string $level
     * @return string
     */
    protected function getLevelName($level)
    {
        $constants = $this->getConstants();
        return isset($constants[$level]) ? $constants[$level] : 'UNKNOWN';
    }

    /**
     * @return array
     */
    public function getLevels()
    {
        $constants = $this->getConstants();
        $levels = array();
        foreach ($constants as $constantName) {
            $levels[$constantName] = $constantName;
        }

        return $levels;
    }

    /**
     * @param string $event
     * @return $this
     */
    public function setEvent($event)
    {
        $this->_event = $event;
        return $this;
    }

    /**
     * @param string $level
     * @param string $message
     * @return bool
     */
    public function log($level, $message)
    {
        /** @var Fianet_Payment3xcb_Model_Log $log */
        $log = Mage::getModel('fianetpayment3xcb/log');
        /** @var Mage_Core_Model_Date $date */
        $date = Mage::getSingleton('core/date');
        $log->setCreatedAt($date->gmtDate())
            ->setLevel($this->getLevelName($level))
            ->setEvent($this->_event)
            ->setMessage($message)
            ->save();
        return (bool) $log->getId();
    }

    /**
     * @param string $message
     * @return bool
     */
    public function debug($message)
    {
        if (Mage::getStoreConfig('payment/fianet3xcb/debug')) {
            return $this->log(self::DEBUG, $message);
        }

        return false;
    }

    /**
     * @param string $message
     * @return bool
     */
    public function notice($message)
    {
        return $this->log(self::NOTICE, $message);
    }

    /**
     * @param string $message
     * @return bool
     */
    public function warning($message)
    {
        return $this->log(self::WARNING, $message);
    }

    /**
     * @param string $message
     * @return bool
     */
    public function error($message)
    {
        return $this->log(self::ERROR, $message);
    }
}
