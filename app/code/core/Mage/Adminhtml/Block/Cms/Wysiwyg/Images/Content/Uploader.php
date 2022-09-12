<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Uploader block for Wysiwyg Images
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
     */
    protected function _getMediaType()
    {
        if ($this->hasData('media_type')) {
            return $this->_getData('media_type');
        }
        return $this->getRequest()->getParam('type');
    }
}
