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

class Fianet_Payment3xcb_Model_Fianet_Checksum
{
    /**
     * @param array $data
     * @return string
     */
    public function getHash($data)
    {
        // String construction: name1=value1 + & + name2=value2 + & + name3=value3
        $toHash = array();
        foreach ($data as $name => $value) {
            $toHash[] = $name . '=' . urlencode($value);
        }

        $toHash = implode('&', $toHash);
        $privateSharedKey = Mage::getStoreConfig('payment/fianet3xcb/secret_key');
        return hash_hmac('sha512', $toHash, $privateSharedKey);
    }
}
