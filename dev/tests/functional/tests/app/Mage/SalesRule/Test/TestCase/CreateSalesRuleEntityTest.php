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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\SalesRule\Test\TestCase;

use Magento\Mtf\TestCase\Injectable;
use Magento\Mtf\Fixture\FixtureFactory;
use Mage\Customer\Test\Fixture\Customer;
use Mage\SalesRule\Test\Fixture\SalesRule;
use Mage\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Mage\SalesRule\Test\Page\Adminhtml\PromoQuoteNew;
use Mage\SalesRule\Test\Page\Adminhtml\PromoQuoteEdit;

/**
 * Precondition:
 * 1. Create two simple products with categories.
 * 2. Create default customer.
 *
 * Steps:
 * 1. Login to the backend.
 * 2. Navigate to Promotions -> Shopping Cart Price Rules.
 * 3. Click "Add new rule" button.
 * 4. Fill form according to data set.
 * 5. Click "Save" button.
 * 6. Perform asserts.
 *
 * @group Shopping_Cart_Price_Rules_(CS)
 * @ZephyrId MPERF-6748
 */
class CreateSalesRuleEntityTest extends Injectable
{
    /**
     * Page PromoQuoteNew.
     *
     * @var PromoQuoteNew
     */
    protected $promoQuoteNew;

    /**
     * Page PromoQuoteEdit.
     *
     * @var PromoQuoteEdit
     */
    protected $promoQuoteEdit;

    /**
     * Page PromoQuoteIndex.
     *
     * @var PromoQuoteIndex
     */
    protected $promoQuoteIndex;

    /**
     * Sales rule name.
     *
     * @var string
     */
    protected $salesRuleName;

    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Create customer and 2 simple products with categories before run test.
     *
     * @param FixtureFactory $fixtureFactory
     * @param Customer $customer
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory, Customer $customer)
    {
        $this->fixtureFactory = $fixtureFactory;
        $customer->persist();
        $products = $this->createProducts(['simple_for_salesrule_1', 'simple_for_salesrule_2']);

        return [
            'customer' => $customer,
            'productForSalesRule1' => $products[0],
            'productForSalesRule2' => $products[1]
        ];
    }

    /**
     * Inject data.
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param PromoQuoteNew $promoQuoteNew
     * @param PromoQuoteEdit $promoQuoteEdit
     * @return void
     */
    public function __inject(
        PromoQuoteIndex $promoQuoteIndex,
        PromoQuoteNew $promoQuoteNew,
        PromoQuoteEdit $promoQuoteEdit
    ) {
        $this->promoQuoteIndex = $promoQuoteIndex;
        $this->promoQuoteNew = $promoQuoteNew;
        $this->promoQuoteEdit = $promoQuoteEdit;
    }

    /**
     * Create Sales Rule.
     *
     * @param SalesRule $salesRule
     * @return void
     */
    public function test(SalesRule $salesRule)
    {
        // Prepare data for tearDown
        $this->salesRuleName = $salesRule->getName();

        // Steps
        $this->promoQuoteIndex->open();
        $this->promoQuoteIndex->getGridPageActions()->addNew();
        $this->promoQuoteNew->getSalesRuleForm()->fill($salesRule);
        $this->promoQuoteNew->getFormPageActions()->save();
    }

    /**
     * Create products.
     *
     * @param array $productsdatasets
     * @return array
     */
    protected function createProducts(array $productsdatasets)
    {
        $products = [];
        foreach ($productsdatasets as $dataset){
            $product = $this->fixtureFactory->createByCode('catalogProductSimple', ['dataset' => $dataset]);
            $product->persist();
            $products[] = $product;
        }
        return $products;
    }

    /**
     * Delete sales rule.
     *
     * @return void
     */
    public function tearDown()
    {
        if ($this->salesRuleName !== null) {
            $this->promoQuoteIndex->open();
            $this->promoQuoteIndex->getPromoQuoteGrid()->searchAndOpen(['name' => $this->salesRuleName]);
            $this->promoQuoteEdit->getFormPageActions()->delete();
            $this->salesRuleName = null;
        }
    }
}
