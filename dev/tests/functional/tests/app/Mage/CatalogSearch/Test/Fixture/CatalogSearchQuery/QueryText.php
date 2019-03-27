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

namespace Mage\CatalogSearch\Test\Fixture\CatalogSearchQuery;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Data to search for.
 * Possible templates:
 * - {value}
 * - {product}::{product_property_to_search}
 * - {product}::{product_dataset}::{product_property_to_search}
 */
class QueryText implements FixtureInterface
{
    /**
     * Entity to search.
     *
     * @var InjectableFixture
     */
    protected $product;

    /**
     * Resource data.
     *
     * @var string
     */
    protected $data;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        $explodeValue = explode('::', $data['value']);
        if (!empty($explodeValue) && count($explodeValue) > 1) {
            $fixtureCode = $explodeValue[0];
            $dataset = isset($explodeValue[2]) ? $explodeValue[1] : '';
            $searchValue = isset($explodeValue[2]) ? $explodeValue[2] : $explodeValue[1];
            $this->product = $fixtureFactory->createByCode($fixtureCode, ['dataset' => $dataset]);
            if (!$this->product->hasData('id')) {
                $this->product->persist();
            }
            if ($this->product->hasData($searchValue)) {
                $getProperty = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $searchValue)));
                $this->data = $this->product->$getProperty();
            } else {
                $this->data = $searchValue;
            }
        } else {
            $this->data = strval($data['value']);
        }
    }

    /**
     * Persist catalog search query.
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data.
     *
     * @param string|null $key
     * @return string
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
     * Get product fixture to search.
     *
     * @return InjectableFixture
     */
    public function getProduct()
    {
        return $this->product;
    }
}
