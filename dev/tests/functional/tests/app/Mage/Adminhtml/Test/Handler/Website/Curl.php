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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Handler\Website;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Curl as AbstractCurl;
use Magento\Mtf\Config\DataInterface;
use Magento\Mtf\System\Event\EventManagerInterface;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mage\Adminhtml\Test\Fixture\Website;
use Magento\Mtf\Fixture\FixtureFactory;
use Mage\Adminhtml\Test\Fixture\StoreGroup;

/**
 * Curl handler for creating Website.
 */
class Curl extends AbstractCurl implements WebsiteInterface
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
     * POST request for creating Website.
     *
     * @param FixtureInterface|null $fixture [optional]
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        /** @var Website $fixture $data */
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . 'system_store/save';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write(CurlInterface::POST, $url, '1.1', [], $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'success-msg"')) {
            throw new \Exception("Website entity creating by curl handler was not successful! Response: $response");
        }
        $websiteId = $this->getWebSiteId($fixture);

        if ($fixture->hasData('default_group_id')) {
            $this->createStoreGroup($fixture, $websiteId);
        }

        return ['website_id' => $websiteId];
    }

    /**
     * Create store group.
     *
     * @param Website $website
     * @param int $websiteId
     * @return void
     */
    protected function createStoreGroup(Website $website, $websiteId)
    {
        $groupId = $website->getDefaultGroupId();
        $website = $this->prepareWebsite($website, $websiteId);
        $storeGroup = $this->fixtureFactory->createByCode(
            'storeGroup',
            ['dataSet' => $groupId['dataSet'], 'data' => ['website_id' => ['website' => $website]]]
        );
        $storeGroup->persist();
    }

    /**
     * Prepare website fixture.
     *
     * @param Website $website
     * @param int $websiteId
     * @return Website
     */
    protected function prepareWebsite(Website $website, $websiteId)
    {
        $websiteData = $website->getData();
        $websiteData['website_id'] = $websiteId;
        $website = $this->fixtureFactory->createByCode('website', ['data' => $websiteData]);

        return $website;
    }

    /**
     * Get website id.
     *
     * @param Website $website
     * @return int
     * @throws \Exception
     */
    protected function getWebSiteId(Website $website)
    {
        //Set pager limit to 2000 in order to find created website by name
        $url = $_ENV['app_backend_url'] . 'system_store/index/sort/group_title/dir/asc/limit/2000';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0');
        $response = $curl->read();

        $expectedUrl = '/admin/system_store/editWebsite/website_id/';
        preg_match('@.*' . $expectedUrl . '(\d+).*?' . $website->getName() . '@siu', $response, $matches);

        if (empty($matches)) {
            throw new \Exception('Cannot find website id.');
        }

        return intval($matches[1]);
    }

    /**
     * Prepare data from text to values.
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture)
    {
        $data = [
            'website' => $fixture->getData(),
            'store_action' => 'add',
            'store_type' => 'website',
        ];
        $data['website']['website_id'] = isset($data['website']['website_id']) ? $data['website']['website_id'] : '';
        $data['website']['is_default'] = isset($data['website']['is_default']) ? $data['website']['is_default'] : '';

        return $data;
    }
}
