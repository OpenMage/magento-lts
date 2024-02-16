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

namespace Mage\Customer\Test\Block\Form;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Register new customer on Frontend.
 */
class Register extends Form
{
    /**
     * 'Submit' form button.
     *
     * @var string
     */
    protected $submit = '.buttons-set .button';

    /**
     * Create new customer account and fill billing address if it exists.
     *
     * @param FixtureInterface $fixture
     * @return void
     */
    public function registerCustomer(FixtureInterface $fixture)
    {
        $mapping = $this->dataMapping($fixture->getData());
        unset($mapping['id']);
        $this->_fill($mapping);
        $this->_rootElement->find($this->submit, Locator::SELECTOR_CSS)->click();
    }
}
