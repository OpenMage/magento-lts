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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Review\Test\Fixture\Review;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\FixtureInterface;
use Mage\Rating\Test\Fixture\Rating;

/**
 * Source for product ratings fixture.
 */
class Ratings implements FixtureInterface
{
    /**
     * Configuration settings of fixture.
     *
     * @var array
     */
    protected $params;

    /**
     * Data of the created ratings.
     *
     * @var array
     */
    protected $data = [];

    /**
     * List of the created ratings.
     *
     * @var array
     */
    protected $ratings = [];

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        /** @var Rating $fixtureRating */
        $fixtureRating = null;
        foreach ($data as $rating) {
            if (isset($rating['dataSet'])) {
                $fixtureRating = $fixtureFactory->createByCode('rating', ['dataSet' => $rating['dataSet']]);
                if (!$fixtureRating->hasData('rating_id')) {
                    $fixtureRating->persist();
                }
            } elseif (isset($rating['fixtureRating']) && $rating['fixtureRating'] instanceof Rating) {
                $fixtureRating = $rating['fixtureRating'];
            }
            if ($fixtureRating != null) {
                $this->ratings[] = $fixtureRating;
                $this->data[] = [
                    'title' => $fixtureRating->getRatingCode(),
                    'rating' => $rating['rating']
                ];
            }
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
     * Return prepared data set.
     *
     * @param string|null $key [optional]
     * @return array
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
     * Get ratings.
     *
     * @return array
     */
    public function getRatings()
    {
        return $this->ratings;
    }
}
