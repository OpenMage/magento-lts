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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block for rendering buttons
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 * @method Mage_Api2_Block_Adminhtml_Roles_Buttons setRole(Mage_Api2_Model_Acl_Global_Role $role)
 * @method Mage_Api2_Model_Acl_Global_Role getRole()
 */
class Mage_Api2_Block_Adminhtml_Roles_Buttons extends Mage_Adminhtml_Block_Template
{
    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('api2/role/buttons.phtml');
    }

    /**
     * Preparing global layout
     *
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $buttons = array(
            'backButton'    => array(
                'label'     => Mage::helper('adminhtml')->__('Back'),
                'onclick'   => sprintf("window.location.href='%s';", $this->getUrl('*/*/')),
                'class'     => 'back'
            ),
            'resetButton'   => array(
                'label'     => Mage::helper('adminhtml')->__('Reset'),
                'onclick'   => 'window.location.reload()'
            ),
            'saveButton'    => array(
                'label'     => Mage::helper('adminhtml')->__('Save Role'),
                'onclick'   => 'roleForm.submit(); return false;',
                'class'     => 'save'
            ),
            'deleteButton'  => array(
                'label'     => Mage::helper('adminhtml')->__('Delete Role'),
                'onclick'   => '',  //roleId is not set at this moment, so we set script later
                'class'     => 'delete'
            ),
        );

        foreach ($buttons as $name => $data) {
            $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData($data);
            $this->setChild($name, $button);
        }

        return parent::_prepareLayout();
    }

    /**
     * Get back button HTML
     *
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('backButton');
    }

    /**
     * Get reset button HTML
     *
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('resetButton');
    }

    /**
     * Get save button HTML
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('saveButton');
    }

    /**
     * Get delete button HTML
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        if (!$this->getRole() || !$this->getRole()->getId()
            || Mage_Api2_Model_Acl_Global_Role::isSystemRole($this->getRole())) {
            return '';
        }

        $this->getChild('deleteButton')->setData('onclick', sprintf(
            "if(confirm('%s')) roleForm.submit('%s'); return false;",
            Mage::helper('core')->jsQuoteEscape(Mage::helper('adminhtml')->__('Are you sure you want to do this?')),
            $this->getUrl('*/*/delete')
        ));

        return $this->getChildHtml('deleteButton');
    }

    /**
     * Get block caption
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->getRole() && $this->getRole()->getId()
                ? ($this->__('Edit Role') . " '{$this->escapeHtml($this->getRole()->getRoleName())}'")
                : $this->__('Add New Role');
    }
}
