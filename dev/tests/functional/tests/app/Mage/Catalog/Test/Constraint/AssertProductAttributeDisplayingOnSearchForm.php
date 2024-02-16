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

use Mage\Catalog\Test\Fixture\CatalogProductAttribute;
use Mage\CatalogSearch\Test\Page\CatalogsearchAdvanced;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Check whether attribute is displayed in the advanced search form on the frontend.
 */
class AssertProductAttributeDisplayingOnSearchForm extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Check whether attribute is displayed in the advanced search form on the frontend.
     *
     * @param CatalogProductAttribute $attribute
     * @param CatalogsearchAdvanced $advancedSearch
     * @return void
     */
    public function processAssert(CatalogProductAttribute $attribute, CatalogsearchAdvanced $advancedSearch)
    {
        $advancedSearch->open();
        $formLabels = $advancedSearch->getForm()->getFormlabels();
        $label = $attribute->hasData('manage_frontend_label')
            ? $attribute->getManageFrontendLabel()
            : $attribute->getFrontendLabel();
        \PHPUnit_Framework_Assert::assertTrue(
            in_array($label, $formLabels),
            'Attribute is absent on advanced search form.'
        );
    }

    /**
     * Returns string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Attribute is present on advanced search form.';
    }
}
