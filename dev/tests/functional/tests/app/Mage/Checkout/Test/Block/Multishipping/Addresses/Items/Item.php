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

namespace Mage\Checkout\Test\Block\Multishipping\Addresses\Items;

use Mage\Customer\Test\Fixture\Address;
use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Item block on checkout with multishipping address page.
 */
class Item extends Block
{
    /**
     * Selector for address field.
     *
     * @var string
     */
    protected $address = 'select';

    /**
     * Fill item data.
     *
     * @param Address $address
     * @return void
     */
    public function fillItem(Address $address)
    {
        $value = $this->prepareAddressData($address);
        $this->_rootElement->find($this->address, Locator::SELECTOR_CSS, 'select')->setValue($value);
    }

    /**
     * Prepare address data.
     *
     * @param Address $address
     * @return string
     */
    protected function prepareAddressData(Address $address)
    {
        return $address->getFirstname() . ' ' . $address->getLastname() . ', ' . $address->getStreet() . ', '
            . $address->getCity() . ', ' . $address->getRegionId() . ' ' . $address->getPostcode() . ', '
            . $address->getCountryId();
    }
}
