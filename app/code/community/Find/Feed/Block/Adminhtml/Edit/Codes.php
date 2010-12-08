<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Find
 * @package     Find_Feed
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Attribute map edit codes form container block
 *
 * @category    Find
 * @package     Find_Feed
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Find_Feed_Block_Adminhtml_Edit_Codes extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize form container
     *
     */
    public function __construct()
    {
        $this->_blockGroup = 'find_feed';
        $this->_controller = 'adminhtml_edit_codes';

        parent::__construct();

        $this->_removeButton('back');
        $url = $this->getUrl('*/codes_grid/saveForm');
        $this->_updateButton('save', 'onclick', 'saveNewImportItem(\''.$url.'\')');
        $this->_updateButton('reset', 'label', 'Close');
        $this->_updateButton('reset', 'onclick', 'closeNewImportItem()');
    }

    /**
     * Return Form Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('find_feed')->__('Import attribute map');
    }
}
