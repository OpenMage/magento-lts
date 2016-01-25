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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\CatalogRule\Test\Constraint;

use Mage\CatalogRule\Test\Fixture\CatalogRule;
use Mage\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Check that data in grid on Catalog Price Rules page according to fixture.
 */
class AssertCatalogPriceRuleInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that data in grid on Catalog Price Rules page according to fixture.
     *
     * @param CatalogRule $catalogPriceRule
     * @param CatalogRuleIndex $pageCatalogRuleIndex
     * @return void
     */
    public function processAssert(CatalogRule $catalogPriceRule, CatalogRuleIndex $pageCatalogRuleIndex)
    {
        $data = $catalogPriceRule->getData();
        $filter = [
            'name' => $data['name'],
            'is_active' => $data['is_active'],
        ];
        //add ruleWebsite to filter if there is one
        if ($catalogPriceRule->hasData('website_ids')) {
            $ruleWebsite = $catalogPriceRule->getWebsiteIds();
            $ruleWebsite = is_array($ruleWebsite) ? reset($ruleWebsite) : $ruleWebsite;
            $filter['rule_website'] = $ruleWebsite;
        }
        //add from_date & to_date to filter if there are ones
        if (isset($data['from_date']) && isset($data['to_date'])) {
            $dateArray['from_date'] = date("M j, Y", strtotime($catalogPriceRule->getFromDate()));
            $dateArray['to_date'] = date("M j, Y", strtotime($catalogPriceRule->getToDate()));
            $filter = array_merge($filter, $dateArray);
        }

        $pageCatalogRuleIndex->open();
        $errorMessage = implode(', ', $filter);
        \PHPUnit_Framework_Assert::assertTrue(
            $pageCatalogRuleIndex->getCatalogRuleGrid()->isRowVisible($filter),
            'Catalog Price Rule with following data: \'' . $errorMessage . '\' '
            . 'is absent in Catalog Price Rule grid.'
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return 'Catalog Price Rule is present in Catalog Rule grid.';
    }
}
