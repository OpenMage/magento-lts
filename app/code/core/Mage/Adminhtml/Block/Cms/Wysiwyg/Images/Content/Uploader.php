<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Uploader block for Wysiwyg Images
 *
 * @category   Mage
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
