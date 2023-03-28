<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
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
