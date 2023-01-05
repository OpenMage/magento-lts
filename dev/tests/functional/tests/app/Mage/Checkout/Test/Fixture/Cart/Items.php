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

namespace Mage\Checkout\Test\Fixture\Cart;

use Magento\Mtf\Fixture\DataSource;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\ObjectManager;

/**
 * Data for verify cart item block on checkout page.
 *
 * Data keys:
 *  - product (fixture data for verify)
 */
class Items extends DataSource
{
    /**
     * Item render.
     *
     * @var array
     */
    protected $itemRender = [
        'simple' => 'Mage\Catalog\Test\Fixture\Cart\Item',
        'configurable' => 'Mage\Catalog\Test\Fixture\ConfigurableProduct\Cart\Item',
        'downloadable' => 'Mage\Downloadable\Test\Fixture\Cart\Item',
        'virtual' => 'Mage\Catalog\Test\Fixture\Cart\Item',
        'grouped' => 'Mage\Catalog\Test\Fixture\GroupedProduct\Cart\Item',
        'bundle' => 'Mage\Bundle\Test\Fixture\Cart\Item'
    ];

    /**
     * List fixture products.
     *
     * @var FixtureInterface[]
     */
    protected $products;

    /**
     * @constructor
     * @param array $params
     * @param array $data
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        $this->products = isset($data['products']) ? $data['products'] : [];
        foreach ($this->products as $product) {
            $this->data[] = $this->getCartItemClass($product);
        }
    }

    /**
     * Get module name from fixture.
     *
     * @param FixtureInterface $product
     * @return string
     */
    protected function getCartItemClass(FixtureInterface $product)
    {
        $typeId = $product->getDataConfig()['type_id'];
        return ObjectManager::getInstance()->create($this->itemRender[$typeId], ['product' => $product]);
    }

    /**
     * Get source products.
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }
}
