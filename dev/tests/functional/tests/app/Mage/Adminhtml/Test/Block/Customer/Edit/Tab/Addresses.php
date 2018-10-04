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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Customer\Edit\Tab;

use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Mage\Customer\Test\Fixture\Address;
use Mage\Adminhtml\Test\Block\Widget\Tab;
use Mage\Adminhtml\Test\Block\Template;

/**
 * Customer addresses edit block.
 */
class Addresses extends Tab
{
    /**
     * "Add New Customer" button.
     *
     * @var string
     */
    protected $addNewAddress = '#add_address_button';

    /**
     * Open customer address.
     *
     * @var string
     */
    protected $customerAddress = '//*[@id="address_list"]/li[%d]/a';

    /**
     * Backend abstract block selector.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Get backend abstract block.
     *
     * @return Template
     */
    protected function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Fill customer addresses.
     *
     * @param Address|Address[] $address
     * @return $this
     */
    public function fillAddresses($address)
    {
        $addresses = is_array($address) ? $address : [$address];
        foreach ($addresses as $address) {
            $this->addNewAddress();

            /* Fix switch between region_id and region */
            /** @var Address $address */
            $countryId = $address->getCountryId();
            if ($countryId && $this->mapping['country_id']) {
                $this->_fill($this->dataMapping(['country_id' => $countryId]));
                $this->getTemplateBlock()->waitLoader();
            }

            $this->fillFormTab($address->getData(), $this->_rootElement);
        }

        return $this;
    }

    /**
     * Update customer addresses.
     *
     * @param Address|Address[] $address
     * @return $this
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function updateAddresses($address)
    {
        $addresses = is_array($address) ? $address : [1 => $address];
        foreach ($addresses as $addressNumber => $address) {
            /* Throw exception if isn't exist previous customer address. */
            if (1 < $addressNumber && !$this->isVisibleCustomerAddress($addressNumber - 1)) {
                throw new \Exception("Invalid argument: can't update customer address #{$addressNumber}");
            }

            if (!$this->isVisibleCustomerAddress($addressNumber)) {
                $this->addNewAddress();
            }
            $this->openCustomerAddress($addressNumber);

            /* Fix switch between region_id and region */
            /** @var Address $address */
            $countryId = $address->getCountryId();
            if ($countryId && $this->mapping['country_id']) {
                $this->_fill($this->dataMapping(['country_id' => $countryId]));
                $this->getTemplateBlock()->waitLoader();
            }
            $defaultAddress = ['default_billing' => 'No', 'default_shipping' => 'No'];
            $addressData = $address->getData();
            foreach ($defaultAddress as $key => $value) {
                if (isset($addressData[$key])) {
                    $defaultAddress[$key] = $value;
                }
            }
            $this->_fill($this->dataMapping($defaultAddress));

            $this->fillFormTab(array_diff($addressData, $defaultAddress), $this->_rootElement);
        }

        return $this;
    }

    /**
     * Get data of Customer addresses.
     *
     * @param Address|Address[]|null $address
     * @return array
     * @throws \Exception
     */
    public function getDataAddresses($address = null)
    {
        $data = [];
        $addresses = is_array($address) ? $address : [1 => $address];

        foreach ($addresses as $addressNumber => $address) {
            $isHasData = (null === $address) || $address->hasData();
            $isVisibleCustomerAddress = $this->isVisibleCustomerAddress($addressNumber);

            if ($isHasData && !$isVisibleCustomerAddress) {
                throw new \Exception("Invalid argument: can't get data from customer address #{$addressNumber}");
            }

            if (!$isHasData && !$isVisibleCustomerAddress) {
                $data[$addressNumber] = [];
            } else {
                $this->openCustomerAddress($addressNumber);
                $data[$addressNumber] = $this->getData($address, $this->_rootElement);
            }
        }

        return $data;
    }

    /**
     * Get data to fields on tab.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        /* Skip get data for standard method. Use getDataAddresses. */
        return [];
    }

    /**
     * Click "Add New Address" button.
     *
     * @return void
     */
    protected function addNewAddress()
    {
        $this->_rootElement->find($this->addNewAddress)->click();
    }

    /**
     * Open customer address.
     *
     * @param int $addressNumber
     * @throws \Exception
     * @return void
     */
    protected function openCustomerAddress($addressNumber)
    {
        $addressTab = $this->_rootElement->find(
            sprintf($this->customerAddress, $addressNumber),
            Locator::SELECTOR_XPATH
        );

        if (!$addressTab->isVisible()) {
            throw new \Exception("Can't open customer address #{$addressNumber}");
        }
        $addressTab->click();
    }

    /**
     * Check is visible customer address.
     *
     * @param int $addressNumber
     * @return bool
     */
    protected function isVisibleCustomerAddress($addressNumber)
    {
        $addressTab = $this->_rootElement->find(
            sprintf($this->customerAddress, $addressNumber),
            Locator::SELECTOR_XPATH
        );
        return $addressTab->isVisible();
    }
}
