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

namespace Mage\Tax\Test\Handler\TaxRule;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Curl as AbstractCurl;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Curl handler for creating Tax Rule.
 */
class Curl extends AbstractCurl implements TaxRuleInterface
{
    /**
     * Default Tax Class values.
     *
     * @var array
     */
    protected $defaultTaxClasses = [
        'tax_customer_class' => 3, // Retail Customer
        'tax_product_class' => 2, // Taxable Goods
    ];

    /**
     * Post request for creating tax rule.
     *
     * @param FixtureInterface $fixture [optional]
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);

        $url = $_ENV['app_backend_url'] . 'tax_rule/save/?back=1';
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.1', [], $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'success-msg')) {
            throw new \Exception("Tax rule creation by curl handler was not successful!\nResponse:\n$response");
        }
        $id = $this->getTaxRuleId($response);

        return ['id' => $id];
    }

    /**
     * Returns data for curl POST params.
     *
     * @param FixtureInterface $fixture
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function prepareData(FixtureInterface $fixture)
    {
        $data = $fixture->getData();
        $fields = [
            'tax_rate',
            'tax_customer_class',
            'tax_product_class',
        ];

        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                $data[$field][] = $this->defaultTaxClasses[$field];
                continue;
            }
            $fieldFixture = $fixture->getDataFieldConfig($field);
            $fieldFixture = $fieldFixture['source']->getFixtures();
            foreach ($data[$field] as $key => $value) {
                $id = $fieldFixture[$key]->getId();
                if ($id === null) {
                    $fieldFixture[$key]->persist();
                    $id = $fieldFixture[$key]->getId();
                }
                $data[$field][$key] = $id;
            }
        }

        return $data;
    }

    /**
     * Return saved tax rule id.
     *
     * @param string $response
     * @return int|null
     */
    protected function getTaxRuleId($response)
    {
        preg_match_all('~tax_rule/edit[^\s]*\/rule\/(\d+)~', $response, $match);

        return max(empty($match[1]) ? null : $match[1]);
    }
}
