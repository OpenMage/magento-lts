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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin product tax class add form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Tax_Rate_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_titles = null;

    public function __construct()
    {
        parent::__construct();
        $this->setDestElementId('rate_form');
        $this->setTemplate('tax/rate/form.phtml');
    }

    protected function _prepareForm()
    {
        $rateId = (int)$this->getRequest()->getParam('rate');
        $rateObject = new Varien_Object();
        $rateModel  = Mage::getSingleton('tax/calculation_rate');
        $rateObject->setData($rateModel->getData());
        $form = new Varien_Data_Form();

        $countries = Mage::getModel('adminhtml/system_config_source_country')
            ->toOptionArray();
        unset($countries[0]);

        $countryId = $rateObject->getTaxCountryId();
        if (!$countryId) {
            $countryId = Mage::getStoreConfig('general/country/default');
        }

        $regionCollection = Mage::getModel('directory/region')
            ->getCollection()
            ->addCountryFilter($countryId);

        $regions = $regionCollection->toOptionArray();

        if ($regions) {
            $regions[0]['label'] = '*';
        } else {
            $regions = array(array('value'=>'', 'label'=>'*'));
        }

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('tax')->__('Tax Rate Information')));

        if( $rateObject->getTaxCalculationRateId() > 0 ) {
            $fieldset->addField('tax_calculation_rate_id', 'hidden',
                array(
                    'name' => "tax_calculation_rate_id",
                    'value' => $rateObject->getTaxCalculationRateId()
                )
            );
        }

        $fieldset->addField('code', 'text',
            array(
                'name' => 'code',
                'label' => Mage::helper('tax')->__('Tax Identifier'),
                'title' => Mage::helper('tax')->__('Tax Identifier'),
                'class' => 'required-entry',
                'value' => $rateModel->getCode(),
                'required' => true,
            )
        );

        $fieldset->addField('tax_country_id', 'select',
            array(
                'name' => 'tax_country_id',
                'label' => Mage::helper('tax')->__('Country'),
                'title' => Mage::helper('tax')->__('Please select Country'),
                'class' => 'required-entry',
                'required' => true,
                'values' => $countries,
                'value' => $countryId,
            )
        );

        $fieldset->addField('tax_region_id', 'select',
            array(
                'name' => 'tax_region_id',
                'label' => Mage::helper('tax')->__('State'),
                'title' => Mage::helper('tax')->__('Please select State'),
                'class' => 'required-entry',
                'required' => true,
                'values' => $regions,
                'value' => $rateObject->getTaxRegionId()
            )
        );

        /* FIXME!!! {*
        $fieldset->addField('tax_county_id', 'select',
            array(
                'name' => 'tax_county_id',
                'label' => Mage::helper('tax')->__('County'),
                'title' => Mage::helper('tax')->__('Please select County'),
                'values' => array(
                    array(
                        'label' => '*',
                        'value' => ''
                    )
                ),
                'value' => $rateObject->getTaxCountyId()
            )
        );
        } */

        $postcode = $rateObject->getTaxPostcode();
        if (!$postcode) {
            $postcode = '*';
        }

        $fieldset->addField('tax_postcode', 'text',
            array(
                'name' => 'tax_postcode',
                'label' => Mage::helper('tax')->__('Zip/Post Code'),
                'note' => Mage::helper('tax')->__("'*' - matches any; 'xyz*' - matches any that begins on 'xyz' and not longer than %d; '9001-9099' - matches range.", Mage::helper('tax')->getPostCodeSubStringLength()),
                'value' => $postcode
            )
        );

        if ($rateObject->getRate()) {
            $value = 1*$rateObject->getRate();
        } else {
            $value = 0;
        }
        $fieldset->addField('rate', 'text',
            array(
                'name' => "rate",
                'label' => Mage::helper('tax')->__('Rate'),
                'title' => Mage::helper('tax')->__('Rate'),
                'value' => number_format($value, 4),
                'required' => true,
                'class' => 'validate-not-negative-number required-entry'
            )
        );

        $form->setAction($this->getUrl('*/tax_rate/save'));
        $form->setUseContainer(true);
        $form->setId('rate_form');
        $form->setMethod('post');


        if (!Mage::app()->isSingleStoreMode()) {
            $form->addElement(Mage::getBlockSingleton('adminhtml/tax_rate_title_fieldset')->setLegend(Mage::helper('tax')->__('Tax Titles')));
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
