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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Handler;

use Magento\Mtf\Util\Protocol\CurlInterface;
use Magento\Mtf\Util\Protocol\CurlTransport;
use Magento\Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Used to omit possible issue, when searched Id is not on the same page in cURL response.
 */
class Extractor
{
    /**
     * Pattern for searching grid table in cURL response.
     *
     * @var string
     */
    protected $regExpPattern;

    /**
     * Url of cURL request.
     *
     * @var string
     */
    protected $url;

    /**
     * Flag is search all match.
     *
     * @var bool
     */
    protected $isAll;

    /**
     * Setting all Pagination params for Pagination object.
     * Required url for cURL request and regexp pattern for searching in cURL response.
     *
     * @param string $url
     * @param string $regExpPattern
     * @param bool $isAll [optional]
     */
    public function __construct($url, $regExpPattern, $isAll = false)
    {
        $this->url = $url;
        $this->regExpPattern = $regExpPattern;
        $this->isAll = $isAll;
    }

    /**
     * Retrieves data from cURL response
     *
     * @throws \Exception
     * @return array
     */
    public function getData()
    {
        /** @var \Magento\Mtf\Config\DataInterface $config */
        $config = \Magento\Mtf\ObjectManagerFactory::getObjectManager()->get('Magento\Mtf\Config\DataInterface');
        $url = $_ENV['app_backend_url'] . $this->url;
        $curl = new BackendDecorator(new CurlTransport(), $config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write($url);
        $response = $curl->read();
        $curl->close();
        if ($this->isAll) {
            preg_match_all($this->regExpPattern, $response, $matches);
        } else {
            preg_match($this->regExpPattern, $response, $matches);
        }

        $countMatches = $this->isAll ? count($matches[1]) : count($matches);
        if ($countMatches == 0) {
            throw new \Exception('Matches array can\'t be empty.');
        }
        return $matches;
    }
}
