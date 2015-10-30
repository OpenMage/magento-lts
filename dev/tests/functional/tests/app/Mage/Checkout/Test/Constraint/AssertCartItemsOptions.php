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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\Constraint;

use Mage\Checkout\Test\Block\Cart\CartItem;

/**
 * Assert that cart item options for product(s) display with correct information block.
 *
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class AssertCartItemsOptions extends AbstractAssertProductInShoppingCart
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Data type.
     *
     * @var string
     */
    protected $dataType = 'options';

    /**
     * Error message for verify options.
     *
     * @var string
     */
    protected $errorMessage = '- %s: "%s" instead of "%s"';

    /**
     * Get cart data.
     *
     * @param CartItem $cartItem
     * @return array
     */
    protected function getCartData(CartItem $cartItem)
    {
        return [$this->dataType => $this->sortDataByPath($cartItem->getOptions(), '::title'),];
    }

    /**
     * Get product data.
     *
     * @param array $checkoutItem
     * @param array $verifyData
     * @return array
     */
    protected function getProductData(array $checkoutItem, array $verifyData)
    {
        return [$this->dataType => $this->sortDataByPath($checkoutItem[$this->dataType], '::title')];
    }

    /**
     * Verify form data contains in fixture data.
     *
     * @param array $fixtureData
     * @param array $formData
     * @param bool $isStrict [optional]
     * @param bool $isPrepareError [optional]
     * @return array|string
     */
    protected function verifyContainsData(
        array $fixtureData,
        array $formData,
        $isStrict = false,
        $isPrepareError = true
    ) {
        $errors = [];

        foreach ($fixtureData as $key => $value) {
            if (in_array($key, $this->skippedFields)) {
                continue;
            }

            $formValue = isset($formData[$key]) ? $formData[$key] : null;
            if ($formValue && !is_array($formValue)) {
                $formValue = trim($formValue, '. ');
            }

            if (null === $formValue) {
                $errors[] = '- field "' . $key . '" is absent in form';
            } elseif (is_array($value) && is_array($formValue)) {
                $valueErrors = $this->verifyContainsData($value, $formValue, true, false);
                if (!empty($valueErrors)) {
                    $errors[$key] = $valueErrors;
                }
            } elseif (($key == 'value') && $this->equals($fixtureData['value'], $formData['value'])) {
                $errors[] = $this->errorFormat($value, $formValue, $key);
            } elseif (null === strpos($value, $formValue)) {
                $errors[] = $this->errorFormat($value, $formValue, $key);
            }
        }

        if ($isStrict) {
            $diffData = array_diff(array_keys($formData), array_keys($fixtureData));
            if ($diffData) {
                $errors[] = '- fields ' . implode(', ', $diffData) . ' is absent in fixture';
            }
        }

        if ($isPrepareError) {
            return $this->prepareErrors($errors);
        }
        return $errors;
    }

    /**
     * Check that params are equals.
     *
     * @param mixed $expected
     * @param mixed $actual
     * @return bool
     */
    protected function equals($expected, $actual)
    {
        return (null === strpos($expected, $actual));
    }

    /**
     * Format error.
     *
     * @param mixed $value
     * @param mixed $formValue
     * @param mixed $key
     * @return string
     */
    protected function errorFormat($value, $formValue, $key)
    {
        if (is_array($value)) {
            $value = $this->arrayToString($value);
        }
        if (is_array($formValue)) {
            $formValue = $this->arrayToString($formValue);
        }

        return sprintf($this->errorMessage, $key, $formValue, $value);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product options on the page match.';
    }
}
