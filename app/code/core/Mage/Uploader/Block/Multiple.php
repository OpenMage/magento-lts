<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Uploader
 */

/**
 * @package    Mage_Uploader
 */
class Mage_Uploader_Block_Multiple extends Mage_Uploader_Block_Abstract
{
    public const DEFAULT_UPLOAD_BUTTON_ID_SUFFIX = 'upload';

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setChild(
            'upload_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData([
                    'id'      => $this->getElementId(self::DEFAULT_UPLOAD_BUTTON_ID_SUFFIX),
                    'label'   => Mage::helper('uploader')->__('Upload Files'),
                    'type'    => 'button',
                ]),
        );

        $this->_addElementIdsMapping([
            'upload' => $this->_prepareElementsIds([self::DEFAULT_UPLOAD_BUTTON_ID_SUFFIX]),
        ]);

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
