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

namespace Mage\Paypal\Test\Block;

use Mage\Paypal\Test\Fixture\PaypalAddress;
use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Pay Pal sandbox old addresses block.
 */
class OldAddresses extends Block
{
    /**
     * Address selector.
     *
     * @var string
     */
    protected $addressSelector = './/input[following-sibling::span[contains(.,"%s")]]';

    /**
     * Save button selector.
     *
     * @var string
     */
    protected $saveButton = "#continueBabySlider";

    /**
     * Select address by criteria.
     *
     * @param PaypalAddress $address
     * @param string $criteria [optional]
     * @throws \Exception
     * @return void
     */
    public function selectAddress(PaypalAddress $address, $criteria = 'street')
    {
        if ($address->hasData($criteria)) {
            $addressSelector = sprintf($this->addressSelector, $address->getData($criteria));
            $this->_rootElement->find($addressSelector, Locator::SELECTOR_XPATH, 'checkbox')->setValue('Yes');
            $this->_rootElement->find($this->saveButton)->click();
        } else {
            throw new \Exception("$criteria field is absent in provided address fixture.");
        }
    }
}
