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

namespace Mage\Adminhtml\Test\Constraint;

use Mage\Adminhtml\Test\Page\Adminhtml\EditGroup;
use Mage\Adminhtml\Test\Page\Adminhtml\StoreIndex;
use Mage\Adminhtml\Test\Fixture\StoreGroup;
use Magento\Mtf\Constraint\AbstractAssertForm;

/**
 * Assert that displayed Store Group data on edit page equals passed from fixture.
 */
class AssertStoreGroupForm extends AbstractAssertForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Skipped fields for verify data.
     *
     * @var array
     */
    protected $skippedFields = ['group_id'];

    /**
     * Assert that displayed Store Group data on edit page equals passed from fixture.
     *
     * @param StoreIndex $storeIndex
     * @param EditGroup $editGroup
     * @param StoreGroup $storeGroup
     * @return void
     */
    public function processAssert(StoreIndex $storeIndex, EditGroup $editGroup, StoreGroup $storeGroup)
    {
        $storeIndex->open();
        $storeIndex->getStoreGrid()->openStoreGroup($storeGroup->getName());
        $formData = $editGroup->getEditFormStoreGroup()->getData();
        $fixtureData = $storeGroup->getData();

        $errors = $this->verifyData($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Store Group data on edit page equals data from fixture.';
    }
}
