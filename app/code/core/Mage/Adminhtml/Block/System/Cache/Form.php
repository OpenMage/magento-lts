<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cache management form page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
            'legend' => Mage::helper('adminhtml')->__('Cache Control')
        ]);

        $fieldset->addField('all_cache', 'select', [
            'name'=>'all_cache',
            'label'=>'<strong>'.Mage::helper('adminhtml')->__('All Cache').'</strong>',
            'value'=>1,
            'options'=> [
                '' => Mage::helper('adminhtml')->__('No change'),
                'refresh' => Mage::helper('adminhtml')->__('Refresh'),
                'disable' => Mage::helper('adminhtml')->__('Disable'),
                'enable' => Mage::helper('adminhtml')->__('Enable'),
            ],
        ]);

        foreach (Mage::helper('core')->getCacheTypes() as $type=>$label) {
            $fieldset->addField('enable_'.$type, 'checkbox', [
                'name'=>'enable['.$type.']',
                'label'=>Mage::helper('adminhtml')->__($label),
                'value'=>1,
                'checked'=>(int)Mage::app()->useCache($type),
                //'options'=>$options,
            ]);
        }

        $fieldset = $form->addFieldset('beta_cache_enable', [
            'legend' => Mage::helper('adminhtml')->__('Cache Control (beta)')
        ]);

        foreach (Mage::helper('core')->getCacheBetaTypes() as $type=>$label) {
            $fieldset->addField('beta_enable_'.$type, 'checkbox', [
                'name'=>'beta['.$type.']',
                'label'=>Mage::helper('adminhtml')->__($label),
                'value'=>1,
                'checked'=>(int)Mage::app()->useCache($type),
            ]);
        }

        $this->setForm($form);

        return $this;
    }
}
