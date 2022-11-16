<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Varien
 * @package    Varien_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory Factory
 * *
 * @category   Varien
 * @package    Varien_Directory
 * @author     Magento Core Team <core@magentocommerce.com>
 */

require_once("Varien/Directory/Collection.php");
require_once("Varien/File/Object.php");

class Varien_Directory_Factory
{
    /**
     * return or Varien_Directory_Collection or Varien_File_Object object
     *
     * @param   array $path - path to direcctory
     * @param   bool $is_recursion - use or not recursion
     * @param   int $recurse_level - recurse level
     * @return  IFactor - Varien_Directory_Collection or Varien_File_Object object
     */
    public static function getFactory($path, $is_recursion = true, $recurse_level = 0)
    {
        if (is_dir($path)) {
            $obj = new Varien_Directory_Collection($path, $is_recursion, $recurse_level + 1);
            return $obj;
        } else {
            return new Varien_File_Object($path);
        }
    }
}
