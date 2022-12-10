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
 * @category   Magento
 * @package    Magento_Autoload
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Autoload;

class ClassMap
{
    /**
     * Absolute path to base directory that will be prepended as prefix to the included files
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * Map of class name to file (relative to the base directory)
     *
     * array(
     *     'Class_Name' => 'relative/path/to/Class/Name.php',
     * )
     *
     * @var array
     */
    protected $_map = [];

    /**
     * Set base directory absolute path
     *
     * @param string $baseDir
     * @throws \InvalidArgumentException
     */
    public function __construct($baseDir)
    {
        $this->_baseDir = realpath($baseDir);
        if (!$this->_baseDir || !is_dir($this->_baseDir)) {
            throw new \InvalidArgumentException("Specified path is not a valid directory: '{$baseDir}'");
        }
    }

    /**
     * Find an absolute path to a file to be included
     *
     * @param string $class
     * @return string|bool
     */
    public function getFile($class)
    {
        if (isset($this->_map[$class])) {
            return $this->_baseDir . DIRECTORY_SEPARATOR . $this->_map[$class];
        }
        return false;
    }

    /**
     * Add classes files declaration to the map. New map will override existing values if such was defined before.
     *
     * @param array $map
     * @return \Magento\Autoload\ClassMap
     */
    public function addMap(array $map)
    {
        $this->_map = array_merge($this->_map, $map);
        return $this;
    }

    /**
     * Resolve a class file and include it
     *
     * @param string $class
     */
    public function load($class)
    {
        $file = $this->getFile($class);
        if (file_exists($file)) {
            include $file;
        }
    }
}
