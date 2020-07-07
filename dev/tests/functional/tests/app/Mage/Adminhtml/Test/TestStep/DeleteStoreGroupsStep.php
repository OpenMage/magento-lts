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

namespace Mage\Adminhtml\Test\TestStep;

use Mage\Adminhtml\Test\Fixture\StoreGroup;
use Mage\Adminhtml\Test\Page\Adminhtml\DeleteStore;
use Mage\Adminhtml\Test\Page\Adminhtml\EditStore;
use Magento\Mtf\TestStep\TestStepInterface;
use Mage\Adminhtml\Test\Page\Adminhtml\StoreIndex;

/**
 * Delete specified store groups.
 */
class DeleteStoreGroupsStep implements TestStepInterface
{
    /**
     * StoreGroups to delete.
     *
     * @var array
     */
    protected $storeGroups;

    /**
     * Page store index.
     *
     * @var StoreIndex
     */
    protected $storeIndex;

    /**
     * Edit store group page.
     *
     * @var EditStore
     */
    protected $editStore;

    /**
     * Delete store group page.
     *
     * @var DeleteStore
     */
    protected $deleteStore;

    /**
     * @constructor
     * @param StoreIndex $storeIndex
     * @param EditStore $editStore
     * @param DeleteStore $deleteStore
     * @param array $storeGroups
     */
    public function __construct(
        StoreIndex $storeIndex,
        EditStore $editStore,
        DeleteStore $deleteStore,
        array $storeGroups
    ) {
        $this->storeIndex = $storeIndex;
        $this->editStore = $editStore;
        $this->deleteStore = $deleteStore;
        $this->storeGroups = $storeGroups;
    }

    /**
     * Delete store groups.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->storeGroups as $storeGroup) {
            /** @var StoreGroup $storeGroup */
            $this->storeIndex->getStoreGrid()->openStoreGroupByName($storeGroup->getName());
            $this->editStore->getFormPageActions()->delete();
            $deleteStoreFormPageActions = $this->deleteStore->getFormPageActions();
            if ($deleteStoreFormPageActions->isVisible()) {
                $this->deleteStore->getForm()->fillForm();
                $deleteStoreFormPageActions->delete();
            }
        }
    }
}
