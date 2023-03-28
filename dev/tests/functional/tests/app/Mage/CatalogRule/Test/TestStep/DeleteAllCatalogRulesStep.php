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

namespace Mage\CatalogRule\Test\TestStep;

use Mage\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;
use Mage\CatalogRule\Test\Page\Adminhtml\CatalogRuleEdit;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Delete all Catalog Rules on backend.
 */
class DeleteAllCatalogRulesStep implements TestStepInterface
{
    /**
     * Catalog rule index page.
     *
     * @var CatalogRuleIndex
     */
    protected $catalogRuleIndex;

    /**
     * Catalog rule new and edit page.
     *
     * @var CatalogRuleEdit
     */
    protected $catalogRuleEdit;

    /**
     * @constructor
     * @param CatalogRuleIndex $catalogRuleIndex
     * @param CatalogRuleEdit $catalogRuleEdit
     */
    public function __construct(CatalogRuleIndex $catalogRuleIndex, CatalogRuleEdit $catalogRuleEdit)
    {
        $this->catalogRuleIndex = $catalogRuleIndex;
        $this->catalogRuleEdit = $catalogRuleEdit;
    }

    /**
     * Delete Catalog Rule on backend.
     *
     * @return array
     */
    public function run()
    {
        $this->catalogRuleIndex->open();
        $this->catalogRuleIndex->getCatalogRuleGrid()->resetFilter();
        while ($this->catalogRuleIndex->getCatalogRuleGrid()->isFirstRowVisible()) {
            $this->catalogRuleIndex->getCatalogRuleGrid()->openFirstRow();
            $this->catalogRuleEdit->getFormPageActions()->delete();
        }
    }
}
