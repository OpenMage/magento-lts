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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profile edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Extensions_Custom_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'extensions_custom';

        parent::__construct();

        $this->_removeButton('back');

        $this->_updateButton('reset', 'onclick', "resetPackage()");

        $this->_addButton('create', array(
            'label'=>Mage::helper('adminhtml')->__('Save data and Create Package'),
            'class'=>'save',
            'onclick'=>"createPackage()",
        ));
        $this->_addButton('load', array(
            'label'=>Mage::helper('adminhtml')->__('Load'),
            'title'=>Mage::helper('adminhtml')->__('Load previously created custom local package information'),
            'onclick'=>"loadPackage()",
        ));
    }

    public function getHeaderText()
    {
        return Mage::helper('adminhtml')->__('New Extension');
    }
}
