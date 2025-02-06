<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Cms
 */

/**
 * Wysiwyg Images storage collection
 *
 * @category   Mage
 * @package    Mage_Cms
 */
class Mage_Cms_Model_Wysiwyg_Images_Storage_Collection extends Varien_Data_Collection_Filesystem
{
    /**
     * @inheritDoc
     */
    protected function _generateRow($filename)
    {
        $filename = preg_replace('~[/\\\]+~', DIRECTORY_SEPARATOR, $filename);

        return [
            'filename' => $filename,
            'basename' => basename($filename),
            'mtime'    => filemtime($filename),
        ];
    }
}
