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

namespace Mage\Tax\Test\TestCase;

use Mage\Tax\Test\Fixture\TaxRate;
use Mage\Tax\Test\Page\Adminhtml\TaxRateIndex;
use Mage\Tax\Test\Page\Adminhtml\TaxRateNew;
use Magento\Mtf\TestCase\Injectable;

/**
 * Steps:
 * 1. Log in as default admin user.
 * 2. Go to Sales > Tax > Manage Tax Zones and Rates.
 * 3. Click 'Add New Tax Rate' button.
 * 4. Fill in data according to dataset
 * 5. Save Tax Rate.
 * 6. Perform all assertions.
 *
 * @group Tax_(CS)
 * @ZephyrId MPERF-6757
 */
class CreateTaxRateEntityTest extends Injectable
{
    /**
     * Tax Rate grid page.
     *
     * @var TaxRateIndex
     */
    protected $taxRateIndexPage;

    /**
     * Tax Rate new/edit page.
     *
     * @var TaxRateNew
     */
    protected $taxRateNewPage;

    /**
     * Injection data.
     *
     * @param TaxRateIndex $taxRateIndexPage
     * @param TaxRateNew $taxRateNewPage
     * @return void
     */
    public function __inject(TaxRateIndex $taxRateIndexPage, TaxRateNew $taxRateNewPage)
    {
        $this->taxRateIndexPage = $taxRateIndexPage;
        $this->taxRateNewPage = $taxRateNewPage;
    }

    /**
     * Create Tax Rate Entity test.
     *
     * @param TaxRate $taxRate
     * @return void
     */
    public function test(TaxRate $taxRate)
    {
        // Steps:
        $this->taxRateIndexPage->open();
        $this->taxRateIndexPage->getPageActionsBlock()->addNew();
        $this->taxRateNewPage->getTaxRateForm()->fill($taxRate);
        $this->taxRateNewPage->getFormPageActions()->save();
    }
}
