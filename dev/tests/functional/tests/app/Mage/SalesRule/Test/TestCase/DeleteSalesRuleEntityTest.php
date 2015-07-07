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
