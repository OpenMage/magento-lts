<?php

/**
 * @category   Varien
 * @package    Varien_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Factory Interface for Directory
 * *
 * @category   Varien
 * @package    Varien_Directory
 */

interface IFactory
{
    public function getFilesName(&$files);
    public function getFilesPaths(&$paths);
    public function getFilesObj(&$objs);
    public function useFilter($useFilter);
    public function getDirsName(&$dirs);
    public function toArray(&$arr);
    public function toXml(&$xml, $recursionLevel = 0, $addOpenTag = true, $rootName = 'Struct');
}
