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
 * @package    Varien_Convert
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Convert HTTP adapter
 *
 * @category   Varien
 * @package    Varien_Convert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
 class Mage_Core_Model_Convert_Adapter_Interactive extends Varien_Convert_Adapter_Abstract
 {

     public function load()
     {
         if (!$_FILES) {
?>
<form method="post" enctype="multipart/form-data">
File to upload: <input type="file" name="io_file"/> <input type="submit" value="Upload"/>
</form>
<?php
             exit;
         }
         if (!empty($_FILES['io_file']['tmp_name'])) {
             //move_uploaded_file($_FILES['io_file']['tmp_name'])
             $this->setData(file_get_contents($_FILES['io_file']['tmp_name']));
         }

         return $this;
     }

     public function save()
     {
         if ($this->getVars()) {
             foreach ($this->getVars() as $key=>$value) {
                 header($key.': '.$value);
             }
         }
         echo $this->getData();
         return $this;
     }

 }