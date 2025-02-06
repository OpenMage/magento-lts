<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */

/**
 * Convert HTTP adapter
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Convert_Iterator_Http extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    /**
     * @SuppressWarnings("PHPMD.ExitExpression")
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public function load()
    {
        if (!$_FILES) {
            echo '<form method="post" enctype="multipart/form-data">';
            echo 'File to upload: <input type="file" name="io_file"/> <input type="submit" value="Upload"/>';
            echo '</form>';
            exit;
        }
        if (!empty($_FILES['io_file']['tmp_name'])) {
            $uploader = Mage::getModel('core/file_uploader', 'io_file');
            $uploader->setAllowedExtensions(['csv','xml']);
            $path = Mage::app()->getConfig()->getTempVarDir() . '/import/';
            $uploader->save($path);
            if ($uploadFile = $uploader->getUploadedFileName()) {
                $fp = fopen($uploadFile, 'rb');
                while ($row = fgetcsv($fp)) {
                    // check csv
                }
                fclose($fp);
            }
        }
        return $this;
    }
}
