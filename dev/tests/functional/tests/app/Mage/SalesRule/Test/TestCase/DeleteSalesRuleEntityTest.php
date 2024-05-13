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

namespace Mage\SalesRule\Test\TestCase;

use Mage\SalesRule\Test\Fixture\SalesRule;
use Mage\SalesRule\Test\Page\Adminhtml\PromoQuoteEdit;
use Mage\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Magento\Mtf\TestCase\Injectable;

/**
 * Precondition:
 * 1. Shopping cart price rule is created.
 *
 * Steps:
 * 1. Login to backend.
 * 2. Navigate to Promotions > Shopping Cart Price Rules.
 * 3. Open shopping cart price rule from preconditions.
 * 4. Click 'Delete' button.
 * 5. Perform asserts.
 *
 * @group Shopping_Cart_Price_Rules_(MX)
 * @ZephyrId MPERF-7632
 */
class DeleteSalesRuleEntityTest extends Injectable
{
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
     * Inject data.
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param PromoQuoteEdit $promoQuoteEdit
     * @return void
     */
    public function __inject(PromoQuoteIndex $promoQuoteIndex, PromoQuoteEdit $promoQuoteEdit)
    {
        $this->promoQuoteIndex = $promoQuoteIndex;
        $this->promoQuoteEdit = $promoQuoteEdit;
    }

    /**
     * Delete Sales Rule Entity test.
     *
     * @param SalesRule $salesRule
     * @return void
     */
    public function test(SalesRule $salesRule)
    {
        // Preconditions:
        $salesRule->persist();
        // Steps:
        $this->promoQuoteIndex->open();
        $this->promoQuoteIndex->getPromoQuoteGrid()->searchAndOpen(['name' => $salesRule->getName()]);
        $this->promoQuoteEdit->getFormPageActions()->delete();
    }
}
