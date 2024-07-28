<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert HTTP adapter
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Convert_Iterator_Http extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
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
