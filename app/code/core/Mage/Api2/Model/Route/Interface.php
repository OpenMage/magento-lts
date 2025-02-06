<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api2
 */

/**
 * Webservice apia2 route interface
 *
 * @category   Mage
 * @package    Mage_Api2
 */
interface Mage_Api2_Model_Route_Interface
{
    /**
     * Matches a Request with parts defined by a map. Assigns and
     * returns an array of variables on a successful match.
     *
     * @param Mage_Api2_Model_Request $request
     * @param bool $partial Partial path matching
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($request, $partial = false);
}
