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

namespace Mage\Catalog\Test\Fixture\CatalogProductSimple;

use Magento\Mtf\Fixture\DataSource;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Repository\RepositoryFactory;

/**
 * Preset for custom options.
 *
 * Data keys:
 *  - preset (Custom options preset name)
 *  - import_products (comma separated data set name)
 */
class CustomOptions extends DataSource
{
    /**
     * Custom options data.
     *
     * @var array
     */
    protected $customOptions;

    /**
     * @constructor
     * @param RepositoryFactory $repositoryFactory
     * @param array $params
     * @param array $data
     * @param FixtureFactory|null $fixtureFactory
     */
    public function __construct(RepositoryFactory $repositoryFactory, array $params, array $data, FixtureFactory $fixtureFactory)
    {
        $this->params = $params;
        if (isset($data['dataset']) && isset($this->params['repository'])) {
            $this->data = $repositoryFactory->get($this->params['repository'])->get($data['dataset']);
            $this->customOptions = $this->data;
            unset($data['dataset']);
        }
        $this->data = array_merge_recursive($data, $this->data);
    }

    /**
     * Replace custom options data.
     *
     * @param array $data
     * @param int $replace
     * @return array
     */
    protected function replaceData(array $data, $replace)
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->replaceData($value, $replace);
            }
            $result[$key] = str_replace('%isolation%', $replace, $value);
        }

        return $result;
    }


    /**
     * Return all custom options.
     *
     * @return array
     */
    public function getCustomOptions()
    {
        return $this->customOptions;
    }
}
