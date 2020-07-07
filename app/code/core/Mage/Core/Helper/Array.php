<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Core_Helper_Array extends Mage_Core_Helper_Abstract
{
    /**
     * Merge array recursive without overwrite keys.
     * PHP function array_merge_recursive merge array
     * with overwrite num keys
     *
     * @param array $baseArray
     * @param array $mergeArray
     * @return array
     */
    public function mergeRecursiveWithoutOverwriteNumKeys(array $baseArray, array $mergeArray)
    {
        foreach ($mergeArray as $key => $value) {
            if (is_array($value)) {
                if (array_key_exists($key, $baseArray)) {
                    $baseArray[$key] = $this->mergeRecursiveWithoutOverwriteNumKeys($baseArray[$key], $value);
                } else {
                    $baseArray[$key] = $value;
                }
            } else {
                if ($key) {
                    $baseArray[$key] = $value;
                } else {
                    $baseArray[] = $value;
                }
            }
        }

        return $baseArray;
    }
}
