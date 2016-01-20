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

namespace Mage\Core\Test\Handler\ConfigData;

use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Magento\Mtf\Handler\Curl as AbstractCurl;

/**
 * Curl for setting config.
 */
class Curl extends AbstractCurl implements ConfigDataInterface
{
    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'scope' => [
            'Website' => 'website',
            'Store' => 'group',
            'Store View' => 'store',
        ],
    ];

    /**
     * Post request for setting configuration.
     *
     * @param FixtureInterface|null $fixture [optional]
     * @return void
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        foreach ($data as $scope => $item) {
            $this->applyConfigSettings($item, $scope);
        }
    }

    /**
     * Prepare POST data for setting configuration.
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture)
    {
        $result = [];
        $fields = $fixture->getData();
        if (isset($fields['section'])) {
            foreach ($fields['section'] as $key => $itemSection) {
                if (is_array($itemSection)) {
                    $itemSection['path'] = $key;
                }
                parse_str($this->prepareConfigPath($itemSection), $configPath);
                $result = array_merge_recursive($result, $configPath);
            }
        }
        return $result;
    }

    /**
     * Prepare config path.
     *
     * From payment/cashondelivery/active to ['payment']['groups']['cashondelivery']['fields']['active']
     *
     * @param array $input
     * @return string
     */
    protected function prepareConfigPath(array $input)
    {
        $resultArray = '';
        $InputValue = isset($input['value']) ? $input['value'] : null;
        $path = explode('/', $input['path']);
        foreach ($path as $position => $subPath) {
            if ($position === 0) {
                $resultArray .= $subPath;
                continue;
            } elseif ($position === (count($path) - 1)) {
                $resultArray .= '[fields]';
            } else {
                $resultArray .= '[groups]';
            }
            $resultArray .= '[' . $subPath . ']';
        }
        $resultArray .= '[value]';
        if (is_array($InputValue)) {
            $values = [];
            foreach ($InputValue as $key => $value) {
                $values[] = $resultArray . "[$key]=$value";
            }
            $resultArray = implode('&', $values);
        } elseif(!empty($InputValue)) {
            $resultArray .= '=' . $InputValue;
        }
        return $resultArray;
    }

    /**
     * Apply config settings via curl.
     *
     * @param array $data
     * @param string $section
     * @throws \Exception
     */
    protected function applyConfigSettings(array $data, $section)
    {
        $url = $this->getUrl($section);
        $curl = new BackendDecorator(new CurlTransport(), $this->_configuration);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.1', [], $data);
        $response = $curl->read();
        $curl->close();

        if (strpos($response, 'class="success-msg"') === false) {
            throw new \Exception("Settings are not applied! Response: $response");
        }
    }

    /**
     * Retrieve URL for request.
     *
     * @param string $section
     * @return string
     */
    protected function getUrl($section)
    {
        return $_ENV['app_backend_url'] . 'system_config/save/section/' . $section;
    }
}
