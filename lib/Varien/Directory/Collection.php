<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Varien Directory Collection
 * *
 * @category   Varien
 * @package    Varien_Directory
 */

require_once('Varien/Data/Collection.php');
require_once('Varien/Directory/Factory.php');
require_once('Varien/Directory/IFactory.php');

class Varien_Directory_Collection extends Varien_Data_Collection implements IFactory
{
    protected $_path = '';
    protected $_dirName = '';
    protected $_recursionLevel = 0;
    protected $_isRecursion;
    protected $_filters = [];
    /**
     * Constructor
     *
     * @param   string $path - path to directory
     * @param   bool $is_recursion - use or not recursion
     * @return  none
     */
    public function __construct($path, $isRecursion = true, $recursionLevel = 0)
    {
        parent::__construct();
        $this->setPath($path);
        $this->_dirName = $this->lastDir();
        $this->setRecursion($isRecursion);
        $this->setRecursionLevel($recursionLevel);
        if ($this->getRecursion() || $this->getRecursionLevel() == 0) {
            $this->parseDir();
        }
    }
    /**
     * Get name of this directory
     *
     * @return  string - name of this directory
     */
    public function getDirName()
    {
        return $this->_dirName;
    }
    /**
     * Get recursion
     *
     * @return  bool - is or not recursion
     */
    public function getRecursion()
    {
        return $this->_isRecursion;
    }
    /**
     * Get recursion level
     *
     * @return  int - recursion level
     */
    public function getRecursionLevel()
    {
        return $this->_recursionLevel;
    }
    /**
     * Get path
     *
     * @return  string - path to this directory
     */
    public function getPath()
    {
        return $this->_path;
    }
    /**
     * Set path to this directory
     * @param   string $path - path to this directory
     * @param   bool $isRecursion - use or not recursion
     * @return  none
     */
    public function setPath($path, $isRecursion = '')
    {
        if (is_dir($path)) {
            if (isset($this->_path) && $this->_path != $path && $this->_path != '') {
                $this->_path = $path;
                if ($isRecursion != '') {
                    $this->_isRecursion = $isRecursion;
                }
                $this->parseDir();
            } else {
                $this->_path = $path;
            }
        } else {
            throw new Exception($path . 'is not dir.');
        }
    }
    /**
     * Set recursion
     *
     * @param   bool $isRecursion - use or not recursion
     * @return  none
     */
    public function setRecursion($isRecursion)
    {
        $this->_isRecursion = $isRecursion;
    }
    /**
     * Set level of recursion
     *
     * @param   int $recursionLevel - level of recursion
     * @return  none
     */
    public function setRecursionLevel($recursionLevel)
    {
        $this->_recursionLevel = $recursionLevel;
    }
    /**
     * get latest dir in the path
     *
     * @param   string $path - path to directory
     * @return  string - latest dir in the path
     */
    public function lastDir()
    {
        return self::getLastDir($this->getPath());
    }
    /**
     * get latest dir in the path
     *
     * @param   string $path - path to directory
     * @return  string - latest dir in the path
     */
    public static function getLastDir($path)
    {
        $last = strrpos($path, '/');
        return substr($path, $last + 1);
    }
    /**
     * add item to collection
     *
     * @param   IFactory $item - item of collection
     * @return  none
     */
    public function addItem(IFactory $item)
    {
        $this->_items[] = $item;
    }
    /**
     * parse this directory
     *
     * @return  none
     */
    protected function parseDir()
    {
        $this->clear();
        $iter = new RecursiveDirectoryIterator($this->getPath());
        while ($iter->valid()) {
            $curr = (string) $iter->getSubPathname();
            if (!$iter->isDot() && $curr[0] != '.') {
                $this->addItem(Varien_Directory_Factory::getFactory($iter->current(), $this->getRecursion(), $this->getRecursionLevel()));
            }
            $iter->next();
        }
    }
    /**
     * set filter using
     *
     * @param   bool $useFilter - filter using
     * @return  none
     */
    public function useFilter($useFilter)
    {
        $this->_renderFilters();
        $this->walk('useFilter', [$useFilter]);
    }
    /**
     * get files names of current collection
     *
     * @return  array - files names of current collection
     */
    public function filesName()
    {
        $files = [];
        $this->getFilesName($files);
        return $files;
    }
    /**
     * get files names of current collection
     *
     * @param   array $files - array of files names
     * @return  none
     */
    public function getFilesName(&$files)
    {
        $this->walk('getFilesName', [&$files]);
    }
    /**
     * get files paths of current collection
     *
     * @return  array - files paths of current collection
     */
    public function filesPaths()
    {
        $paths = [];
        $this->getFilesPaths($paths);
        return $paths;
    }
    /**
     * get files paths of current collection
     *
     * @param   array $files - array of files paths
     * @return  none
     */
    public function getFilesPaths(&$paths)
    {
        $this->walk('getFilesPaths', [&$paths]);
    }
    /**
     * get SplFileObject objects of files of current collection
     *
     * @return  array - array of SplFileObject objects
     */
    public function filesObj()
    {
        $objs = [];
        $this->getFilesObj($objs);
        return $objs;
    }
    /**
     * get SplFileObject objects of files of current collection
     *
     * @param   array $objs - array of SplFileObject objects
     * @return  none
     */
    public function getFilesObj(&$objs)
    {
        $this->walk('getFilesObj', [&$objs]);
    }
    /**
     * get names of dirs of current collection
     *
     * @return  array - array of names of dirs
     */
    public function dirsName()
    {
        $dir = [];
        $this->getDirsName($dir);
        return $dir;
    }
    /**
     * get names of dirs of current collection
     *
     * @param   array $dirs - array of names of dirs
     * @return  none
     */
    public function getDirsName(&$dirs)
    {
        $this->walk('getDirsName', [&$dirs]);
        if ($this->getRecursionLevel() > 0) {
            $dirs[] = $this->getDirName();
        }
    }
    /**
     * set filters for files
     *
     * @param   array $filter - array of filters
     * @return  none
     */
    protected function setFilesFilter($filter)
    {
        $this->walk('setFilesFilter', [$filter]);
    }
    /**
     * display this collection as array
     *
     * @return  array
     */
    public function __toArray()
    {
        $arr = [];
        $this->toArray($arr);
        return $arr;
    }
    /**
     * display this collection as array
     * @param   array &$arr - this collection array
     * @return  none
     */
    public function toArray(&$arr)
    {
        if ($this->getRecursionLevel() > 0) {
            $arr[$this->getDirName()] = [];
            $this->walk('toArray', [&$arr[$this->getDirName()]]);
        } else {
            $this->walk('toArray', [&$arr]);
        }
    }
    /**
     * get this collection as xml
     * @param   bool $addOpenTag - add or not header of xml
     * @param   string $rootName - root element name
     * @return  none
     */
    public function __toXml($addOpenTag = true, $rootName = 'Struct')
    {
        $xml = '';
        $this->toXml($xml, $addOpenTag, $rootName);
        return $xml;
    }
    /**
     * get this collection as xml
     * @param   string &$xml - xml
     * @param   bool $addOpenTag - add or not header of xml
     * @param   string $rootName - root element name
     * @return  none
     */
    public function toXml(&$xml, $recursionLevel = 0, $addOpenTag = true, $rootName = 'Struct')
    {
        if ($recursionLevel == 0) {
            $xml = '';
            if ($addOpenTag) {
                $xml .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            }
            $xml .= '<' . $rootName . '>' . "\n";
        }
        $recursionLevel = $this->getRecursionLevel();
        $xml .= str_repeat("\t", $recursionLevel + 1) . "<$this->_dirName>\n";
        $this->walk('toXml', [&$xml,$recursionLevel,$addOpenTag,$rootName]);
        $xml .= str_repeat("\t", $recursionLevel + 1) . "</$this->_dirName>" . "\n";
        if ($recursionLevel == 0) {
            $xml .= '</' . $rootName . '>' . "\n";
        }
    }
    /**
     * apply filters
     * @return  none
     */
    protected function _renderFilters()
    {
        $exts = [];
        $names = [];
        $regName = [];
        foreach ($this->_filters as $filter) {
            switch ($filter['field']) {
                case 'extension':
                    if (is_array($filter['value'])) {
                        foreach ($filter['value'] as $value) {
                            $exts[] = $value;
                        }
                    } else {
                        $exts[] = $filter['value'];
                    }
                    break;
                case 'name':
                    if (is_array($filter['value'])) {
                        foreach ($filter['value'] as $value) {
                            $names[] = $filter['value'];
                        }
                    } else {
                        $names[] = $filter['value'];
                    }
                    break;
                case 'regName':
                    if (is_array($filter['value'])) {
                        foreach ($filter['value'] as $value) {
                            $regName[] = $filter['value'];
                        }
                    } else {
                        $regName[] = $filter['value'];
                    }
                    break;
            }
        }
        $filter = [];
        if (count($exts) > 0) {
            $filter['extension'] = $exts;
        } else {
            $filter['extension'] = null;
        }
        if (count($names) > 0) {
            $filter['name'] = $names;
        } else {
            $filter['name'] = null;
        }
        if (count($regName) > 0) {
            $filter['regName'] = $regName;
        } else {
            $filter['regName'] = null;
        }
        $this->setFilesFilter($filter);
    }
    /**
     * add filter
     * @return  none
     */
    public function addFilter($field, $value)
    {
        $filter = [];
        $filter['field']   = $field;
        $filter['value']   = $value;
        $this->_filters[] = $filter;
        $this->_isFiltersRendered = false;
        $this->walk('addFilter', [$field, $value]);
        return $this;
    }
}

/* Example */
/*
 $a = new Varien_Directory_Collection('/usr/home/vasily/dev/magento/lib',false);

 $a->addFilter("extension","php");

 $a->useFilter(true);

 print "-----------------------\n";
 print_r($a->filesName());

 $a->setPath('/usr/home/vasily/dev/magento/lib/Varien/Image',true);
 $a->useFilter(true);

 print "-----------------------\n";
 print_r($a->filesName());

 print "-----------------------\n";
 $filesObj = $a->filesObj();
 print $filesObj[0]->fgets();
 print $filesObj[0]->fgets();
 print $filesObj[0]->fgets();
 print $filesObj[0]->fgets();
 print $filesObj[0]->fgets();
 print $filesObj[0]->fgets();

 */
