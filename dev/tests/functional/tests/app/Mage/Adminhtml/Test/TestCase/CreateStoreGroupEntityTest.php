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

namespace Mage\Adminhtml\Test\TestCase;

use Mage\Adminhtml\Test\Page\Adminhtml\NewStoreGroup;
use Mage\Adminhtml\Test\Page\Adminhtml\StoreIndex;
use Mage\Adminhtml\Test\Fixture\StoreGroup;
use Magento\Mtf\TestCase\Injectable;
use Mage\Adminhtml\Test\Page\Adminhtml\EditGroup;
use Mage\Adminhtml\Test\Page\Adminhtml\DeleteGroup;
use Mage\Adminhtml\Test\Page\Adminhtml\EditStore;
use Mage\Adminhtml\Test\Page\Adminhtml\DeleteStore;

/**
 * Steps:
 * 1. Log in to backend.
 * 2. Go to System -> Manage Stores.
 * 3. Click "Create Store" button.
 * 4. Fill data according to dataset.
 * 5. Click "Save Store" button.
 * 6. Perform all assertions.
 *
 * @group Store_Management_(PS)
 * @ZephyrId MPERF-6816
 */
class CreateStoreGroupEntityTest extends Injectable
{
    /**
     * Page StoreIndex.
     *
     * @var StoreIndex
     */
    protected $storeIndex;

    /**
     * NewGroupIndex page.
     *
     * @var NewStoreGroup
     */
    protected $newStoreGroup;

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
     * Store group fixture.
     *
     * @var StoreGroup
     */
    protected $storeGroup;

    /**
     * Injection pages.
     *
     * @param StoreIndex $storeIndex
     * @param NewStoreGroup $newStoreGroup
     * @param EditStore $editStore
     * @param DeleteStore $deleteStore
     * @param EditGroup $editGroup
     * @param DeleteGroup $deleteGroup
     * @return void
     */
    public function __inject(
        StoreIndex $storeIndex,
        NewStoreGroup $newStoreGroup,
        EditGroup $editGroup,
        DeleteGroup $deleteGroup,
        EditStore $editStore,
        DeleteStore $deleteStore
    )
    {
        $this->storeIndex = $storeIndex;
        $this->newStoreGroup = $newStoreGroup;
        $this->editGroup = $editGroup;
        $this->deleteGroup = $deleteGroup;
        $this->editStore = $editStore;
        $this->deleteStore = $deleteStore;
    }

    /**
     * Create new StoreGroup.
     *
     * @param StoreGroup $storeGroup
     * @return void
     */
    public function test(StoreGroup $storeGroup)
    {
        $this->storeGroup = $storeGroup;
        //Steps
        $this->storeIndex->open();
        $this->storeIndex->getGridPageActions()->createStoreGroup();
        $this->newStoreGroup->getEditFormStoreGroup()->fill($storeGroup);
        $this->newStoreGroup->getFormPageActions()->save();
    }

    /**
     * Delete store.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->storeIndex->open();
        if ($this->storeGroup->getWebsiteId() !== 'Main Website') {
            $this->storeIndex->getStoreGrid()->openWebsite($this->storeGroup->getWebsiteId());
            $this->editStore->getFormPageActions()->delete();
            $deleteStoreFormPageActions = $this->deleteStore->getFormPageActions();
            if ($deleteStoreFormPageActions->isVisible()) {
                $this->deleteStore->getForm()->fillForm();
                $deleteStoreFormPageActions->delete();
            }
        } else {
            $this->storeIndex->getStoreGrid()->openStoreGroupByName($this->storeGroup->getName());
            $this->editStore->getFormPageActions()->delete();
            $deleteStoreFormPageActions = $this->deleteStore->getFormPageActions();
            if ($deleteStoreFormPageActions->isVisible()) {
                $this->deleteStore->getForm()->fillForm();
                $deleteStoreFormPageActions->delete();
            }
        }
    }
}
