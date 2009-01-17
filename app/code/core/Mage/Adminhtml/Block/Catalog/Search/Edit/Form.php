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
 * Adminhtml tag edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Catalog_Search_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_search_form');
        $this->setTitle(Mage::helper('catalog')->__('Search Information'));
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_catalog_search');

        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('catalog')->__('General Information')));

        $yesno = array(
            array(
                'value' => 0,
                'label' => Mage::helper('catalog')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('catalog')->__('Yes')
            ));

        if ($model->getId()) {
        	$fieldset->addField('query_id', 'hidden', array(
                'name' => 'query_id',
            ));
        }

    	$fieldset->addField('query_text', 'text', array(
            'name' => 'query_text',
            'label' => Mage::helper('catalog')->__('Search Query'),
            'title' => Mage::helper('catalog')->__('Search Query'),
            'required' => true,
        ));

        if ($model->getId()) {
	    	$fieldset->addField('num_results', 'text', array(
	            'name' => 'num_results',
	            'label' => Mage::helper('catalog')->__('Number of results<br/>(For last time placed)'),
	            'title' => Mage::helper('catalog')->__('Number of results<br/>(For last time placed)'),
	            'required' => true,
	        ));

	    	$fieldset->addField('popularity', 'text', array(
	            'name' => 'popularity',
	            'label' => Mage::helper('catalog')->__('Number of Uses'),
	            'title' => Mage::helper('catalog')->__('Number of Uses'),
	            'required' => true,
	        ));
        }

        $fieldset->addField('synonim_for', 'text', array(
            'name' => 'synonim_for',
            'label' => Mage::helper('catalog')->__('Synonym For'),
            'title' => Mage::helper('catalog')->__('Synonym For'),
            'after_element_html' => '<span class="hint">' . Mage::helper('catalog')->__('(Will make search for the query above return results for this search.)') . '</span>',
        ));

        $fieldset->addField('redirect', 'text', array(
            'name' => 'redirect',
            'label' => Mage::helper('catalog')->__('Redirect URL'),
            'title' => Mage::helper('catalog')->__('Redirect URL'),
            'after_element_html' => '<span class="hint">' . Mage::helper('catalog')->__('ex. http://domain.com') . '</span>',
        ));

        $fieldset->addField('display_in_terms', 'select', array(
            'name' => 'display_in_terms',
            'label' => Mage::helper('catalog')->__('Display in Suggested Terms'),
            'title' => Mage::helper('catalog')->__('Display in Suggested Terms'),
            'values' => $yesno,
        ));

        $form->setValues($model->getData());

        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
