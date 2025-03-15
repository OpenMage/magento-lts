<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Simplexml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * File based cache for configuration
 *
 * @category   Varien
 * @package    Varien_Simplexml
 */
class Varien_Simplexml_Config_Cache_File extends Varien_Simplexml_Config_Cache_Abstract
{
    /**
     * Initialize variables that depend on the cache key
     *
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->setData('key', $key);

        $file = $this->getDir() . DS . $this->getKey();
        $this->setFileName($file . '.xml');
        $this->setStatFileName($file . '.stat');

        return $this;
    }

    /**
     * Try to load configuration cache from file
     *
     * @return boolean
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function load()
    {
        $this->setIsLoaded(false);

        // try to read stats
        if (!($stats = @file_get_contents($this->getStatFileName()))) {
            return false;
        }

        // try to validate stats
        if (!$this->validateComponents(unserialize($stats))) {
            return false;
        }

        // try to read cache file
        if (!($cache = @file_get_contents($this->getFileName()))) {
            return false;
        }

        // try to process cache file
        if (!($data = $this->getConfig()->processFileData($cache))) {
            return false;
        }

        $xml = $this->getConfig()->loadString($data);
        $this->getConfig()->setXml($xml);
        $this->setIsLoaded(true);

        return true;
    }

    /**
     * Try to save configuration cache to file
     *
     * @return boolean
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function save()
    {
        if (!$this->getIsAllowedToSave()) {
            return false;
        }

        // save stats
        @file_put_contents($this->getStatFileName(), serialize($this->getComponents()));

        // save cache
        @file_put_contents($this->getFileName(), $this->getConfig()->getNode()->asNiceXml());

        return true;
    }
}
