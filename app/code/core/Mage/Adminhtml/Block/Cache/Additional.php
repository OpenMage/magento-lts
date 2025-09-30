<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cache_Additional extends Mage_Adminhtml_Block_Template
{
    public function getCleanImagesUrl()
    {
        return $this->getUrl('*/*/cleanImages');
    }

    public function getCleanSwatchesUrl()
    {
        return $this->getUrl('*/*/cleanSwatches');
    }

    public function getCleanMediaUrl()
    {
        return $this->getUrl('*/*/cleanMedia');
    }
}
