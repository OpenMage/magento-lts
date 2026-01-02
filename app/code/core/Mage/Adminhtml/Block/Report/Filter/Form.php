<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml report filter form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Filter_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Report type options
     */
    protected $_reportTypeOptions = [];

    /**
     * Report field visibility
     */
    protected $_fieldVisibility = [];

    /**
     * Report field options
     */
    protected $_fieldOptions = [];

    /**
     * Set field visibility
     *
     * @param string $fieldId    Field id
     * @param bool   $visibility Field visibility
     */
    public function setFieldVisibility($fieldId, $visibility)
    {
        $this->_fieldVisibility[$fieldId] = (bool) $visibility;
    }

    /**
     * Get field visibility
     *
     * @param  string $fieldId           Field id
     * @param  bool   $defaultVisibility Default field visibility
     * @return bool
     */
    public function getFieldVisibility($fieldId, $defaultVisibility = true)
    {
        if (!array_key_exists($fieldId, $this->_fieldVisibility)) {
            return $defaultVisibility;
        }

        return $this->_fieldVisibility[$fieldId];
    }

    /**
     * Set field option(s)
     *
     * @param string $fieldId Field id
     * @param mixed  $option  Field option name
     * @param mixed  $value   Field option value
     */
    public function setFieldOption($fieldId, $option, $value = null)
    {
        if (is_array($option)) {
            $options = $option;
        } else {
            $options = [$option => $value];
        }

        if (!array_key_exists($fieldId, $this->_fieldOptions)) {
            $this->_fieldOptions[$fieldId] = [];
        }

        foreach ($options as $k => $v) {
            $this->_fieldOptions[$fieldId][$k] = $v;
        }
    }

    /**
     * Add report type option
     *
     * @param  string $key
     * @param  string $value
     * @return $this
     */
    public function addReportTypeOption($key, $value)
    {
        $this->_reportTypeOptions[$key] = $this->__($value);
        return $this;
    }

    /**
     * Add fieldset with general report fields
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $actionUrl = $this->getUrl('*/*/sales');
        $form = new Varien_Data_Form(
            ['id' => 'filter_form', 'action' => $actionUrl, 'method' => 'get'],
        );
        $htmlIdPrefix = 'sales_report_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('reports')->__('Filter')]);

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('store_ids', 'hidden', [
            'name'  => 'store_ids',
        ]);

        $fieldset->addField('report_type', 'select', [
            'name'      => 'report_type',
            'options'   => $this->_reportTypeOptions,
            'label'     => Mage::helper('reports')->__('Match Period To'),
        ]);

        $fieldset->addField('period_type', 'select', [
            'name' => 'period_type',
            'options' => [
                'day'   => Mage::helper('reports')->__('Day'),
                'month' => Mage::helper('reports')->__('Month'),
                'year'  => Mage::helper('reports')->__('Year'),
            ],
            'label' => Mage::helper('reports')->__('Period'),
            'title' => Mage::helper('reports')->__('Period'),
        ]);

        $fieldset->addField('from', 'date', [
            'name'      => 'from',
            'format'    => $dateFormatIso,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('reports')->__('From'),
            'title'     => Mage::helper('reports')->__('From'),
            'required'  => true,
        ]);

        $fieldset->addField('to', 'date', [
            'name'      => 'to',
            'format'    => $dateFormatIso,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('reports')->__('To'),
            'title'     => Mage::helper('reports')->__('To'),
            'required'  => true,
        ]);

        $fieldset->addField('show_empty_rows', 'select', [
            'name'      => 'show_empty_rows',
            'options'   => [
                '1' => Mage::helper('reports')->__('Yes'),
                '0' => Mage::helper('reports')->__('No'),
            ],
            'label'     => Mage::helper('reports')->__('Empty Rows'),
            'title'     => Mage::helper('reports')->__('Empty Rows'),
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Initialize form fields values
     * Method will be called after prepareForm and can be used for field values initialization
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _initFormValues()
    {
        $data = $this->getFilterData()->getData();
        foreach ($data as $key => $value) {
            if (is_array($value) && isset($value[0])) {
                $data[$key] = explode(',', $value[0]);
            }
        }

        $this->getForm()->addValues($data);
        return parent::_initFormValues();
    }

    /**
     * This method is called before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _beforeToHtml()
    {
        $result = parent::_beforeToHtml();

        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if ($fieldset instanceof Varien_Data_Form_Element_Fieldset) {
            // apply field visibility
            foreach ($fieldset->getElements() as $field) {
                if (!$this->getFieldVisibility($field->getId())) {
                    $fieldset->removeField($field->getId());
                }
            }

            // apply field options
            foreach ($this->_fieldOptions as $fieldId => $fieldOptions) {
                $field = $fieldset->getElements()->searchById($fieldId);
                /** @var Varien_Object $field */
                if ($field) {
                    foreach ($fieldOptions as $k => $v) {
                        $field->setDataUsingMethod($k, $v);
                    }
                }
            }
        }

        return $result;
    }
}
