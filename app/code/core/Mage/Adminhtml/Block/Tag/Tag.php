<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml all tags
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tag_Tag extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'tag_tag';
        $this->_headerText = Mage::helper('tag')->__('Manage Tags');
        $this->_addButtonLabel = Mage::helper('tag')->__('Add New Tag');
        parent::__construct();
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-tag';
    }
}
