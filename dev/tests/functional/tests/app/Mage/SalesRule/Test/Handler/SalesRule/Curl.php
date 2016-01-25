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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\SalesRule\Test\Handler\SalesRule;

use Mage\Adminhtml\Test\Fixture\Website;
use Mage\Adminhtml\Test\Handler\Conditions;
use Mage\SalesRule\Test\Fixture\SalesRule;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Curl handler for creating sales rule.
 */
class Curl extends Conditions implements SalesRuleInterface
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'is_active' => [
            'Active' => 1,
            'Inactive' => 0,
        ],
        'coupon_type' => [
            'No Coupon' => 1,
            'Specific Coupon' => 2,
            'Auto' => 3,
        ],
        'is_rss' => [
            'Yes' => 1,
            'No' => 2,
        ],
        'simple_action' => [
            'Percent of product price discount' => 'by_percent',
            'Fixed amount discount' => 'by_fixed',
            'Fixed amount discount for whole cart' => 'cart_fixed',
            'Buy X get Y free (discount amount is Y)' => 'buy_x_get_y',
        ],
        'apply_to_shipping' => [
            'Yes' => 1,
            'No' => 2,
        ],
        'stop_rules_processing' => [
            'Yes' => 1,
            'No' => 2,
        ],
        'simple_free_shipping' => [
            'No' => 0,
            'For matching items only' => 1,
            'For shipment with matching items' => 2,
        ],
        'website_ids' => [
            'Main Website' => 1
        ]
    ];

    /**
     * Map of type parameter.
     *
     * @var array
     */
    protected $mapTypeParams = [
        'Conditions combination' => [
            'type' => 'salesrule/rule_condition_combine',
            'aggregator' => 'all',
            'value' => '1',
        ],
        'Category' => [
            'type' => 'salesrule/rule_condition_product',
            'attribute' => 'category_ids',
        ]
    ];

    /**
     * Mapping values for customer group.
     *
     * @var array
     */
    protected $customerIds = [
        'NOT LOGGED IN' => 0,
        'General' => 1,
        'Wholesale' => 2,
        'Retailer' => 3,
    ];

    /**
     * Post request for creating sales rule.
     *
     * @param FixtureInterface $fixture [optional]
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . 'promo_quote/save/';
        $data = $this->replaceMappingData($fixture->getData());
        $data['customer_group_ids'] = $this->prepareCustomerGroup($data);
        $data['website_ids'] = $this->prepareWebsiteIds($fixture);
        if (isset($data['conditions_serialized'])) {
            $data['rule']['conditions'] = $this->prepareCondition($data['conditions_serialized']);
            unset($data['conditions_serialized']);
        }

        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'class="messages"')) {
            throw new \Exception("Sales rule entity creating by curl handler was not successful! Response: $response");
        }
        $id = $this->getSalesRuleId($response);

        return ['rule_id' => $id];
    }

    /**
     * Prepare website ids for curl.
     *
     * @param SalesRule $fixture
     * @return array
     */
    protected function prepareWebsiteIds(SalesRule $fixture)
    {
        $websites = $fixture->getDataFieldConfig('website_ids')['source']->getWebsites();
        $data = [];
        /** @var Website $website */
        foreach ($websites as $website) {
            $data[] = $website->getWebsiteId();
        }
        return $data;
    }

    /**
     * Prepare customer group data for curl.
     *
     * @param array $data
     * @return array
     */
    protected function prepareCustomerGroup(array $data)
    {
        $groupIds = [];
        if (!empty($data['customer_group_ids'])) {
            foreach ($data['customer_group_ids'] as $name) {
                $groupIds[] = isset($this->customerIds[$name]) ? $this->customerIds[$name] : $name;
            }
        }

        return $groupIds;
    }

    /**
     * Return saved sales rule id.
     *
     * @param string $response
     * @return int|null
     * @throws \Exception
     */
    protected function getSalesRuleId($response)
    {
        preg_match_all('~promo_quote/edit[^\s]*\/id\/(\d+)~', $response, $matches);
        if (empty($matches)) {
            throw new \Exception('Cannot find Sales Rule id');
        }

        return max(empty($matches[1]) ? null : $matches[1]);
    }
}
