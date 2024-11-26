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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract class for configuration cache
 *
 * @category   Varien
 * @package    Varien_Simplexml
 */
abstract class Varien_Simplexml_Config_Cache_Abstract extends Varien_Object
{
    /**
     * Constructor
     *
     * Initializes components and allows to save the cache
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        parent::__construct($data);

        $this->setComponents([]);
        $this->setIsAllowedToSave(true);
    }

    /**
     * Add configuration component to stats
     *
     * @param string $component Filename of the configuration component file
     * @return Varien_Simplexml_Config_Cache_Abstract
     */
    public function addComponent($component)
    {
        $comps = $this->getComponents();
        if (is_readable($component)) {
            $comps[$component] = ['mtime' => filemtime($component)];
        }
        $this->setComponents($comps);

        return $this;
    }

    /**
     * Validate components in the stats
     *
     * @param array $data
     * @return boolean
     */
    public function validateComponents($data)
    {
        if (empty($data) || !is_array($data)) {
            return false;
        }
        // check that no source files were changed or check file exists
        foreach ($data as $sourceFile => $stat) {
            if (empty($stat['mtime']) || !is_file($sourceFile) || filemtime($sourceFile) !== $stat['mtime']) {
                return false;
            }
        }
        return true;
    }

    public function getComponentsHash()
    {
        $sum = '';
        foreach ($this->getComponents() as $comp) {
            $sum .= $comp['mtime'] . ':';
        }
        $hash = md5($sum);
        return $hash;
    }
}
