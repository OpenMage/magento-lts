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

namespace Mage\Customer\Test\Block\Form;

use Magento\Mtf\Block\Form;
use Mage\Customer\Test\Fixture\Customer;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Customer account edit form.
 */
class CustomerForm extends Form
{
    /**
     * Save button button css selector.
     *
     * @var string
     */
    protected $saveButton = '[type="submit"]';

    /**
     * Locator for customer attribute on Edit Account Information page.
     *
     * @var string
     */
    protected $customerAttribute = "[name='%s[]']";

    /**
     * Validation text message for a field.
     *
     * @var string
     */
    protected $validationText = '[id="%s"]';

    /**
     * Click on save button.
     *
     * @return void
     */
    public function submit()
    {
        $this->_rootElement->find($this->saveButton)->click();
    }

    /**
     * Get all error validation messages for fields.
     *
     * @param Customer $customer
     * @return array
     */
    public function getValidationMessages(Customer $customer)
    {
        $messages = [];
        foreach (array_keys($customer->getData()) as $field) {
            $element = $this->_rootElement->find(
                sprintf($this->validationText, 'advice-validate-c' . str_replace('_', '-', $field))
            );
            if ($element->isVisible()) {
                $messages[$field] = $element->getText();
            }
        }
        return $messages;
    }
}
