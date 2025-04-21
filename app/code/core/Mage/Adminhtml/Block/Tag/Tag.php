<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Adminhtml all tags
 *
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
