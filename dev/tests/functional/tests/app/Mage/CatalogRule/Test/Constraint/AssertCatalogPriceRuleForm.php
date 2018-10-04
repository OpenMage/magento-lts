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

namespace Mage\CatalogRule\Test\Constraint;

use Mage\CatalogRule\Test\Fixture\CatalogRule;
use Mage\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;
use Mage\CatalogRule\Test\Page\Adminhtml\CatalogRuleEdit;
use Magento\Mtf\Constraint\AbstractAssertForm;

/**
 * Check that displayed Catalog Price Rule data on edit page equals passed from fixture.
 */
class AssertCatalogPriceRuleForm extends AbstractAssertForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Skipped fields for verify data.
     *
     * @var array
     */
    protected $skippedFields = ['conditions'];

    /**
     * Assert that displayed Catalog Price Rule data on edit page equals passed from fixture.
     *
     * @param CatalogRule $catalogPriceRule
     * @param CatalogRuleIndex $pageCatalogRuleIndex
     * @param CatalogRuleEdit $catalogRuleEdit
     * @return void
     */
    public function processAssert(
        CatalogRule $catalogPriceRule,
        CatalogRuleIndex $pageCatalogRuleIndex,
        CatalogRuleEdit $catalogRuleEdit
    ) {
        $filter['name'] = $catalogPriceRule->getName();

        $pageCatalogRuleIndex->open();
        $pageCatalogRuleIndex->getCatalogRuleGrid()->searchAndOpen($filter);
        $formData = $this->prepareFormData($catalogRuleEdit->getEditForm()->getData($catalogPriceRule));
        $fixtureData = $this->prepareFixtureData($catalogPriceRule->getData());
        $diff = $this->verifyData($formData, $fixtureData);

        \PHPUnit_Framework_Assert::assertEmpty($diff, $diff);
    }

    /**
     * Prepare fixture data.
     *
     * @param array $fixtureData
     * @return array
     */
    protected function prepareFixtureData(array $fixtureData)
    {
        if (isset($fixtureData['discount_amount'])) {
            $fixtureData['discount_amount'] = floatval($fixtureData['discount_amount']);
        }

        return $fixtureData;
    }

    /**
     * Prepare form data.
     *
     * @param array $formData
     * @return array
     */
    protected function prepareFormData(array $formData)
    {
        if (isset($formData['discount_amount'])) {
            $formData['discount_amount'] = floatval($formData['discount_amount']);
        }

        return $formData;
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed catalog price rule data on edit page(backend) equals to passed from fixture.';
    }
}
