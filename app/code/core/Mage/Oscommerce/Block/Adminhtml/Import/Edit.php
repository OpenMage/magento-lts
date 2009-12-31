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
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * osCommerce import edit block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Oscommerce_Block_Adminhtml_Import_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_blockGroup = 'oscommerce';

    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_import';
        $this->_updateButton('save', 'label', Mage::helper('oscommerce')->__('Save Profile'));
        $this->_updateButton('delete', 'label', Mage::helper('oscommerce')->__('Delete Profile'));
        $this->_addButton('save_and_edit_button',
                    array(
                    'label'     => Mage::helper('oscommerce')->__('Save And Continue Edit'),
                    'onclick'   => 'saveAndContinueEdit()',
                    'class'     => 'save'
                ), 100

            );
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'continue/back/');
            }
        ";
        parent::__construct();
    }

    public function getHeaderText()
    {
        if (Mage::registry('oscommerce_adminhtml_import')->getId()) { // TOCHECK
            return Mage::helper('oscommerce')->__('Edit osCommerce Profile :: %s', Mage::registry('oscommerce_adminhtml_import')->getName());
        }
        else {
            return Mage::helper('oscommerce')->__('New osCommerce Profile');
        }
    }
}
