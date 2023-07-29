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

namespace Mage\Checkout\Test\TestStep;

use Magento\Mtf\TestStep\TestStepInterface;
use Mage\Checkout\Test\Page\Adminhtml\CheckoutAgreementNew;
use Mage\Checkout\Test\Fixture\CheckoutAgreement;

/**
 * Fill and save checkout agreement step.
 */
class FillAndSaveCheckoutAgreementStep implements TestStepInterface
{
    /**
     * Checkout agreement new page.
     *
     * @var CheckoutAgreementNew
     */
    protected $agreementNew;

    /**
     * CheckoutAgreement fixture.
     *
     * @var CheckoutAgreement
     */
    protected $checkoutAgreement;

    /**
     * @constructor
     * @param CheckoutAgreementNew $agreementNew
     * @param CheckoutAgreement $checkoutAgreement
     */
    public function __construct(CheckoutAgreementNew $agreementNew, CheckoutAgreement $checkoutAgreement)
    {
        $this->agreementNew = $agreementNew;
        $this->checkoutAgreement = $checkoutAgreement;
    }

    /**
     * Fill and save checkout agreement step.
     *
     * @return array
     */
    public function run()
    {
        $this->agreementNew->getAgreementsForm()->fill($this->checkoutAgreement);
        $this->agreementNew->getPageActionsBlock()->save();

        return ['checkoutAgreement' => $this->checkoutAgreement];
    }
}
