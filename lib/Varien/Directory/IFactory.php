<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Directory
 */

/**
 * Factory Interface for Directory
 * *
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
