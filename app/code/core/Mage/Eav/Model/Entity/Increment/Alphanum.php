<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity_Increment_Alphanum extends Mage_Eav_Model_Entity_Increment_Abstract
{
    /**
     * @return string
     */
    public function getAllowedChars()
    {
        return '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }

    /**
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getNextId()
    {
        $lastId = $this->getLastId();

        if (str_starts_with($lastId, $this->getPrefix())) {
            $lastId = substr($lastId, strlen($this->getPrefix()));
        }

        $lastId = str_pad((string) $lastId, $this->getPadLength(), $this->getPadChar(), STR_PAD_LEFT);

        $nextId = '';
        $bumpNextChar = true;
        $chars = $this->getAllowedChars();
        $lchars = strlen($chars);
        $lid = strlen($lastId) - 1;

        for ($i = $lid; $i >= 0; $i--) {
            $p = strpos($chars, $lastId[$i]);
            if ($p === false) {
                throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid character encountered in increment ID: %s', $lastId));
            }
            if ($bumpNextChar) {
                $p++;
                $bumpNextChar = false;
            }
            if ($p === $lchars) {
                $p = 0;
                $bumpNextChar = true;
            }
            $nextId = $chars[$p] . $nextId;
        }

        return $this->format($nextId);
    }
}
