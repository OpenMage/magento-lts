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

namespace Mage\Tax\Test\TestCase;

use Mage\Tax\Test\Fixture\TaxRate;
use Mage\Tax\Test\Page\Adminhtml\TaxRateIndex;
use Mage\Tax\Test\Page\Adminhtml\TaxRateEdit;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 * 1. Create Tax Rate.
 *
 * Steps:
 * 1. Log in as default admin user.
 * 2. Go to Sales -> Tax -> Manage Tax Zones and Rates.
 * 3. Open created tax rate.
 * 4. Click "Delete Rate" button.
 * 5. Perform all assertions.
 *
 * @group Tax_(CS)
 * @ZephyrId MPERF-7656
 */
class DeleteTaxRateEntityTest extends Injectable
{
    /**
     * Tax Rate grid page.
     *
     * @var TaxRateIndex
     */
    protected $taxRateIndex;

    /**
     * Tax Rate edit page.
     *
     * @var TaxRateEdit
     */
    protected $taxRateEdit;

    /**
     * Injection data.
     *
     * @param TaxRateIndex $taxRateIndex
     * @param TaxRateEdit $taxRateEdit
     * @return void
     */
    public function __inject(TaxRateIndex $taxRateIndex, TaxRateEdit $taxRateEdit)
    {
        $this->taxRateIndex = $taxRateIndex;
        $this->taxRateEdit = $taxRateEdit;
    }

    /**
     * Delete Tax Rate Entity test.
     *
     * @param TaxRate $taxRate
     * @return void
     */
    public function testDeleteTaxRate(TaxRate $taxRate)
    {
        // Precondition
        $taxRate->persist();

        // Steps
        $this->taxRateIndex->open();
        $this->taxRateIndex->getTaxRatesGrid()->searchAndOpen(['code' => $taxRate->getCode()]);
        $this->taxRateEdit->getFormPageActions()->deleteAndAcceptAlert();
    }
}
