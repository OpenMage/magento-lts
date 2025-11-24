<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
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
