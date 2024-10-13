<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Uploader block for Wysiwyg Images
 *
 * @category   Mage
 * @package    Mage_Cms
*/
class Mage_Cms_Block_Adminhtml_Wysiwyg_Images_Content_Uploader extends Mage_Uploader_Block_Multiple
{
    /**
     * @throws Exception
     */
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
                    ['type' => $type, '_query' => false]
                )
            );
        $this->getButtonConfig()
            ->setAttributes([
                'accept' => $this->getButtonConfig()->getMimeTypesByExtensions($allowed)
            ]);
    }

    /**
     * Return current media type based on request or data
     * @return string
     * @throws Exception
     */
    protected function _getMediaType()
    {
        if ($this->hasData('media_type')) {
            return $this->_getData('media_type');
        }
        return $this->getRequest()->getParam('type');
    }
}
