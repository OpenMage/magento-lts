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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * description
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Promo_Catalog_Edit_Tab_Actions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('catalogrule')->__('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('catalogrule')->__('Actions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_catalog_rule');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array(
                'legend' => Mage::helper('catalogrule')->__('Update Prices Using the Following Information')
            )
        );

        $fieldset->addField('simple_action', 'select', array(
            'label'     => Mage::helper('catalogrule')->__('Apply'),
            'name'      => 'simple_action',
            'options'   => array(
                'by_percent'    => Mage::helper('catalogrule')->__('By Percentage of the Original Price'),
                'by_fixed'      => Mage::helper('catalogrule')->__('By Fixed Amount'),
                'to_percent'    => Mage::helper('catalogrule')->__('To Percentage of the Original Price'),
                'to_fixed'      => Mage::helper('catalogrule')->__('To Fixed Amount'),
            ),
        ));

        $fieldset->addField('discount_amount', 'text', array(
            'name'      => 'discount_amount',
            'required'  => true,
            'class'     => 'validate-not-negative-number',
            'label'     => Mage::helper('catalogrule')->__('Discount Amount'),
        ));

        $fieldset->addField('sub_is_enable', 'select', array(
            'name'      => 'sub_is_enable',
            'label'     => Mage::helper('catalogrule')->__('Enable Discount to Subproducts'),
            'title'     => Mage::helper('catalogrule')->__('Enable Discount to Subproducts'),
            'onchange'  => 'hideShowSubproductOptions(this);',
            'values'    => array(
                0 => Mage::helper('catalogrule')->__('No'),
                1 => Mage::helper('catalogrule')->__('Yes')
            )
        ));

        $fieldset->addField('sub_simple_action', 'select', array(
            'label'     => Mage::helper('catalogrule')->__('Apply'),
            'name'      => 'sub_simple_action',
            'options'   => array(
                'by_percent'    => Mage::helper('catalogrule')->__('By Percentage of the Original Price'),
                'by_fixed'      => Mage::helper('catalogrule')->__('By Fixed Amount'),
                'to_percent'    => Mage::helper('catalogrule')->__('To Percentage of the Original Price'),
                'to_fixed'      => Mage::helper('catalogrule')->__('To Fixed Amount'),
            ),
        ));

        $fieldset->addField('sub_discount_amount', 'text', array(
            'name'      => 'sub_discount_amount',
            'required'  => true,
            'class'     => 'validate-not-negative-number',
            'label'     => Mage::helper('catalogrule')->__('Discount Amount'),
        ));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => Mage::helper('catalogrule')->__('Stop Further Rules Processing'),
            'title'     => Mage::helper('catalogrule')->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'   => array(
                '1' => Mage::helper('catalogrule')->__('Yes'),
                '0' => Mage::helper('catalogrule')->__('No'),
            ),
        ));

        $form->setValues($model->getData());

        //$form->setUseContainer(true);

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
