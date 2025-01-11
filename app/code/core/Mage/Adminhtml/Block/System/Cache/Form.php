<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cache management form page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Cache_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Initialize cache management form
     *
     * @return $this
     */
    public function initForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('cache_enable', [
            'legend' => Mage::helper('adminhtml')->__('Cache Control'),
        ]);

        $fieldset->addField('all_cache', 'select', [
            'name' => 'all_cache',
            'label' => '<strong>' . Mage::helper('adminhtml')->__('All Cache') . '</strong>',
            'value' => 1,
            'options' => [
                '' => Mage::helper('adminhtml')->__('No change'),
                'refresh' => Mage::helper('adminhtml')->__('Refresh'),
                'disable' => Mage::helper('adminhtml')->__('Disable'),
                'enable' => Mage::helper('adminhtml')->__('Enable'),
            ],
        ]);

        foreach (Mage::helper('core')->getCacheTypes() as $type => $label) {
            $fieldset->addField('enable_' . $type, 'checkbox', [
                'name' => 'enable[' . $type . ']',
                'label' => Mage::helper('adminhtml')->__($label),
                'value' => 1,
                'checked' => (int) Mage::app()->useCache($type),
                //'options'=>$options,
            ]);
        }

        $fieldset = $form->addFieldset('beta_cache_enable', [
            'legend' => Mage::helper('adminhtml')->__('Cache Control (beta)'),
        ]);

        foreach (Mage::helper('core')->getCacheBetaTypes() as $type => $label) {
            $fieldset->addField('beta_enable_' . $type, 'checkbox', [
                'name' => 'beta[' . $type . ']',
                'label' => Mage::helper('adminhtml')->__($label),
                'value' => 1,
                'checked' => (int) Mage::app()->useCache($type),
            ]);
        }

        $this->setForm($form);

        return $this;
    }
}
