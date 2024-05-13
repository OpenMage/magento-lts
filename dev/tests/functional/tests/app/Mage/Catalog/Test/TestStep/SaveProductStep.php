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

namespace Mage\Catalog\Test\TestStep;

use Mage\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Save product step.
 */
class SaveProductStep implements TestStepInterface
{
    /**
     * Catalog product edit page.
     *
     * @var CatalogProductEdit
     */
    protected $catalogProductEdit;

    /**
     * @constructor
     * @param CatalogProductEdit $catalogProductEdit
     */
    public function __construct(CatalogProductEdit $catalogProductEdit)
    {
        $this->catalogProductEdit = $catalogProductEdit;
    }

    /**
     * Save product.
     *
     * @return void
     */
    public function run()
    {
        $this->catalogProductEdit->getFormPageActions()->save();
    }
}
