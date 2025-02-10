<?php
/**
 * Wysiwyg Images storage collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
