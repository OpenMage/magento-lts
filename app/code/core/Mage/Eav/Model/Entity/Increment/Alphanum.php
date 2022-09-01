<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * Properties:
 * - prefix
 * - pad_length
 * - pad_char
 * - last_id
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

        if (strpos($lastId, $this->getPrefix())===0) {
            $lastId = substr($lastId, strlen($this->getPrefix()));
        }

        $lastId = str_pad((string)$lastId, $this->getPadLength(), $this->getPadChar(), STR_PAD_LEFT);

        $nextId = '';
        $bumpNextChar = true;
        $chars = $this->getAllowedChars();
        $lchars = strlen($chars);
        $lid = strlen($lastId)-1;

        for ($i = $lid; $i >= 0; $i--) {
            $p = strpos($chars, $lastId[$i]);
            if ($p === false) {
                throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid character encountered in increment ID: %s', $lastId));
            }
            if ($bumpNextChar) {
                $p++;
                $bumpNextChar = false;
            }
            if ($p===$lchars) {
                $p = 0;
                $bumpNextChar = true;
            }
            $nextId = $chars[$p] . $nextId;
        }

        return $this->format($nextId);
    }
}
