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

namespace Mage\Adminhtml\Test\Fixture\StoreGroup;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\FixtureInterface;
use Mage\Catalog\Test\Fixture\CatalogCategory;

/**
 * Prepare CategoryId for Store Group.
 */
class CategoryId implements FixtureInterface
{
    /**
     * Prepared dataset data.
     *
     * @var array
     */
    protected $data;

    /**
     * Data set configuration settings.
     *
     * @var array
     */
    protected $params;

    /**
     * CatalogCategory fixture.
     *
     * @var CatalogCategory
     */
    protected $category;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['category'])) {
            $this->category = $data['category'];
            $this->data = $data['category']->getName();
        } elseif (isset($data['dataset'])) {
            $category = $fixtureFactory->createByCode('catalogCategory', ['dataset' => $data['dataset']]);
            /** @var CatalogCategory $category */
            if (!$category->getId()) {
                $category->persist();
            }
            $this->category = $category;
            $this->data = $category->getName();
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
     * @return mixed
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
     * Return CatalogCategory fixture.
     *
     * @return CatalogCategory
     */
    public function getCategory()
    {
        return $this->category;
    }
}
