<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\TestStep;

use Mage\Checkout\Test\Page\Adminhtml\CheckoutAgreementIndex;
use Mage\Checkout\Test\Page\Adminhtml\CheckoutAgreementEdit;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Delete all terms on backend.
 */
class DeleteAllTermsEntityStep implements TestStepInterface
{
    /**
     * Checkout agreement index page.
     *
     * @var CheckoutAgreementIndex
     */
    protected $agreementIndex;

    /**
     * Checkout agreement edit page.
     *
     * @var CheckoutAgreementEdit
     */
    protected $agreementEdit;

    /**
     * @constructor
     * @param CheckoutAgreementEdit $agreementEdit
     * @param CheckoutAgreementIndex $agreementIndex
     */
    public function __construct(CheckoutAgreementEdit $agreementEdit, CheckoutAgreementIndex $agreementIndex)
    {
        $this->agreementEdit = $agreementEdit;
        $this->agreementIndex = $agreementIndex;
    }

    /**
     * Delete all terms on backend.
     *
     * @return void
     */
    public function run()
    {
        $this->agreementIndex->open();
        while ($this->agreementIndex->getAgreementGridBlock()->isFirstRowVisible()) {
            $this->agreementIndex->getAgreementGridBlock()->openFirstRow();
            $this->agreementEdit->getPageActionsBlock()->deleteAndAcceptAlert();
        }
    }
}
