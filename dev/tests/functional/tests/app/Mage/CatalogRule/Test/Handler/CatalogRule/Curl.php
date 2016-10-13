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

namespace Mage\CatalogRule\Test\Handler\CatalogRule;

use Mage\Adminhtml\Test\Handler\Conditions;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Curl that creates catalog price rule.
 */
class Curl extends Conditions implements CatalogRuleInterface
{
    /**
     * Map of type parameter.
     *
     * @var array
     */
    protected $mapTypeParams = [
        'Conditions combination' => [
            'type' => 'catalogrule/rule_condition_combine',
            'aggregator' => 'all',
            'value' => 1,
        ],
        'Category' => [
            'type' => 'catalogrule/rule_condition_product',
            'attribute' => 'category_ids',
        ],
    ];

    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'simple_action' => [
            'By Percentage of the Original Price' => 'by_percent',
            'By Fixed Amount' => 'by_fixed',
            'To Percentage of the Original Price' => 'to_percent',
            'To Fixed Amount' => 'to_fixed',
        ],
        'is_active' => [
            'Active' => 1,
            'Inactive' => 0,
        ],
        'stop_rules_processing' => [
            'Yes' => 1,
            'No' => 0,
        ],
    ];

    /**
     * Mapping values for Websites.
     *
     * @var array
     */
    protected $websiteIds = [
        'Main Website' => 1,
    ];

    /**
     * Mapping values for Customer Groups.
     *
     * @var array
     */
    protected $customerGroupIds = [
        'NOT LOGGED IN' => 0,
        'General' => 1,
        'Wholesale' => 2,
        'Retailer' => 3,
    ];

    /**
     * POST request for creating Catalog Price Rule.
     *
     * @param FixtureInterface $fixture
     * @return mixed|void
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . 'promo_catalog/save/';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write($url, $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'id="messages"')) {
            throw new \Exception(
                "Catalog Price Rule entity creating by curl handler was not successful! Response: $response"
            );
        }

        return ['id' => $this->getCategoryPriceRuleId()];
    }

    /**
     * Prepare data from text to values.
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData($fixture)
    {
        $data = $this->replaceMappingData($fixture->getData());
        if (isset($data['website_ids'])) {
            $websiteIds = [];
            foreach ($data['website_ids'] as $websiteId) {
                $websiteIds[] = isset($this->websiteIds[$websiteId]) ? $this->websiteIds[$websiteId] : $websiteId;
            }
            $data['website_ids'] = $websiteIds;
        }
        if (isset($data['customer_group_ids'])) {
            $customerGroupIds = [];
            foreach ($data['customer_group_ids'] as $customerGroupId) {
                $customerGroupIds[] = isset($this->customerGroupIds[$customerGroupId])
                    ? $this->customerGroupIds[$customerGroupId]
                    : $customerGroupId;
            }
            $data['customer_group_ids'] = $customerGroupIds;
        }
        if (!isset($data['stop_rules_processing'])) {
            $data['stop_rules_processing'] = 0;
        }

        if (!isset($data['rule'])) {
            $data['rule'] = null;
        }
        $data['rule'] = ['conditions' => $this->prepareCondition($data['rule'])];

        return $data;
    }

    /**
     * Get id after creating Category Price Rule.
     *
     * @return int
     * @throws \Exception
     */
    public function getCategoryPriceRuleId()
    {
        // Sort data in grid to define category price rule id if more than 20 items in grid
        $url = $_ENV['app_backend_url'] . 'promo_catalog/index/sort/rule_id/dir/desc';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write($url);
        $response = $curl->read();
        $curl->close();

        preg_match('~title="http[^\s]*\/id\/(\d+)~', $response, $matches);
        if (empty($matches)) {
            throw new \Exception('Cannot find Category Price Rule id');
        }

        return $matches[1];
    }
}
