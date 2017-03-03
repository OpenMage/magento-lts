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

namespace Mage\Adminhtml\Test\Handler\StoreGroup;

use Magento\Mtf\Config\DataInterface;
use Magento\Mtf\System\Event\EventManagerInterface;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Magento\Mtf\Handler\Curl as AbstractCurl;
use Mage\Adminhtml\Test\Fixture\StoreGroup;
use Mage\Adminhtml\Test\Fixture\Store;
use Magento\Mtf\Fixture\FixtureFactory;

/**
 * Curl handler for creating Store Group.
 */
class Curl extends AbstractCurl implements StoreGroupInterface
{
    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @constructor
     * @param DataInterface $configuration
     * @param EventManagerInterface $eventManager
     * @param FixtureFactory $fixtureFactory
     */
    public function __construct(
        DataInterface $configuration,
        EventManagerInterface $eventManager,
        FixtureFactory $fixtureFactory
    ) {
        parent::__construct($configuration, $eventManager);
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * POST request for creating store group.
     *
     * @param FixtureInterface $fixture
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        /** @var StoreGroup $fixture */
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . 'system_store/save/';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write($url, $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'success-msg')) {
            throw new \Exception("Store group entity creating by curl handler was not successful! Response: $response");
        }
        $groupId = $this->getStoreGroupIdByGroupName($fixture->getName());

        if ($fixture->hasData('default_store_id')) {
            $this->createStoreView($fixture, $groupId);
        }

        return ['group_id' => $groupId];
    }

    /**
     * Create store view.
     *
     * @param StoreGroup $storeGroup
     * @param int $groupId
     * @return void
     */
    protected function createStoreView(StoreGroup $storeGroup, $groupId)
    {
        $storeId = $storeGroup->getDefaultStoreId();
        $storeGroup = $this->prepareStoreGroup($storeGroup, $groupId);
        $store = $this->fixtureFactory->createByCode(
            'store',
            ['dataset' => $storeId['dataset'], 'data' => ['group_id' => ['store_group' => $storeGroup]]]
        );
        $store->persist();
    }

    /**
     * Prepare store group fixture.
     *
     * @param StoreGroup $storeGroup
     * @param int $groupId
     * @return StoreGroup
     */
    protected function prepareStoreGroup(StoreGroup $storeGroup, $groupId)
    {
        $category = $storeGroup->getDataFieldConfig('root_category_id')['source']->getCategory();
        $website = $storeGroup->getDataFieldConfig('website_id')['source']->getWebsite();
        $storeGroupData = array_replace_recursive(
            $storeGroup->getData(),
            [
                'root_category_id' => ['category' => $category],
                'website_id' => ['website' => $website],
                'group_id' => $groupId
            ]
        );
        $storeGroup = $this->fixtureFactory->createByCode('storeGroup', ['data' => $storeGroupData]);

        return $storeGroup;
    }

    /**
     * Get store id by store name.
     *
     * @param string $storeName
     * @return int
     * @throws \Exception
     */
    protected function getStoreGroupIdByGroupName($storeName)
    {
        //Set pager limit to 2000 in order to find created store group by name
        $url = $_ENV['app_backend_url'] . 'system_store/index/sort/group_title/dir/asc/limit/2000';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write($url);
        $response = $curl->read();
        preg_match('@.*group_id/(\d+).*' . $storeName . '@siu', $response, $matches);

        if (empty($matches)) {
            throw new \Exception('Cannot find store group id');
        }

        return intval($matches[1]);
    }

    /**
     * Prepare data from text to values.
     *
     * @param StoreGroup $fixture
     * @return array
     */
    protected function prepareData(StoreGroup $fixture)
    {
        $categoryId = $fixture->getDataFieldConfig('root_category_id')['source']->getCategory()->getId();
        $websiteId = $fixture->getDataFieldConfig('website_id')['source']->getWebsite()->getWebsiteId();
        $data = [
            'group' => [
                'name' => $fixture->getName(),
                'root_category_id' => $categoryId,
                'website_id' => $websiteId,
                'group_id' => $fixture->hasData('group_id') ? $fixture->getGroupId() : ''
            ],
            'store_action' => 'add',
            'store_type' => 'group'
        ];

        return $data;
    }
}
