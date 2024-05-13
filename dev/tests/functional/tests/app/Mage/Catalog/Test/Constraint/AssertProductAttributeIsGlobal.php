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

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Fixture\CatalogProductAttribute;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductAttributeIndex;

/**
 * Assert that product attribute is global.
 */
class AssertProductAttributeIsGlobal extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that product attribute is global.
     *
     * @param CatalogProductAttribute $attribute
     * @param CatalogProductAttributeIndex $attributeIndexPage
     * @return void
     */
    public function processAssert(CatalogProductAttribute $attribute, CatalogProductAttributeIndex $attributeIndexPage)
    {
        $attributeIndexPage->open();
        $code = $attribute->getAttributeCode();
        $filter = ['attribute_code' => $code, 'is_global' => $attribute->getIsGlobal()];
        \PHPUnit_Framework_Assert::assertTrue(
            $attributeIndexPage->getGrid()->isRowVisible($filter),
            "Attribute with attribute code '$code' isn't global."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Attribute is global.';
    }
}
