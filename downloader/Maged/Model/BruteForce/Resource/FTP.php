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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Maged_Model_BruteForce_Resource_FTP implements Maged_Model_BruteForce_Resource_ResourceInterface
{
    /** @var  string */
    protected $connectionString;
    /** @var Mage_Connect_Ftp */
    protected $ftp;
    /**
     * @var string
     */
    private $localFilePath;
    /**
     * @var string
     */
    private $remoteFilePath;

    /**
     * FTP constructor.
     * @param $connectionString
     * @param $localFilePath
     * @param $remoteFileName
     * @throws Exception
     */
    public function __construct($connectionString, $localFilePath, $remoteFileName)
    {
        $this->connectionString = $connectionString;
        $this->ftp = new Mage_Connect_Ftp();
        $this->localFilePath = $localFilePath;
        $this->remoteFilePath = $remoteFileName;

        $this->ftp->connect($this->connectionString);
    }

    /**
     * @return string
     */
    public function read()
    {
        if ($this->isReadable()) {
            return file_get_contents($this->localFilePath);
        }
        return false;
    }

    /**
     * @return boolean
     */
    public function isReadable()
    {
        return ($this->ftp->get($this->localFilePath, $this->remoteFilePath) === true);
    }

    public function __destruct()
    {
        unlink($this->localFilePath);
        $this->ftp->close();
    }

    /**
     * @param string $content
     * @return boolean
     */
    public function write($content)
    {
        if ($this->isWritable()) {
            file_put_contents($this->localFilePath, $content);
            return $this->ftp->upload(
                $this->remoteFilePath,
                $this->localFilePath
            );
        }
        return false;
    }

    /**
     * @return string
     */
    public function isWritable()
    {
        return ($this->isReadable());
    }

    /**
     * @return string
     */
    public function getResourcePath()
    {
        return $this->remoteFilePath;
    }
}
