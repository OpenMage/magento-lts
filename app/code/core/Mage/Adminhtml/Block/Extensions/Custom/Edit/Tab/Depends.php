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
 * Convert profile edit tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Extensions_Custom_Edit_Tab_Depends
    extends Mage_Adminhtml_Block_Extensions_Custom_Edit_Tab_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('extensions/custom/depends.phtml');
    }

    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_depends');

        $fieldset = $form->addFieldset('depends_php_fieldset', array('legend'=>Mage::helper('adminhtml')->__('PHP Version')));

    	$fieldset->addField('depends_php_min', 'text', array(
            'name' => 'depends_php_min',
            'label' => Mage::helper('adminhtml')->__('Minimum'),
            'required' => true,
            'value' => '5.2.0',
        ));

    	$fieldset->addField('depends_php_max', 'text', array(
            'name' => 'depends_php_max',
            'label' => Mage::helper('adminhtml')->__('Maximum'),
            'required' => true,
            'value' => '6.0.0',
        ));

    	$fieldset->addField('depends_php_recommended', 'text', array(
            'name' => 'depends_php_recommended',
            'label' => Mage::helper('adminhtml')->__('Recommended'),
        ));

    	$fieldset->addField('depends_php_exclude', 'text', array(
            'name' => 'depends_php_exclude',
            'label' => Mage::helper('adminhtml')->__('Exclude (comma separated)'),
        ));

        $form->setValues($this->getData());

        $this->setForm($form);

        return $this;
    }

    public function getPackages()
    {
        return array('Mage_Core'=>'Mage_Core');
    }

    public function getExtensions()
    {
        $arr = array();
        foreach (get_loaded_extensions() as $ext) {
            $arr[$ext] = $ext;
        }
        asort($arr, SORT_STRING);
        return $arr;
    }

    public function getDependTypes()
    {
        return array(
            'required'=>Mage::helper('adminhtml')->__('Required'),
            'optional'=>Mage::helper('adminhtml')->__('Optional'),
            'conflicts'=>Mage::helper('adminhtml')->__('Conflicts'),
        );
    }
}
