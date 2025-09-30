<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Poll edit form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Rating_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare rating edit form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('rating_form', [
            'legend' => Mage::helper('rating')->__('Rating Title'),
        ]);

        $fieldset->addField('rating_code', 'text', [
            'name' => 'rating_code',
            'label' => Mage::helper('rating')->__('Default Value'),
            'class' => 'required-entry',
            'required' => true,
        ]);

        foreach (Mage::getSingleton('adminhtml/system_store')->getStoreCollection() as $store) {
            $fieldset->addField('rating_code_' . $store->getId(), 'text', [
                'label' => $store->getName(),
                'name' => 'rating_codes[' . $store->getId() . ']',
            ]);
        }

        if (Mage::getSingleton('adminhtml/session')->getRatingData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getRatingData());
            $data = Mage::getSingleton('adminhtml/session')->getRatingData();
            if (isset($data['rating_codes'])) {
                $this->_setRatingCodes($data['rating_codes']);
            }
            Mage::getSingleton('adminhtml/session')->setRatingData(null);
        } elseif (Mage::registry('rating_data')) {
            $form->setValues(Mage::registry('rating_data')->getData());
            if (Mage::registry('rating_data')->getRatingCodes()) {
                $this->_setRatingCodes(Mage::registry('rating_data')->getRatingCodes());
            }
        }

        if (Mage::registry('rating_data')) {
            $collection = Mage::getModel('rating/rating_option')
                ->getResourceCollection()
                ->addRatingFilter(Mage::registry('rating_data')->getId())
                ->load();

            $i = 1;
            foreach ($collection->getItems() as $item) {
                $fieldset->addField('option_code_' . $item->getId(), 'hidden', [
                    'required' => true,
                    'name' => 'option_title[' . $item->getId() . ']',
                    'value' => ($item->getCode()) ? $item->getCode() : $i,
                ]);

                $i++;
            }
        } else {
            for ($i = 1; $i <= 5; $i++) {
                $fieldset->addField('option_code_' . $i, 'hidden', [
                    'required' => true,
                    'name' => 'option_title[add_' . $i . ']',
                    'value' => $i,
                ]);
            }
        }

        $fieldset = $form->addFieldset('visibility_form', [
            'legend' => Mage::helper('rating')->__('Rating Visibility'),
        ]);

        $field = $fieldset->addField('stores', 'multiselect', [
            'label' => Mage::helper('rating')->__('Visible In'),
            'name' => 'stores[]',
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
        ]);
        $renderer = $this->getStoreSwitcherRenderer();
        $field->setRenderer($renderer);

        $fieldset->addField('position', 'text', [
            'label' => Mage::helper('rating')->__('Sort Order'),
            'name' => 'position',
        ]);

        if (Mage::registry('rating_data')) {
            $form->getElement('position')->setValue(Mage::registry('rating_data')->getPosition());
            $form->getElement('stores')->setValue(Mage::registry('rating_data')->getStores());
        }

        return parent::_prepareForm();
    }

    protected function _setRatingCodes($ratingCodes)
    {
        foreach ($ratingCodes as $store => $value) {
            if ($element = $this->getForm()->getElement('rating_code_' . $store)) {
                $element->setValue($value);
            }
        }
    }

    protected function _toHtml()
    {
        return $this->_getWarningHtml() . parent::_toHtml();
    }

    protected function _getWarningHtml()
    {
        return '<div>
<ul class="messages">
    <li class="notice-msg">
        <ul>
            <li>' . Mage::helper('rating')->__('If you do not specify a rating title for a store, the default value will be used.') . '</li>
        </ul>
    </li>
</ul>
</div>';
    }
}
