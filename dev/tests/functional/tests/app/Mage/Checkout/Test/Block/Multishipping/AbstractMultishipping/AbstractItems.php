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
