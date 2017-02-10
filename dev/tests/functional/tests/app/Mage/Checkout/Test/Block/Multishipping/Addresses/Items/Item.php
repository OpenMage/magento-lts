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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
