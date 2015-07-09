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

namespace Mage\SalesRule\Test\Constraint;

use Magento\Mtf\Constraint\AbstractAssertForm;
use Mage\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Mage\SalesRule\Test\Page\Adminhtml\PromoQuoteEdit;
use Mage\SalesRule\Test\Fixture\SalesRule;

/**
 * Assert that displayed sales rule data on edit page equals passed from fixture.
 */
class AssertCartPriceRuleForm extends AbstractAssertForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Skipped fields for verify data.
     *
     * @var array
     */
    protected $skippedFields = ['conditions_serialized'];

    /**
     * Assert that displayed sales rule data on edit page equals passed from fixture.
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param PromoQuoteEdit $promoQuoteEdit
     * @param SalesRule $salesRule
     * @return void
     */
    public function processAssert(
        PromoQuoteIndex $promoQuoteIndex,
        PromoQuoteEdit $promoQuoteEdit,
        SalesRule $salesRule
    ) {
        $promoQuoteIndex->open();
        $promoQuoteIndex->getPromoQuoteGrid()->searchAndOpen(['name' => $salesRule->getName()]);
        $formData = $promoQuoteEdit->getSalesRuleForm()->getData();
        $errors = $this->verifyData($salesRule->getData(), $formData);
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed sales rule data on edit page equals to passed from fixture.';
    }
}
