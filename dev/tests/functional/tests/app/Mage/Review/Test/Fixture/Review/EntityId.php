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

namespace Mage\Review\Test\Fixture\Review;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Source for entity id fixture.
 */
class EntityId extends InjectableFixture
{
    /**
     * Configuration settings.
     *
     * @var array
     */
    protected $params;

    /**
     * Id of the created entity.
     *
     * @var int
     */
    protected $data;

    /**
     * The created entity.
     *
     * @var FixtureInterface
     */
    protected $entity;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['dataset'])) {
            list($typeFixture, $dataset) = explode('::', $data['dataset']);
            $fixture = $fixtureFactory->createByCode($typeFixture, ['dataset' => $dataset]);
            if (!$fixture->hasData('id')) {
                $fixture->persist();
            }
            $this->entity = $fixture;
            $this->data = $fixture->getId();
        }
    }

    /**
     * Persist data.
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return id of the created entity.
     *
     * @param string|null $key [optional]
     * @return int
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
     * Get entity.
     *
     * @return FixtureInterface|null
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
