<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Uploader
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Misc Config Parameters
 *
 * @category   Mage
 * @package    Mage_Uploader
 *
 * @method $this setMaxSizePlural (string $sizePlural) Set plural info about max upload size
 * @method $this setMaxSizeInBytes (int $sizeInBytes) Set max upload size in bytes
 * @method $this setReplaceBrowseWithRemove (bool $replaceBrowseWithRemove)
 *      Replace browse button with remove
 */
class Mage_Uploader_Model_Config_Misc extends Mage_Uploader_Model_Config_Abstract
{
    /**
     * Prepare misc params
     */
    protected function _construct()
    {
        $this
            ->setMaxSizeInBytes($this->_getHelper()->getDataMaxSizeInBytes())
            ->setMaxSizePlural($this->_getHelper()->getDataMaxSize())
        ;
    }
}
