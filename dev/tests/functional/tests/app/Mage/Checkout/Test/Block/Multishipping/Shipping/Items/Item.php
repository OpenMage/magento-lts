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

namespace Mage\Checkout\Test\Block\Multishipping\Shipping\Items;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Item block on checkout with multishipping shipping page.
 */
class Item extends Block
{
    /**
     * Shipping method label selector.
     *
     * @var string
     */
    protected $shippingMethodLabel = '//label[contains(., "%s")]';

    /**
     * Shipping method input selector.
     *
     * @var string
     */
    protected $shippingMethodInput = '//dt[contains(.,"%s")]/following-sibling::dd[1]//li[contains(., "%s")]//input';

    /**
     * Fill item shipping method.
     *
     * @param array $method
     * @throws \Exception
     * @return void
     */
    public function fillShippingMethod(array $method)
    {
        $shippingInput = $this->_rootElement->find(
            sprintf($this->shippingMethodInput, $method['shipping_service'], $method['shipping_method']),
            Locator::SELECTOR_XPATH
        );
        if ($shippingInput->isVisible()) {
            $shippingInput->click();
        } else {
            $shippingLabel = $this->_rootElement->find(
                sprintf($this->shippingMethodLabel, $method['shipping_method']),
                Locator::SELECTOR_XPATH
            );
            if (!$shippingLabel->isVisible()) {
                throw new \Exception("{$method['shipping_service']} shipping doesn't exist.");
            }
        }
    }
}
