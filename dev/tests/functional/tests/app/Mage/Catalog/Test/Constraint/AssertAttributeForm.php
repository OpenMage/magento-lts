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

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Page\Adminhtml\CatalogProductAttributeEdit;
use Magento\Mtf\Constraint\AbstractAssertForm;
use Mage\Catalog\Test\Fixture\CatalogProductAttribute;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductAttributeIndex;

/**
 * Assert that displayed attribute data on edit page equals passed from fixture.
 */
class AssertAttributeForm extends AbstractAssertForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Skipped fields for verify data.
     *
     * @var array
     */
    protected $skippedFields = ['attribute_id'];

    /**
     * Assert that displayed attribute data on edit page equals passed from fixture.
     *
     * @param CatalogProductAttributeIndex $catalogProductAttributeIndex
     * @param CatalogProductAttributeEdit $catalogProductAttributeEdit
     * @param CatalogProductAttribute $attribute
     * @return void
     */
    public function processAssert(
        CatalogProductAttributeIndex $catalogProductAttributeIndex,
        CatalogProductAttributeEdit $catalogProductAttributeEdit,
        CatalogProductAttribute $attribute
    ) {
        $filter = ['attribute_code' => $attribute->getAttributeCode()];
        $catalogProductAttributeIndex->open()->getGrid()->searchAndOpen($filter);

        $dataFixture = $attribute->getData();
        $dataForm = $catalogProductAttributeEdit->getAttributeForm()->getData($attribute);
        $errors = $this->verifyData($dataFixture, $dataForm);
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Returns string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed attribute data on edit page equals passed from fixture.';
    }
}
