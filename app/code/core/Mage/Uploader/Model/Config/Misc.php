<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Uploader
 */

/**
 * Misc Config Parameters
 *
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
