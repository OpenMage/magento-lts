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
 * @category    Mage
 * @package     Mage_GoogleBase
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml GoogleBase Item Types Grid
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_GoogleBase_Block_Adminhtml_Types extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'googlebase';
        $this->_controller = 'adminhtml_types';
        $this->_addButtonLabel = Mage::helper('googlebase')->__('Add Attribute Mapping');
        $this->_headerText = Mage::helper('googlebase')->__('Manage Attribute Mapping');
        parent::__construct();
    }

//    public function getGridHtml()
//    {
//        $_storeSwitcherHtml = $this->getLayout()->createBlock('googlebase/adminhtml_store_switcher')->toHtml();
//        return $_storeSwitcherHtml . parent::getGridHtml();
//    }
//
//    public function getCreateUrl()
//    {
//        return $this->getUrl('*/*/new', array('_current'=>true));
//    }
}
