<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Uploader
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
