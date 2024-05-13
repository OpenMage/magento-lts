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

namespace Mage\Page\Test\Block\Html;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Class top menu navigation block.
 */
class Topmenu extends Block
{
    /**
     * Link with category name.
     *
     * @var string
     */
    protected $category = '//a[text()="%s"]';

    /**
     * Select category from top menu by name and click on it.
     *
     * @param string $categoryName
     * @return void
     */
    public function selectCategory($categoryName)
    {
        $this->_rootElement->find(sprintf($this->category, $categoryName), Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Check is visible category in top menu by name.
     *
     * @param string $categoryName
     * @return bool
     */
    public function isCategoryVisible($categoryName)
    {
        return $this->_rootElement->find(sprintf($this->category, $categoryName), Locator::SELECTOR_XPATH)->isVisible();
    }
}
