<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Core\Test\Fixture;

use Magento\Mtf\Fixture\DataSource;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Data keys:
 *  - pattern (Format a local time/date with delta, e.g. 'm-d-Y -3 days' = current day - 3 days)
 */
class Date extends DataSource
{
    /**
     * @constructor
     * @param array $params
     * @param array $data [optional]
     * @throws \Exception
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['pattern']) && $data['pattern'] !== '-') {
            $matches = [];
            $delta = '';
            if (preg_match_all('/(\+|-)\d+.+/', $data['pattern'], $matches)) {
                $delta = $matches[0][0];
            }
            $timestamp = $delta === '' ? time() : strtotime($delta);
            if (!$timestamp) {
                throw new \Exception('Invalid date format for "' . $this->params['attribute_code'] . '" field');
            }
            $date = date(str_replace($delta, '', $data['pattern']), $timestamp);
            if (!$date) {
                $date = date('m/d/Y');
            }
            $this->data = trim($date);
        }
    }
}
