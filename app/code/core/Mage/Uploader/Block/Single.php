<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
