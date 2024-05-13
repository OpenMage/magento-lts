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

namespace Mage\Adminhtml\Test\Block\Sales\Order\Create\CustomerActivities;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Sidebar block.
 */
abstract class Sidebar extends Block
{
    /**
     * 'Add to order' checkbox.
     *
     * @var string
     */
    protected $addToOrder = '//tr[td[.="%s"]]//input[contains(@name,"add")]';

    /**
     * Add product to order.
     *
     * @param array $products
     * @return void
     */
    public function addToOrder(array $products)
    {
        foreach ($products as $product) {
            $this->_rootElement
                ->find(sprintf($this->addToOrder, $product->getName()), Locator::SELECTOR_XPATH, 'checkbox')
                ->setValue('Yes');
        }
    }
}
