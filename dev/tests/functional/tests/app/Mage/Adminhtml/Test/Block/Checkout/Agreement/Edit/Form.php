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

namespace Mage\Adminhtml\Test\Block\Checkout\Agreement\Edit;

use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Form for creation of the term.
 */
class Form extends \Magento\Mtf\Block\Form
{
    /**
     * Selector for store view field.
     *
     * @var string
     */
    protected $storeView = '#store_id';

    /**
     * Fill form.
     *
     * @param FixtureInterface $checkoutAgreement
     * @param Element|null $element
     * @return $this
     */
    public function fill(FixtureInterface $checkoutAgreement, Element $element = null)
    {
        parent::fill($checkoutAgreement, $element);
        $this->fillWebsite();

        return $this;
    }

    /**
     * Fill website field.
     *
     * @return void
     */
    protected function fillWebsite()
    {
        $storeViewField = $this->_rootElement->find($this->storeView, Locator::SELECTOR_CSS, 'multiselectgrouplist');
        if ($storeViewField->isVisible() && !$storeViewField->getValue()) {
            $storeViewField->setValue('All Store Views');
        }
    }
}
