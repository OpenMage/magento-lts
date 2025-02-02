<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Settlement reports transaction details
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Adminhtml_Settlement_Details_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare read-only data and group it by fieldsets
     * @return $this
     * @throws Zend_Currency_Exception
     */
    protected function _prepareForm()
    {
        /** @var Mage_Paypal_Model_Report_Settlement_Row $model */
        $model = Mage::registry('current_transaction');
        /** @var Mage_Paypal_Model_Report_Settlement $settlement */
        $settlement = Mage::getSingleton('paypal/report_settlement');

        $fieldsets = [
            'reference_fieldset' => [
                'fields' => [
                    'transaction_id' => ['label' => $settlement->getFieldLabel('transaction_id')],
                    'invoice_id' => ['label' => $settlement->getFieldLabel('invoice_id')],
                    'paypal_reference_id' => ['label' => $settlement->getFieldLabel('paypal_reference_id')],
                    'paypal_reference_id_type' => [
                        'label' => $settlement->getFieldLabel('paypal_reference_id_type'),
                        'value' => $model->getReferenceType($model->getData('paypal_reference_id_type')),
                    ],
                    'custom_field' => ['label' => $settlement->getFieldLabel('custom_field')],
                ],
                'legend' => Mage::helper('paypal')->__('Reference Information'),
            ],

            'transaction_fieldset' => [
                'fields' => [
                    'transaction_event_code' => [
                        'label' => $settlement->getFieldLabel('transaction_event_code'),
                        'value' => sprintf('%s (%s)', $model->getData('transaction_event_code'), $model->getTransactionEvent($model->getData('transaction_event_code'))),
                    ],
                    'transaction_initiation_date' => [
                        'label' => $settlement->getFieldLabel('transaction_initiation_date'),
                        'value' => $this->formatDate($model->getData('transaction_initiation_date'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true),
                    ],
                    'transaction_completion_date' => [
                        'label' => $settlement->getFieldLabel('transaction_completion_date'),
                        'value' => $this->formatDate($model->getData('transaction_completion_date'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true),
                    ],
                    'transaction_debit_or_credit' => [
                        'label' => $settlement->getFieldLabel('transaction_debit_or_credit'),
                        'value' => $model->getDebitCreditText($model->getData('transaction_debit_or_credit')),
                    ],
                    'gross_transaction_amount' => [
                        'label' => $settlement->getFieldLabel('gross_transaction_amount'),
                        'value' => Mage::app()->getLocale()
                                       ->currency($model->getData('gross_transaction_currency'))
                                       ->toCurrency($model->getData('gross_transaction_amount')),
                    ],
                ],
                'legend' => Mage::helper('paypal')->__('Transaction Information'),
            ],

            'fee_fieldset' => [
                'fields' => [
                    'fee_debit_or_credit' => [
                        'label' => $settlement->getFieldLabel('fee_debit_or_credit'),
                        'value' => $model->getDebitCreditText($model->getData('fee_debit_or_credit')),
                    ],
                    'fee_amount' => [
                        'label' => $settlement->getFieldLabel('fee_amount'),
                        'value' => Mage::app()->getLocale()
                                       ->currency($model->getData('fee_currency'))
                                       ->toCurrency($model->getData('fee_amount')),
                    ],
                ],
                'legend' => Mage::helper('paypal')->__('PayPal Fee Information'),
            ],
        ];

        $form = new Varien_Data_Form();
        foreach ($fieldsets as $key => $data) {
            $fieldset = $form->addFieldset($key, ['legend' => $data['legend']]);
            foreach ($data['fields'] as $id => $info) {
                $fieldset->addField($id, 'label', [
                    'name'  => $id,
                    'label' => $info['label'],
                    'title' => $info['label'],
                    'value' => $info['value'] ?? $model->getData($id),
                ]);
            }
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
