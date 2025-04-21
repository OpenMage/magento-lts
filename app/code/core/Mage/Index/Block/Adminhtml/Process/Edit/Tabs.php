<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * @package    Mage_Index
 */
class Mage_Index_Block_Adminhtml_Process_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Mage_Index_Block_Adminhtml_Process_Edit_Tabs constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('process_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('index')->__('Index'));
    }
}
