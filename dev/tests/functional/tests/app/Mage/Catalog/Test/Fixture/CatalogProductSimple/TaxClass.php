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

namespace Mage\Catalog\Test\Fixture\CatalogProductSimple;

use Magento\Mtf\Config\DataInterface;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mage\Tax\Test\Fixture\TaxClass as FixtureTaxClass;

/**
 * Tax class preset.
 *
 * Data keys:
 *  - dataSet
 *  - tax_product_class
 */
class TaxClass implements FixtureInterface
{
    /**
     * Tax class id.
     *
     * @var int
     */
    protected $taxClassId;

    /**
     * Tax class name.
     *
     * @var string
     */
    protected $data = 'None';

    /**
     * Tax class fixture.
     *
     * @var FixtureTaxClass
     */
    protected $taxClass;

    /**
     * Configuration data.
     *
     * @var DataInterface
     */
    protected $configuration;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param DataInterface $configuration
     * @param array $params
     * @param array|string $data [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, DataInterface $configuration, array $params, $data = [])
    {
        $this->params = $params;
        $this->configuration = $configuration;
        if ((!isset($data['dataSet']) && !isset($data['tax_product_class']))) {
            $this->data = $data;
            return;
        }

        if (isset($data['dataSet'])) {
            $this->taxClass = $fixtureFactory->createByCode('taxClass', ['dataSet' => $data['dataSet']]);
            $this->data = $this->taxClass->getClassName();
            if (!$this->taxClass->hasData('id')) {
                $this->taxClass->persist();
            }
        }

        if (isset($data['tax_product_class']) && $data['tax_product_class'] instanceof FixtureTaxClass) {
            $this->taxClass = $data['tax_product_class'];
            $this->data = $this->taxClass->getClassName();
        }

        if ($this->taxClass->hasData('id')) {
            $this->taxClassId = $this->taxClass->getId();
        } else {
            $this->setTaxClassId($this->data);
        }
    }

    /**
     * Set tax class id.
     *
     * @param string $taxClassName
     * @return void
     * @throws \Exception
     */
    protected function setTaxClassId($taxClassName)
    {
        $url = $_ENV['app_backend_url'] . 'tax/rule/new/';
        $curl = new BackendDecorator(new CurlTransport(), $this->configuration);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', [], []);
        $response = $curl->read();
        $curl->close();

        preg_match('~<option value="(\d+)".*>' . $taxClassName . '</option>~', $response, $matches);
        if (!isset($matches[1]) || empty($matches[1])) {
            throw new \Exception('Product tax class id ' . $taxClassName . ' undefined!');
        }

        $this->taxClassId = (int)$matches[1];
    }

    /**
     * Persist custom selections tax classes.
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
     * @param string|null $key
     * @return string
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
     * Return tax class fixture.
     *
     * @return FixtureTaxClass
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }

    /**
     * Return tax class id.
     *
     * @return int
     */
    public function getTaxClassId()
    {
        return (int)$this->taxClassId;
    }
}
