<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\Block\Multishipping\AbstractMultishipping;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\ElementInterface;
use Mage\Checkout\Test\Block\Multishipping\Addresses\Items\Item as AddressItem;
use Mage\Checkout\Test\Block\Multishipping\Shipping\Items\Item as ShippingItem;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Abstract Items block on checkout with multishipping page.
 */
abstract class AbstractItems extends Block
{
    /**
     * Get element for item block.
     *
     * @param InjectableFixture $entity
     * @param int $itemIndex
     * @return ElementInterface
     */
    protected abstract function getItemBlockElement(InjectableFixture $entity, $itemIndex);

    /**
     * Get path for items class.
     *
     * @return string
     */
    protected abstract function getItemBlockClass();

    /**
     * Get item block.
     *
     * @param InjectableFixture $entity
     * @param int $itemIndex
     * @return AddressItem|ShippingItem
     */
    public function getItemBlock(InjectableFixture $entity, $itemIndex)
    {
        return $this->blockFactory->create(
            $this->getItemBlockClass(),
            ['element' => $this->getItemBlockElement($entity, $itemIndex)]
        );
    }
}
