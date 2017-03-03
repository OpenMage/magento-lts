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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Widget\Test\Fixture\Widget;

use Magento\Mtf\Fixture\DataSource;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Repository\RepositoryFactory;

/**
 * Prepare Layout Updates for widget.
 */
class LayoutUpdates extends DataSource
{
    /**
     * @constructor
     * @param RepositoryFactory $repositoryFactory
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(RepositoryFactory $repositoryFactory, FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['dataset']) && isset($this->params['repository'])) {
            $this->data = $repositoryFactory->get($this->params['repository'])->get($data['dataset']);
            foreach ($this->data as $index => $layouts) {
                if (isset($layouts['entities'])) {
                    foreach ($layouts['entities'] as $key => $layout) {
                        $explodeValue = explode('::', $layout);
                        $fixture = $fixtureFactory->createByCode($explodeValue[0], ['dataset' => $explodeValue[1]]);
                        $fixture->persist();
                        $this->data[$index]['entities'][$key] = $fixture->getData();
                    }
                }
            }
        } else {
            $this->data = $data;
        }
    }
}
