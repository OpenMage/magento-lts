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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Fixture\CatalogCategory;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Fixture\FixtureFactory;
use Mage\Catalog\Test\Fixture\CatalogCategory;

/**
 * Prepare products for category.
 */
class CategoryProducts implements FixtureInterface
{
    /**
     * Prepared dataSet data.
     *
     * @var array|null
     */
    protected $data;

    /**
     * Array products fixtures.
     *
     * @var array
     */
    protected $products = [];

    /**
     * Fixture params.
     *
     * @var array
     */
    protected $params;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, $data = [])
    {
        $this->params = $params;
        if (isset($data['data']) && isset($data['products'])) {
            $this->data = $data['data'];
            $this->products = $data['products'];
        } elseif (isset($data['dataSet'])) {
            $products = explode(',', $data['dataSet']);
            foreach ($products as $value) {
                $explodeValue = explode('::', $value);
                $product = $fixtureFactory->createByCode($explodeValue[0], ['dataSet' => $explodeValue[1]]);
                if (!$product->getId()) {
                    $product->persist();
                }
                $this->data[] = $product->getSku();
                $this->products[] = $product;
            }
        }
    }

    /**
     * Persist attribute options.
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set.
     *
     * @param string|null $key [optional]
     * @return array|null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings.
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Return products.
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }
}
