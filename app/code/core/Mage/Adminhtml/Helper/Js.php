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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml JavaScript helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Js extends Mage_Core_Helper_Js
{
    /**
     * Decode serialized grid data
     *
     * Ignores non-numeric array keys
     *
     * '1&2&3&4' will be decoded into:
     * array(1, 2, 3, 4);
     *
     * otherwise the following format is anticipated:
     * 1=<encoded string>&2=<encoded string>:
     * array (
     *   1 => array(...),
     *   2 => array(...),
     * )
     *
     * @param   string $encoded
     * @return  array
     */
    public function decodeGridSerializedInput($encoded)
    {
        $isSimplified = (strpos($encoded, '=') === false);
        $result = [];
        parse_str($encoded, $decoded);
        foreach($decoded as $key => $value) {
            if (is_numeric($key)) {
                if ($isSimplified) {
                    $result[] = $key;
                } else {
                    $result[$key] = null;
                    parse_str(base64_decode($value), $result[$key]);
                }
            }
        }
        return $result;
    }
}
