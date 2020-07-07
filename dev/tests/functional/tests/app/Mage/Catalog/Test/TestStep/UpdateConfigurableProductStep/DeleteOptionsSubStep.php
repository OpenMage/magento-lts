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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\TestStep\UpdateConfigurableProductStep;

/**
 * Delete options sub step.
 */
class DeleteOptionsSubStep extends AbstractSubStep
{
    /**
     * Delete configurable options.
     *
     * @return void
     */
    public function run()
    {
        $this->unsetProductsKeys();
        $this->unsetAttributeOptions();
        $this->getConfigurableProductTab()->unselectAllProducts();
        if (!empty($this->currentAssignedProducts)) {
            $this->selectProducts($this->currentAssignedProducts);
            $this->fillAttributes($this->currentConfigurableOptionsData);
        }
    }

    /**
     * Return arguments from sub step.
     *
     * @return array
     */
    public function returnArguments()
    {
        return [
            'currentAssignedProducts' => $this->currentAssignedProducts,
            'currentConfigurableOptionsData' => $this->currentConfigurableOptionsData
        ];
    }

    /**
     * Unset products.
     *
     * @return void
     */
    protected function unsetProductsKeys()
    {
        $deleteProductsKey = $this->prepareDeleteProductsKeys();
        foreach ($deleteProductsKey as $key) {
            unset($this->currentAssignedProducts[$key]);
        }
    }

    /**
     * Prepare delete products keys.
     *
     * @return array
     */
    protected function prepareDeleteProductsKeys()
    {
        $keys = [];
        foreach ($this->configurableOptionsEditData['deleteOptions'] as $option) {
            $keys = array_merge($keys, $this->searchKeysForOption($option));
        }
        return $keys;
    }

    /**
     * Search keys for option index.
     *
     * @param int $optionIndex
     * @return array
     */
    protected function searchKeysForOption($optionIndex)
    {
        $keys = [];
        $originalProductAssignedProducts = $this->getOriginalProductAssignedProducts();
        foreach ($originalProductAssignedProducts as $key => $product) {
            if (strpos($key, 'option_key_' . $optionIndex) !== false) {
                $keys[] = $key;
            }
        }
        return $keys;
    }

    /**
     * Unset attribute options.
     *
     * @return void
     */
    protected function unsetAttributeOptions()
    {
        foreach ($this->configurableOptionsEditData['deleteOptions'] as $option) {
            foreach ($this->currentConfigurableOptionsData as &$attribute) {
                unset($attribute['options']['option_key_' . $option]);
            }
        }
    }
}
