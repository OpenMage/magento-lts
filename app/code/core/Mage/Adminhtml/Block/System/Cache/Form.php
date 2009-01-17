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
 * Cache management form page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Cache_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function initForm()
    {
        $hlp = Mage::helper('adminhtml');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('cache_enable', array(
            'legend'=>$hlp->__('Cache control')
        ));

        $fieldset->addField('all_cache', 'select', array(
            'name'=>'all_cache',
            'label'=>'<strong>'.$hlp->__('All Cache').'</strong>',
            'value'=>1,
            'options'=>array(
                '' => $hlp->__('No change'),
                'refresh' => $hlp->__('Refresh'),
                'disable' => $hlp->__('Disable'),
                'enable' => $hlp->__('Enable'),
            ),
        ));

        foreach (Mage::helper('core')->getCacheTypes() as $type=>$label) {
            $fieldset->addField('enable_'.$type, 'checkbox', array(
                'name'=>'enable['.$type.']',
                'label'=>$hlp->__($label),
                'value'=>1,
                'checked'=>(int)Mage::app()->useCache($type),
                //'options'=>$options,
            ));
        }

//        $fieldset = $form->addFieldset('catalog', array(
//            'legend'=>$hlp->__('Catalog')
//        ));

//        $fieldset->addField('refresh_catalog_rewrites', 'checkbox', array(
//            'name'=>'refresh_catalog_rewrites',
//            'label'=>$hlp->__('Refresh Catalog Rewrites'),
//            'value'=>1,
//        ));
//
//        $fieldset->addField('clear_images_cache', 'checkbox', array(
//            'name'=>'clear_images_cache',
//            'label'=>$hlp->__('Clear Images Cache'),
//            'value'=>1,
//        ));
//
//        $fieldset->addField('refresh_layered_navigation', 'checkbox', array(
//            'name'=>'refresh_layered_navigation',
//            'label'=>$hlp->__('Refresh Layered Navigation Indices'),
//            'value'=>1,
//        ));

/*
        $fieldset = $form->addFieldset('database', array(
            'legend'=>$hlp->__('Database')
        ));

        $values = Mage::getSingleton('adminhtml/system_config_source_dev_dbautoup')
            ->toOptionArray();
        $fieldset->addField('db_auto_update', 'select', array(
            'name'=>'db_auto_update',
            'label'=>$hlp->__('Auto Updates'),
            'value'=>Mage::getSingleton('core/resource')->getAutoUpdate(),
            'values'=>$values,
        ));
*/
        $this->setForm($form);

        return $this;
    }
}
