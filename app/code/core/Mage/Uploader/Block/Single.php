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
class Mage_Uploader_Block_Single extends Mage_Uploader_Block_Abstract
{
    /**
     * Prepare layout, change button and set front-end element ids mapping
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getChild('browse_button')->setLabel(Mage::helper('uploader')->__('...'));

        return $this;
    }

    public function __construct()
    {
        parent::__construct();

        $this->getUploaderConfig()->setSingleFile(true);
        $this->getButtonConfig()->setSingleFile(true);
    }
}
