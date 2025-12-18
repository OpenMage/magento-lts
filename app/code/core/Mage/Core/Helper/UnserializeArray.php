<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Core unserialize helper
 *
 * @package    Mage_Core
 */
class Mage_Core_Helper_UnserializeArray
{
    /**
     * @param  string    $str
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
        } catch (Error $error) {
            throw new Exception('Error unserializing data: ' . $error->getMessage(), 0, $error);
        }
    }
}
