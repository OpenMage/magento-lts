<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Uploader block for Wysiwyg Images
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cms_Wysiwyg_Images_Content_Uploader extends Mage_Uploader_Block_Multiple
{
    public function __construct()
    {
        parent::__construct();
        $type = $this->_getMediaType();
        $allowed = Mage::getSingleton('cms/wysiwyg_images_storage')->getAllowedExtensions($type);
        $this->getUploaderConfig()
            ->setFileParameterName('image')
            ->setTarget(
                Mage::getModel('adminhtml/url')->addSessionParam()->getUrl(
                    '*/*/upload',
                    ['type' => $type, '_query' => false],
                ),
            );
        $this->getButtonConfig()
            ->setAttributes([
                'accept' => $this->getButtonConfig()->getMimeTypesByExtensions($allowed),
            ]);
    }

    /**
     * Return current media type based on request or data
     * @return string
     */
    protected function _getMediaType()
    {
        if ($this->hasData('media_type')) {
            return $this->_getData('media_type');
        }

        return $this->getRequest()->getParam('type');
    }
}
