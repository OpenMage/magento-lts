<?php
/**
 * Block tabs for attributes edit page
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Api2
 */
class Mage_Api2_Block_Adminhtml_Attribute_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('api2_attribute_section_main')
            ->setDestElementId('edit_form')
            ->setData('title', $this->__('ACL Attributes Information'));
    }
}
