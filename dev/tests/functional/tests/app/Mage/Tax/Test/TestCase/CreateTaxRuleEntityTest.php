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

namespace Mage\Tax\Test\TestCase;

use Mage\Tax\Test\Fixture\TaxRule;
use Mage\Tax\Test\Page\Adminhtml\TaxRuleIndex;
use Mage\Tax\Test\Page\Adminhtml\TaxRuleNew;
use Magento\Mtf\TestCase\Injectable;

/**
 * Steps:
 *
 * 1. Log in to backend.
 * 2. Go to Sales -> Tax -> Manage Tax Rules.
 * 3. Click 'Add New Tax Rule' button.
 * 4. Fill in data according to dataset.
 * 5. Save Tax Rule.
 * 6. Perform all assertions.
 *
 * @group Tax_(CS)
 * @ZephyrId MPERF-6767
 */
class CreateTaxRuleEntityTest extends Injectable
{
    /**
     * Tax rule index page.
     *
     * @var TaxRuleIndex
     */
    protected $taxRuleIndexPage;

    /**
     * Tax rule form page.
     *
     * @var TaxRuleNew
     */
    protected $taxRuleNewPage;

    /**
     * Injection data.
     *
     * @param TaxRuleIndex $taxRuleIndexPage
     * @param TaxRuleNew $taxRuleNewPage
     * @return void
     */
    public function __inject(TaxRuleIndex $taxRuleIndexPage, TaxRuleNew $taxRuleNewPage)
    {
        $this->taxRuleIndexPage = $taxRuleIndexPage;
        $this->taxRuleNewPage = $taxRuleNewPage;
    }

    /**
     * Test create tax rule.
     *
     * @param TaxRule $taxRule
     * @return void
     */
    public function test(TaxRule $taxRule)
    {
        // Steps
        $this->taxRuleIndexPage->open();
        $this->taxRuleIndexPage->getPageActionsBlock()->addNew();
        $this->taxRuleNewPage->getTaxRuleForm()->fill($taxRule);
        $this->taxRuleNewPage->getFormPageActions()->save();
    }

    /**
     * Delete all tax rules.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->objectManager->create('Mage\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();
    }
}
