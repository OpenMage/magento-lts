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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\Block\Widget\Guest;

use Mage\Customer\Test\Fixture\Customer;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Client\Element\SimpleElement;
use Mage\Sales\Test\Fixture\Order;

/**
 * Orders and Returns form search block.
 */
class Form extends \Magento\Mtf\Block\Form
{
    /**
     * Search button selector.
     *
     * @var string
     */
    protected $searchButtonSelector = '.buttons-set .button';

    /**
     * Selector for loads form.
     *
     * @var string
     */
    protected $loadsForm = 'div[id*=oar] input';

    /**
     * Fill the form.
     *
     * @param FixtureInterface $fixture
     * @param SimpleElement|null $element
     * @param bool $isSearchByEmail [optional]
     * @return $this
     */
    public function fill(FixtureInterface $fixture, SimpleElement $element = null, $isSearchByEmail = true)
    {
        $data = $this->prepareData($fixture, $isSearchByEmail);

        $fields = isset($data['fields']) ? $data['fields'] : $data;
        $mapping = $this->dataMapping($fields);
        $this->_fill($mapping, $element);

        return $this;
    }

    /**
     * Prepare data.
     *
     * @param FixtureInterface $fixture
     * @param $isSearchByEmail
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture, $isSearchByEmail)
    {
        /** @var Order $fixture */
        /** @var Customer $customer */
        $customer = $fixture->getDataFieldConfig('customer_id')['source']->getCustomer();
        $data = [
            'order_id' => $fixture->getId(),
            'billing_last_name' => $customer->getLastname(),
        ];
        if ($isSearchByEmail) {
            $data['find_order_by'] = 'Email';
            $data['email_address'] = $customer->getEmail();
        } else {
            $data['find_order_by'] = 'ZIP Code';
            $data['billing_zip_code'] = $fixture->getDataFieldConfig('billing_address_id')['source']->getPostcode();
        }

        return $data;
    }

    /**
     * Submit search form.
     *
     * @return void
     */
    public function submit()
    {
        $this->_rootElement->find($this->searchButtonSelector)->click();
    }
}
