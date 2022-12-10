<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin tag edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Search_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'catalog_search';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('catalog')->__('Save Search'));
        $this->_updateButton('delete', 'label', Mage::helper('catalog')->__('Delete Search'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_catalog_search')->getId()) {
            return Mage::helper('catalog')->__("Edit Search '%s'", $this->escapeHtml(Mage::registry('current_catalog_search')->getQueryText()));
        } else {
            return Mage::helper('catalog')->__('New Search');
        }
    }
}
