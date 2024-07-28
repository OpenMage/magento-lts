<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\TestStep\UpdateConfigurableProductStep;

/**
 * Update options sub step.
 */
class UpdateOptionsSubStep extends AbstractSubStep
{
    /**
     * Update configurable options.
     *
     * @return void
     */
    public function run()
    {
        $this->prepareOptionsForEdit();
        $this->fillAttributes($this->currentConfigurableOptionsData);
    }

    /**
     * Return arguments from sub step.
     *
     * @return array
     */
    public function returnArguments()
    {
        return [
            'currentConfigurableOptionsData' => $this->currentConfigurableOptionsData
        ];
    }

    /**
     * Prepare options for edit.
     *
     * @return void
     */
    protected function prepareOptionsForEdit()
    {
        foreach ($this->configurableOptionsEditData['updateOptions'] as $editOption) {
            $attributeKey = 'attribute_key_' . $editOption['attributeIndex'];
            $optionKey = 'option_key_' . $editOption['optionIndex'];
            $this->currentConfigurableOptionsData[$attributeKey]['options'][$optionKey] = array_replace(
                $this->currentConfigurableOptionsData[$attributeKey]['options'][$optionKey],
                $editOption['value']
            );
        }
    }
}
