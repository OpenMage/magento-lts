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

namespace Mage\Adminhtml\Test\TestCase;

use Magento\Mtf\TestCase\Injectable;
use Mage\Core\Test\Fixture\ConfigData;
use Mage\Adminhtml\Test\Fixture\Store;
use Mage\Adminhtml\Test\Page\Adminhtml\StoreIndex;
use Mage\Adminhtml\Test\Page\Adminhtml\StoreNew;
use Mage\Adminhtml\Test\Page\Adminhtml\EditStore;
use Mage\Adminhtml\Test\Page\Adminhtml\DeleteStore;
use Mage\Adminhtml\Test\Page\Adminhtml\EditGroup;
use Mage\Adminhtml\Test\Page\Adminhtml\DeleteGroup;
use Mage\Adminhtml\Test\Page\AdminLogout;

/**
 * Test Creation for CreateStoreEntity (Store Management)
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to System -> Manage Stores
 * 3. Click "Create Store View" button
 * 4. Fill data according to dataset
 * 5. Perform all assertions
 *
 * @group Store_Management_(MX)
 * @ZephyrId MPERF-6650
 */
class CreateStoreEntityTest extends Injectable
{
    /**
     * Page StoreIndex.
     *
     * @var StoreIndex
     */
    protected $storeIndex;

    /**
     * Page StoreNew.
     *
     * @var StoreNew
     */
    protected $storeNew;

    /**
     * Current Store View.
     *
     * @var Store
     */
    protected $store;

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
     * Page EditGroup.
     *
     * @var EditGroup
     */
    protected $editGroup;

    /**
     * Page DeleteGroup.
     *
     * @var DeleteGroup
     */
    protected $deleteGroup;

    /**
     * Admin logout page.
     *
     * @var AdminLogout
     */
    protected $adminLogout;

    /**
     * Preparing pages for test.
     *
     * @param StoreIndex $storeIndex
     * @param StoreNew $storeNew
     * @param EditStore $editStore
     * @param DeleteStore $deleteStore
     * @param EditGroup $editGroup
     * @param DeleteGroup $deleteGroup
     * @param AdminLogout $adminLogout
     * @return void
     */
    public function __inject(
        StoreIndex $storeIndex,
        StoreNew $storeNew,
        EditStore $editStore,
        DeleteStore $deleteStore,
        EditGroup $editGroup,
        DeleteGroup $deleteGroup,
        AdminLogout $adminLogout
    ) {
        $this->storeIndex = $storeIndex;
        $this->storeNew = $storeNew;
        $this->editStore = $editStore;
        $this->deleteStore = $deleteStore;
        $this->editGroup = $editGroup;
        $this->deleteGroup = $deleteGroup;
        $this->adminLogout = $adminLogout;
    }

    /**
     * Run CreateStoreEntity test.
     *
     * @param Store $store
     * @param ConfigData $config
     * @return void
     */
    public function test(Store $store, ConfigData $config)
    {
        // Preconditions
        $this->store = $store;
        $config->persist();
        $this->adminLogout->open();

        // Steps
        $this->storeIndex->open();
        $this->storeIndex->getGridPageActions()->addStoreView();
        $this->storeNew->getStoreForm()->fill($store);
        $this->storeNew->getFormPageActions()->save();
    }

    /**
     * Delete store.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->objectManager->create(
            'Mage\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'store_view_local', 'rollback' => true]
        )->run();
        $this->storeIndex->open();
        if ($this->store->getGroupId() === 'Main Website/Main Website Store') {
            $this->storeIndex->getStoreGrid()->openStore($this->store);
            $this->editStore->getFormPageActions()->delete();
            $this->deleteStore->getFormPageActions()->delete();
        } else {
            $this->storeIndex->getStoreGrid()->openStoreGroup(explode('/', $this->store->getGroupId())[1]);
            $this->editGroup->getFormPageActions()->delete();
            $this->deleteGroup->getFormPageActions()->delete();
        }
    }
}
