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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Uploader
 * @copyright Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Uploader_Block_Multiple extends Mage_Uploader_Block_Abstract
{
    /**
     *
     * Default upload button ID suffix
     */
    const DEFAULT_UPLOAD_BUTTON_ID_SUFFIX = 'upload';


    /**
     * Prepare layout, create upload button
     *
     * @return Mage_Uploader_Block_Multiple
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setChild(
            'upload_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                    'id'      => $this->getElementId(self::DEFAULT_UPLOAD_BUTTON_ID_SUFFIX),
                    'label'   => Mage::helper('uploader')->__('Upload Files'),
                    'type'    => 'button',
                ))
        );

        $this->_addElementIdsMapping(array(
            'upload' => $this->_prepareElementsIds(array(self::DEFAULT_UPLOAD_BUTTON_ID_SUFFIX))
        ));

        return $this;
    }

    /**
     * Get upload button html
     *
     * @return string
     */
    public function getUploadButtonHtml()
    {
        return $this->getChildHtml('upload_button');
    }
}
