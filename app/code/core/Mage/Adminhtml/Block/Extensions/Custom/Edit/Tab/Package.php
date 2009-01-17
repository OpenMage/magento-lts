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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profile edit tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Extensions_Custom_Edit_Tab_Package
    extends Mage_Adminhtml_Block_Extensions_Custom_Edit_Tab_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('extensions/custom/package.phtml');
    }

    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_package');

        $fieldset = $form->addFieldset('package_fieldset', array('legend'=>Mage::helper('adminhtml')->__('Package')));

        if ($this->getData('name') != $this->getData('file_name')) {
            $this->setData('file_name_disabled', $this->getData('file_name'));
            $fieldset->addField('file_name_disabled', 'text', array(
                'name' => 'file_name_disabled',
                'label' => Mage::helper('adminhtml')->__('Package File Name'),
                'disabled' => 'disabled',
            ));
        }

        $fieldset->addField('file_name', 'hidden', array(
            'name' => 'file_name',
        ));

    	$fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('adminhtml')->__('Name'),
            'required' => true,
        ));

    	$fieldset->addField('channel', 'text', array(
            'name' => 'channel',
            'label' => Mage::helper('adminhtml')->__('Channel'),
            'required' => true,
            'value' => 'var-dev.varien.com',
        ));

        $fieldset->addField('summary', 'textarea', array(
            'name' => 'summary',
            'label' => Mage::helper('adminhtml')->__('Summary'),
            'style' => 'height:50px;',
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('adminhtml')->__('Description'),
            'style' => 'height:200px;',
            'required' => true,
        ));

    	$fieldset->addField('license', 'text', array(
            'name' => 'license',
            'label' => Mage::helper('adminhtml')->__('License'),
            'required' => true,
            'value' => 'Open Software License (OSL 3.0)',
        ));

    	$fieldset->addField('license_uri', 'text', array(
            'name' => 'license_uri',
            'label' => Mage::helper('adminhtml')->__('License URI'),
            'value' => 'http://opensource.org/licenses/osl-3.0.php',
        ));

        $form->setValues($this->getData());

        $this->setForm($form);

        return $this;
    }

}

