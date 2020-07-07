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

namespace Mage\Tax\Test\Constraint;

use Mage\Tax\Test\Fixture\TaxRate;
use Mage\Tax\Test\Page\Adminhtml\TaxRateIndex;
use Mage\Tax\Test\Page\Adminhtml\TaxRateNew;
use Magento\Mtf\Constraint\AbstractAssertForm;

/**
 * Assert that tax rate form filled correctly.
 */
class AssertTaxRateForm extends AbstractAssertForm
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Assert that tax rate form filled correctly.
     *
     * @param TaxRateIndex $taxRateIndex
     * @param TaxRateNew $taxRateNew
     * @param TaxRate $taxRate
     * @return void
     */
    public function processAssert(TaxRateIndex $taxRateIndex, TaxRateNew $taxRateNew, TaxRate $taxRate)
    {
        $data = $this->prepareData($taxRate->getData());

        $taxRateIndex->open()->getTaxRatesGrid()->searchAndOpen(['code' => $data['code']]);
        $formData = $taxRateNew->getTaxRateForm()->getData($taxRate);
        $errors = $this->verifyData($data, $formData);

        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Preparing data for verification.
     *
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        if ($data['zip_is_range'] === 'Yes') {
            unset($data['tax_postcode']);
        } else {
            unset($data['zip_from'], $data['zip_to']);
        }
        $data['rate'] = number_format($data['rate'], 4);

        return $data;
    }

    /**
     * Returns string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Tax Rate form was filled correctly.';
    }
}
