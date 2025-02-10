<?php
/**
 * Abstract container block with header
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Page
 */
class Mage_Page_Block_Template_Container extends Mage_Core_Block_Template
{
    /**
     * Set default template
     *
     */
    protected function _construct()
    {
        $this->setTemplate('page/template/container.phtml');
    }
}
