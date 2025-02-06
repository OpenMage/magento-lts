<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Core unserialize helper
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Helper_UnserializeArray
{
    /**
     * @param string $str
     * @return array
     * @throws Exception
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function unserialize($str)
    {
        try {
            $str = is_null($str) ? '' : $str;
            $result = @unserialize($str, ['allowed_classes' => false]);
            if ($result === false && $str !== serialize(false)) {
                throw new Exception('Error unserializing data.');
            }
            return $result;
        } catch (Error $e) {
            throw new Exception('Error unserializing data: ' . $e->getMessage(), 0, $e);
        }
    }
}
