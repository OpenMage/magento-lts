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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

/**
 * Assert form data equals fixture data.
 */
class AssertConfigurableProductForm extends AssertProductForm
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * List skipped attribute fields in verify.
     *
     * @var array
     */
    protected $skippedAttributeFields = [
        'frontend_input',
        'attribute_code',
        'attribute_id',
        'label'
    ];

    /**
     * List skipped option fields in verify.
     *
     * @var array
     */
    protected $skippedOptionFields = [
        'label',
    ];

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Prepares fixture data for comparison.
     *
     * @param array $data
     * @param array $sortFields [optional]
     * @return array
     */
    protected function prepareFixtureData(array $data, array $sortFields = [])
    {
        // filter values and reset keys in attributes data
        if (isset($data['configurable_options'])) {
            $attributeData = $data['configurable_options']['attributes_data'];
            foreach ($attributeData as $attributeKey => $attribute) {
                foreach ($attribute['options'] as $optionKey => $option) {
                    $attribute['options'][$optionKey] = array_diff_key($option, array_flip($this->skippedOptionFields));
                }
                $attribute['options'] = $this->sortDataByPath($attribute['options'], '::admin');
                $attributeData[$attributeKey] = array_diff_key($attribute, array_flip($this->skippedAttributeFields));
            }
            $data['configurable_options']['attributes_data'] = $this->sortDataByPath(
                $attributeData,
                '::frontend_label'
            );
        }

        return parent::prepareFixtureData($data, $sortFields);
    }

    /**
     * Prepares form data for comparison.
     *
     * @param array $data
     * @param array $sortFields [optional]
     * @return array
     */
    protected function prepareFormData(array $data, array $sortFields = [])
    {
        // prepare attributes data
        if (isset($data['configurable_options'])) {
            $attributeData = $data['configurable_options']['attributes_data'];
            foreach ($attributeData as $attributeKey => $attribute) {
                $attribute['options'] = $this->sortDataByPath($attribute['options'], '::admin');
                $attributeData[$attributeKey] = $attribute;
            }
            $data['configurable_options']['attributes_data'] = $this->sortDataByPath(
                $attributeData,
                '::frontend_label'
            );

            foreach ($sortFields as $path) {
                $data = $this->sortDataByPath($data, $path);
            }
        }
        return $data;
    }
}
