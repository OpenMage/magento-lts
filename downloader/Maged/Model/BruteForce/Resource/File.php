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
 * @package     Mage_Connect
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Maged_Model_BruteForce_Resource_File implements Maged_Model_BruteForce_Resource_ResourceInterface
{
    /** @var string */
    protected $filePath;
    /** @var  string */
    protected $displayFileName;

    /**
     * File constructor.
     * @param string $filePath
     * @param displayFileName
     */
    public function __construct($filePath, $displayFileName)
    {
        $this->filePath = $filePath;
        $this->displayFileName = $displayFileName;
    }

    /**
     * @return string
     */
    public function read()
    {
        if ($this->isReadable()) {
            return file_get_contents($this->filePath);
        }
        return false;
    }

    /**
     * @return boolean
     */
    public function isReadable()
    {
        return (is_file($this->filePath) and is_readable($this->filePath));
    }

    /**
     * @param string $content
     * @return boolean
     */
    public function write($content)
    {
        if ($this->isWritable()) {
            return (boolean)file_put_contents($this->filePath, $content);
        }
        return false;
    }

    /**
     * @return string
     */
    public function isWritable()
    {
        return (is_file($this->filePath) and is_writable($this->filePath));
    }

    /**
     * @return string
     */
    public function getResourcePath()
    {
        return $this->displayFileName;
    }
}
