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

namespace Mage\Cms\Test\TestCase;

use Mage\Cms\Test\Fixture\CmsPage;
use Magento\Mtf\TestCase\Injectable;
use Mage\Cms\Test\Page\Adminhtml\CmsPageIndex;
use Mage\Cms\Test\Page\Adminhtml\CmsPageNew;
use Mage\Adminhtml\Test\Page\Adminhtml\StoreIndex;
use Mage\Adminhtml\Test\Page\Adminhtml\EditStore;
use Mage\Adminhtml\Test\Page\Adminhtml\DeleteStore;

/**
 * Steps:
 * 1. Log in to Backend.
 * 2. Navigate to CMS > Pages > Manage Content.
 * 3. Click "Add New Page" button.
 * 4. Fill data according to data set.
 * 5. Click "Save Page" button.
 * 6. Perform all assertions.
 *
 * @group CMS Content (PS)
 * @ZephyrId MPERF-6686
 */
class CreateCmsPageEntityTest extends Injectable
{
    /**
     * CmsIndex page.
     *
     * @var CmsPageIndex
     */
    protected $cmsPageIndex;

    /**
     * CmsNew page.
     *
     * @var CmsPageNew
     */
    protected $cmsPageNew;

    /**
     * Page StoreIndex.
     *
     * @var StoreIndex
     */
    protected $storeIndex;

    /**
     * Cms page fixture.
     *
     * @var CmsPage
     */
    protected $cms;

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
     * Inject data.
     *
     * @param CmsPageIndex $cmsPageIndex
     * @param CmsPageNew $cmsPageNew
     * @param StoreIndex $storeIndex
     * @param EditStore $editStore
     * @param DeleteStore $deleteStore
     * @return void
     */
    public function __inject(
        CmsPageIndex $cmsPageIndex,
        CmsPageNew $cmsPageNew,
        StoreIndex $storeIndex,
        EditStore $editStore,
        DeleteStore $deleteStore
    ) {
        $this->cmsPageIndex = $cmsPageIndex;
        $this->cmsPageNew = $cmsPageNew;
        $this->storeIndex = $storeIndex;
        $this->editStore = $editStore;
        $this->deleteStore = $deleteStore;
    }

    /**
     * Creating Cms page.
     *
     * @param CmsPage $cms
     * return void
     */
    public function test(CmsPage $cms)
    {
        $this->cms = $cms;

        // Steps
        $this->cmsPageIndex->open();
        $this->cmsPageIndex->getPageActionsBlock()->addNew();
        $this->cmsPageNew->getPageForm()->fill($cms);
        $this->cmsPageNew->getPageMainActions()->save();
    }

    /**
     * Delete store.
     *
     * @return void
     */
    public function tearDown()
    {
        if (!$this->cms->hasData('store_id')) {
            return;
        }
        $stores = $this->cms->getStoreId();
        if ($stores) {
            $stores = $this->cms->getDataFieldConfig('store_id')['source']->getStore();
            foreach ($stores as $store) {
                $this->storeIndex->open();
                $this->storeIndex->getStoreGrid()->openStore($store);
                $this->editStore->getFormPageActions()->delete();
                $this->deleteStore->getFormPageActions()->delete();
            }
        }
    }
}
