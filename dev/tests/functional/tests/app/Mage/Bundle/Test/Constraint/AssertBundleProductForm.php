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

namespace Mage\Bundle\Test\Constraint;

use Mage\Catalog\Test\Constraint\AssertProductForm;

/**
 * Assert that displayed product data on edit page equals passed from fixture.
 */
class AssertBundleProductForm extends AssertProductForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Sort fields for fixture and form data.
     *
     * @var array
     */
    protected $sortFields = [
        'custom_options::title',
        'cross_sell_products::entity_id',
        'up_sell_products::entity_id',
        'related_products::entity_id',
        'bundle_selections::title'
    ];
}
