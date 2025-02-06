<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api2
 */

/**
 * Block tabs for attributes edit page
 *
 * @category   Mage
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
