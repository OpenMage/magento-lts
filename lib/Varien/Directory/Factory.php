<?php

/**
 * @category   Varien
 * @package    Varien_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory Factory
 * *
 * @category   Varien
 * @package    Varien_Directory
 */

require_once('Varien/Directory/Collection.php');
require_once('Varien/File/Object.php');

class Varien_Directory_Factory
{
    /**
     * return or Varien_Directory_Collection or Varien_File_Object object
     *
     * @param   array $path - path to directory
     * @param   bool $is_recursion - use or not recursion
     * @param   int $recurse_level - recurse level
     * @return  IFactor - Varien_Directory_Collection or Varien_File_Object object
     */
    public static function getFactory($path, $is_recursion = true, $recurse_level = 0)
    {
        if (is_dir($path)) {
            return new Varien_Directory_Collection($path, $is_recursion, $recurse_level + 1);
        } else {
            return new Varien_File_Object($path);
        }
    }
}
