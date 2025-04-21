<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * Block for rendering role info tab
 *
 * @package    Mage_Api2
 *
 * @method Mage_Api2_Model_Acl_Global_Role getRole()
 * @method $this setRole(Mage_Api2_Model_Acl_Global_Role $role)
 */
class Mage_Api2_Block_Adminhtml_Roles_Tab_Info extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare form object
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend'    => Mage::helper('adminhtml')->__('Role Information'),
        ]);

        $data = [
            'name'  => 'role_name',
            'label' => Mage::helper('adminhtml')->__('Role Name'),
            'id'    => 'role_name',
            'class' => 'required-entry',
            'required' => true,
        ];

        if ($this->isRoleSystem()) {
            /** @var Mage_Core_Helper_Data $helper */
            $helper = Mage::helper('core');

            $data['note'] = Mage::helper('api2')->__('%s role is protected.', $helper->escapeHtml($this->getRole()->getRoleName()));
            $data['readonly'] = 'readonly';
        }
        $fieldset->addField('role_name', 'text', $data);

        $fieldset->addField(
            'entity_id',
            'hidden',
            [
                'name'  => 'id',
            ],
        );

        $fieldset->addField(
            'in_role_users',
            'hidden',
            [
                'name'  => 'in_role_users',
                'id'    => 'in_role_userz',
            ],
        );

        $fieldset->addField(
            'current_password',
            'obscure',
            [
                'name'  => 'current_password',
                'label' => Mage::helper('adminhtml')->__('Current Admin Password'),
                'title' => Mage::helper('adminhtml')->__('Current Admin Password'),
                'required' => true,
            ],
        );

        $fieldset->addField('in_role_users_old', 'hidden', ['name' => 'in_role_users_old']);

        if ($this->getRole()) {
            $form->setValues($this->getRole()->getData());
        }
        $this->setForm($form);
        return $this;
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('api2')->__('Role Info');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Whether tab is available
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Whether tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Whether role is system
     *
     * @return bool
     */
    public function isRoleSystem()
    {
        return $this->getRole() && Mage_Api2_Model_Acl_Global_Role::isSystemRole($this->getRole());
    }
}
