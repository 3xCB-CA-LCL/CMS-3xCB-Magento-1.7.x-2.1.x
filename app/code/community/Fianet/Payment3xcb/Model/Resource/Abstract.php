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

abstract class Fianet_Payment3xcb_Model_Resource_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    public function save(Mage_Core_Model_Abstract $object)
    {
        if ($object->isDeleted()) {
            return $this->delete($object);
        }

        $this->_beforeSave($object);
        $this->_checkUnique($object);

        if ($object->getId() !== null || $object->getId() === 0) {
            $condition = $this->_getWriteAdapter()->quoteInto($this->getIdFieldName() . '=?', $object->getId());
            if ($this->_exist($object)) {
                $this->_getWriteAdapter()->update(
                    $this->getMainTable(),
                    $this->_prepareDataForSave($object),
                    $condition
                );
            } else {
                $this->_getWriteAdapter()->insert($this->getMainTable(), $this->_prepareDataForSave($object));
            }
        } else {
            $this->_getWriteAdapter()->insert($this->getMainTable(), $this->_prepareDataForSave($object));
            $object->setId($this->_getWriteAdapter()->lastInsertId($this->getMainTable()));
        }

        $this->_afterSave($object);

        return $this;
    }

    protected function _exist(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId() === null) {
            return (false);
        }

        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable())
            ->reset(Zend_Db_Select::WHERE)
            ->where($this->getIdFieldName() . ' = ?', $object->getId());

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return (true);
        }

        return (false);
    }
}
