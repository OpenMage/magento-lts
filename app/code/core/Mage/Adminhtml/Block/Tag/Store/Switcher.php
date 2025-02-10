<?php
/**
 * Adminhtml Tag Store Switcher
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tag_Store_Switcher extends Mage_Adminhtml_Block_Store_Switcher
{
    /**
     * @var bool
     */
    protected $_hasDefaultOption = false;

    /**
     * Set overridden params
     */
    public function __construct()
    {
        parent::__construct();
        $this->setUseConfirm(false)->setSwitchUrl(
            $this->getUrl('*/*/*/', ['store' => null, '_current' => true]),
        );
    }
}
