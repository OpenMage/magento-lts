<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Catalog product gallery controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Catalog_Product_GalleryController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'catalog/products';

    public function uploadAction()
    {
        try {
            $uploader = Mage::getModel('core/file_uploader', 'image');
            $uploader->setAllowedExtensions(Varien_Io_File::ALLOWED_IMAGES_EXTENSIONS);
            $uploader->addValidateCallback(
                'catalog_product_image',
                Mage::helper('catalog/image'),
                'validateUploadFile',
            );
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $uploader->addValidateCallback(
                Mage_Core_Model_File_Validator_Image::NAME,
                Mage::getModel('core/file_validator_image'),
                'validate',
            );
            $result = $uploader->save(
                Mage::getSingleton('catalog/product_media_config')->getBaseTmpMediaPath(),
            );

            Mage::dispatchEvent('catalog_product_gallery_upload_image_after', [
                'result' => $result,
                'action' => $this,
            ]);

            /**
             * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
             */
            $result['tmp_name'] = str_replace(DS, '/', $result['tmp_name']);
            $result['path'] = str_replace(DS, '/', $result['path']);

            $result['url'] = Mage::getSingleton('catalog/product_media_config')->getTmpMediaUrl($result['file']);
            $result['file'] .= '.tmp';
            $result['cookie'] = [
                'name'     => session_name(),
                'value'    => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path'     => $this->_getSession()->getCookiePath(),
                'domain'   => $this->_getSession()->getCookieDomain(),
            ];
        } catch (Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()];
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}
