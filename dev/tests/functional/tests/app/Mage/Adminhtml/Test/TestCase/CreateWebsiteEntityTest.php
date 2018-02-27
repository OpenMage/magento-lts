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

namespace Mage\Adminhtml\Test\TestCase;

use Mage\Adminhtml\Test\Fixture\StoreGroup;
use Mage\Adminhtml\Test\Fixture\Website;
use Mage\Adminhtml\Test\Page\Adminhtml\NewWebsite;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\TestCase\Injectable;
use Mage\Adminhtml\Test\Fixture\Store;
use Mage\Adminhtml\Test\Page\Adminhtml\StoreIndex;
use Mage\Adminhtml\Test\Page\Adminhtml\EditStore;
use Mage\Adminhtml\Test\Page\Adminhtml\DeleteStore;

/**
 * Steps:
 * 1. Open Backend.
 * 2. Go to System -> Manage Stores.
 * 3. Click "Create Website" button.
 * 4. Fill data according to dataset.
 * 5. Create Store with created Website.
 * 6. Create StoreView with created Store.
 * 7. Create folder with appropriate files for created Website.
 * 8. Configure configuration settings for created Website.
 * 9. Perform all assertions
 *
 * @group Store_Management_(MX)
 * @ZephyrId MPERF-7232
 */
class CreateWebsiteEntityTest extends Injectable
{
    /**
     * Page StoreIndex.
     *
     * @var StoreIndex
     */
    protected $storeIndex;

    /**
     * Website Fixture.
     *
     * @var Website
     */
    protected $website;

    /**
     * Page EditStore.
     *
     * @var EditStore
     */
    protected $editStore;

    /**
     * Page DeleteStore.
     *
     * @var DeleteStore
     */
    protected $deleteStore;

    /**
     * Page NewWebsite.
     *
     * @var NewWebsite
     */
    protected $newWebsite;

    /**
     * Fixture Factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Prepare fixture factory for test.
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Preparing pages for test.
     *
     * @param StoreIndex $storeIndex
     * @param EditStore $editStore
     * @param DeleteStore $deleteStore
     * @param NewWebsite $newWebsite
     * @return void
     */
    public function __inject(
        StoreIndex $storeIndex,
        EditStore $editStore,
        DeleteStore $deleteStore,
        NewWebsite $newWebsite
    ) {
        $this->storeIndex = $storeIndex;
        $this->editStore = $editStore;
        $this->deleteStore = $deleteStore;
        $this->newWebsite = $newWebsite;
    }

    /**
     * Run Create Website Entity test.
     *
     * @param Website $website
     * @param StoreGroup $store
     * @param Store $storeView
     * @return array
     */
    public function test(Website $website, StoreGroup $store, Store $storeView)
    {
        // Steps
        $this->storeIndex->open();
        $this->website = $this->createWebsite($website);

        // Persisting Store and StoreView with created website
        $store = $this->persistStore($store);
        $storeView = $this->persistStoreView($storeView, $store);

        return ['storeView' => $storeView];
    }

    /**
     * Create website from admin panel.
     *
     * @param Website $website
     * @return Website
     */
    protected function createWebsite(Website $website)
    {
        $this->storeIndex->getGridPageActions()->createWebsite();
        $this->newWebsite->getWebsiteForm()->fill($website);
        $this->newWebsite->getFormPageActions()->save();

        return $this->prepareWebsite($website);
    }

    /**
     * Persist store with created website.
     *
     * @param StoreGroup $store
     * @return StoreGroup
     */
    protected function persistStore(StoreGroup $store)
    {
        $category = $store->getDataFieldConfig('root_category_id')['source']->getCategory();
        $data = ['website_id' => ['website' => $this->website], 'root_category_id' => ['category' => $category]];
        $store = $this->fixtureFactory->createByCode('storeGroup', ['data' => array_merge($store->getData(), $data)]);
        $store->persist();

        return $store;
    }

    /**
     * Persist store view with created store.
     *
     * @param Store $storeView
     * @param StoreGroup $store
     * @return Store
     */
    protected function persistStoreView(Store $storeView, StoreGroup $store)
    {
        $data = ['data' => array_merge($storeView->getData(), ['group_id' => ['store_group' => $store]])];
        $storeView = $this->fixtureFactory->createByCode('store', $data);
        $storeView->persist();

        return $storeView;
    }

    /**
     * Prepare website for test.
     *
     * @param Website $website
     * @return Website
     */
    protected function prepareWebsite(Website $website)
    {
        $id = preg_replace("@.*/(\d+)/@", "$1", $this->storeIndex->getStoreGrid()->getLinkUrl($website->getName()));
        $data = array_merge($website->getData(), ['website_id' => $id]);

        return $this->fixtureFactory->createByCode('website', ['data' => $data]);
    }

    /**
     * Delete Website after test variation.
     *
     * @return void
     */
    public function tearDown()
    {
        // Delete Website
        if ($this->website->hasData('website_id')) {
            $this->storeIndex->open();
            $storeGrid = $this->storeIndex->getStoreGrid();
            $storeGrid->openWebsite($this->website->getName());
            $this->editStore->getFormPageActions()->delete();
            $this->deleteStore->getFormPageActions()->delete();
        }
    }
}
