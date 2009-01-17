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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Varien
 * @package    Mage_Dataflow_Model_Convert
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Convert HTTP adapter
 *
 * @category   Varien
 * @package    Mage_Dataflow_Model_Convert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
 class Mage_Dataflow_Model_Session_Adapter_Http extends Mage_Dataflow_Model_Convert_Adapter_Abstract
 {
     public function load()
     {
         if (!$_FILES) {
?>
<form method="POST" enctype="multipart/form-data">
File to upload: <input type="file" name="io_file"/> <input type="submit" value="Upload"/>
</form>
<?php
             exit;
         }
         if (!empty($_FILES['io_file']['tmp_name'])) {
            //$this->setData(file_get_contents($_FILES['io_file']['tmp_name']));
            $uploader = new Varien_File_Uploader('io_file');
            $uploader->setAllowedExtensions(array('csv','xml'));
            $path = Mage::app()->getConfig()->getTempVarDir().'/import/';
            $uploader->save($path);
            if ($uploadFile = $uploader->getUploadedFileName()) {
                $session = Mage::getModel('dataflow/session');
                $session->setCreatedDate(date('Y-m-d H:i:s'));
                $session->setDirection('import');
                $session->setUserId(Mage::getSingleton('admin/session')->getUser()->getId());
                $session->save();
                $sessionId = $session->getId();
                $newFilename = 'import_'.$sessionId.'_'.$uploadFile;
                rename($path.$uploadFile, $path.$newFilename);
                $session->setFile($newFilename);
                $session->save();
                $this->setData(file_get_contents($path.$newFilename));
                Mage::register('current_dataflow_session_id', $sessionId);
                /*
                $read = @fopen($path.$newFilename, "r");
                if ($read) {
                    $i = 0;
                    while (!feof($read)) {

                        $buffer = fgets($read, 4096);
                        $import = Mage::getModel('dataflow/import');
                        $import->setSerialNumber($i);
                        $import->setSessionId($sessionId);
                        $import->setSessionId($value);
                        $i++;
                    }
                    fclose($read);
                }
                */
            }
         }
         return $this;
     } // end
 }