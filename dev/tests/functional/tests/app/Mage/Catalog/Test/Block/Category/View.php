<?php
/**
 * OpenMage
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

namespace Mage\Catalog\Test\Block\Category;

use Magento\Mtf\Block\Block;

/**
 * Category view block on the category page.
 */
class View extends Block
{
    /**
     * Description CSS selector.
     *
     * @var string
     */
    protected $description = '.category-description';

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_rootElement->find($this->description)->getText();
    }

    /**
     * Get view block text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->_rootElement->getText();
    }
}
