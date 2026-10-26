<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml Tag Store Switcher
 *
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
