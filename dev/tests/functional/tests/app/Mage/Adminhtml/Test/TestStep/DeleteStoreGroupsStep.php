<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
