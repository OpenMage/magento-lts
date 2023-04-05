<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
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
