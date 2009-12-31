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
 * osCommerce edit tab
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oscommerce_Block_Adminhtml_Import_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{

    function initForm()
    {
        $model = Mage::registry('oscommerce_adminhtml_import');


        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        if ($model->getId()) {
            $form->addField('import_id', 'hidden', array(
                'name' => 'import_id',
            ));
        }
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('oscommerce')->__('General Information')));
        $fieldset->addField('name', 'text', array(
            'label'     => $this->__('Name'),
            'title'     => $this->__('Name'),
            'name'      => 'name',
            'required'  => true,
        ));

        $fieldset->addField('host', 'text', array(
            'label'     => $this->__('IP or Hostname'),
            'title'     => $this->__('IP or Hostname'),
            'name'      => 'host',
            'required'  => true,
        ));

//        $fieldset->addField('port', 'text', array(
//            'label'     => $this->__('Port (Default as 3306)'),
//            'title'     => $this->__('Port (Default as 3306)'),
//            'name'      => 'port',
//            'required'  => true,
//            'value'     => $model->getData('port') ? $model->getData('port'): Mage_Oscommerce_Model_Oscommerce::DEFAULT_PORT
//        ));

        $fieldset->addField('db_name', 'text', array(
            'label'     => $this->__('DB Name'),
            'title'     => $this->__('DB Name'),
            'name'      => 'db_name',
            'required'  => true,
        ));

        $fieldset->addField('db_user', 'text', array(
            'label'     => $this->__('DB Username'),
            'title'     => $this->__('DB Username'),
            'name'      => 'db_user',
            'required'  => true,
        ));

        $fieldset->addField('db_password', 'password', array(
            'label'     => $this->__('DB Password'),
            'title'     => $this->__('DB Password'),
            'name'      => 'db_password',
        ));

        $fieldset->addField('table_prefix', 'text', array(
            'label'     => $this->__('Prefix'),
            'title'     => $this->__('Prefix'),
            'name'      => 'table_prefix',
        ));

        $fieldset->addField('send_subscription', 'checkbox', array(
            'label'     => $this->__('Send subscription notify to customers'),
            'title'     => $this->__('Send subscription notify to customers'),
            'name'      => 'send_subscription',
            'values'    => $this->getData('send_subscription'),
            'checked'   => $this->getData('send_subscription'),
        ));

        $form->setValues($model->getData());

        $this->setForm($form);

        return $this;
    }
}
