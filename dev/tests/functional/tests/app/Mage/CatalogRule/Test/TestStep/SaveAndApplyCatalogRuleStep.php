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
 * Save and apply catalog rule on backend.
 */
class SaveAndApplyCatalogRuleStep implements TestStepInterface
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
     * Catalog rule id.
     *
     * @var string
     */
    protected $catalogRuleId;

    /**
     * @constructor
     * @param CatalogRuleIndex $catalogRuleIndex
     * @param CatalogRuleEdit $catalogRuleEdit
     * @param string $catalogRuleId
     */
    public function __construct(
        CatalogRuleIndex $catalogRuleIndex,
        CatalogRuleEdit $catalogRuleEdit,
        $catalogRuleId
    ) {
        $this->catalogRuleIndex = $catalogRuleIndex;
        $this->catalogRuleEdit = $catalogRuleEdit;
        $this->catalogRuleId = $catalogRuleId;
    }

    /**
     * Save and apply catalog rule on backend.
     *
     * @return void
     */
    public function run()
    {
        $this->catalogRuleIndex->open();
        $this->catalogRuleIndex->getCatalogRuleGrid()->searchAndOpen(['rule_id' => $this->catalogRuleId]);
        $this->catalogRuleEdit->getFormPageActions()->saveAndApply();
    }
}
