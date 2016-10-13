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

namespace Mage\Catalog\Test\Handler\CatalogCategory;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Handler\Curl as AbstractCurl;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mage\Catalog\Test\Fixture\CatalogCategory;

/**
 * Create new category via curl.
 */
class Curl extends AbstractCurl implements CatalogCategoryInterface
{
    /**
     * Data use config for category.
     *
     * @var array
     */
    protected $dataUseConfig = [
        'available_sort_by',
        'default_sort_by',
        'filter_price_range',
    ];

    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'is_active' => [
            'Yes' => 1,
            'No' => 0,
        ],
        'is_anchor' => [
            'Yes' => 1,
            'No' => 0,
        ],
        'include_in_menu' => [
            'Yes' => 1,
            'No' => 0,
        ],
        'display_mode' => [
            'Static block and products' => 'PRODUCTS_AND_PAGE',
            'Static block only' => 'PAGE',
            'Products only' => 'PRODUCTS',
        ],
    ];

    /**
     * Post request for creating Subcategory.
     *
     * @param FixtureInterface|null $fixture [optional]
     * @return array
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data['general'] = $this->replaceMappingData($fixture->getData());
        if ($fixture->hasData('landing_page')) {
            $data['general']['landing_page'] = $this->getBlockId($fixture->getLandingPage());
        }
        if ($fixture->hasData('category_products')) {
            $data['category_products'] = $this->prepareCategoryProducts($fixture);
        }

        $diff = array_diff($this->dataUseConfig, array_keys($data['general']));
        if (!empty($diff)) {
            $data['use_config'] = $diff;
        }
        $parentCategoryId = $data['general']['parent_id'];
        $url = $_ENV['app_backend_url'] . 'catalog_category/save/store/0/parent/' . $parentCategoryId . '/?isAjax=true';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write($url, $data);
        $response = $curl->read();
        $curl->close();

        preg_match('~http://.+/id/(\d+).+?isAjax~', $response, $matches);
        $id = isset($matches[1]) ? (int)$matches[1] : null;

        return ['id' => $id];
    }

    /**
     * Prepare assigned products for category.
     *
     * @param CatalogCategory $category
     * @return string
     */
    protected function prepareCategoryProducts(CatalogCategory $category)
    {
        $products = $category->getDataFieldConfig('category_products')['source']->getProducts();
        $productIds = [];
        foreach ($products as $product) {
            $productIds[] = $product->getId();
        }

        return implode('=&', $productIds);
    }

    /**
     * Getting block id by name
     *
     * @param string $landingName
     * @return int|null
     */
    protected function getBlockId($landingName)
    {
        $url = $_ENV['app_backend_url'] . 'catalog_category';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->write($url, [], CurlInterface::POST);
        $response = $curl->read();
        $curl->close();
        preg_match('~<option.*value="(\d+)".*>' . preg_quote($landingName) . '</option>~', $response, $matches);
        $id = isset($matches[1]) ? (int)$matches[1] : null;

        return $id;
    }
}
