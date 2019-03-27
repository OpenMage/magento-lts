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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\TestStep;

use Mage\Checkout\Test\Page\CheckoutMultishippingAddresses;
use Mage\Customer\Test\Fixture\Address;
use Mage\Customer\Test\Fixture\Customer;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Select addresses step on checkout with multishipping address.
 */
class SelectAddressesStep implements TestStepInterface
{
    /**
     * Checkout multishipping addresses page.
     *
     * @var CheckoutMultishippingAddresses
     */
    protected $checkoutMultishippingAddresses;

    /**
     * Address fixture.
     *
     * @var array
     */
    protected $addresses;

    /**
     * Customer fixture.
     *
     * @var Customer
     */
    protected $customer;

    /**
     * Array products fixtures.
     *
     * @var array
     */
    protected $products;

    /**
     * Data for fill items.
     *
     * @var array
     */
    protected $fillItemsData;

    /**
     * @constructor
     * @param CheckoutMultishippingAddresses $checkoutMultishippingAddresses
     * @param array $products
     * @param array $fillItemsData [optional]
     * @param Address[] $addresses [optional]
     * @param Customer|null $customer
     */
    public function __construct(
        CheckoutMultishippingAddresses $checkoutMultishippingAddresses,
        array $products,
        array $fillItemsData = [],
        array $addresses = [],
        Customer $customer = null
    ) {
        $this->checkoutMultishippingAddresses = $checkoutMultishippingAddresses;
        $this->products = $products;
        $this->addresses = $addresses;
        $this->customer = $customer;
        $this->fillItemsData = $fillItemsData;
    }

    /**
     * Select addresses.
     *
     * @return array
     */
    public function run()
    {
        $addresses = $this->getAddresses();
        if (!empty($this->fillItemsData)) {
            foreach ($this->fillItemsData as $key => $itemData) {
                $this->checkoutMultishippingAddresses->getAddressesBlock()->getItemsBlock()
                    ->getItemBlock($this->products[$itemData['productIndex']], $key)
                    ->fillItem($addresses[$itemData['addressIndex']]);
            }
            $this->checkoutMultishippingAddresses->getAddressesBlock()->getItemsBlock()->updateData();
        }
        $this->checkoutMultishippingAddresses->getAddressesBlock()->clickContinueButton();

        return ['addresses' => $addresses];
    }

    /**
     * Get addresses.
     *
     * @return array
     */
    protected function getAddresses()
    {
        $addresses = [];
        if ($this->customer->hasData('address')) {
            $addresses = $this->customer->getDataFieldConfig('address')['source']->getAddresses();
        }
        return array_merge($addresses, $this->addresses);
    }
}
