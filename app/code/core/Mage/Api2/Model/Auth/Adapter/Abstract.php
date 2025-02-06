<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api2
 */

/**
 * Abstract class of authentication adapter
 *
 * @category   Mage
 * @package    Mage_Api2
 */
abstract class Mage_Api2_Model_Auth_Adapter_Abstract
{
    /**
     * Process request and figure out an API user type and its identifier
     *
     * Returns stdClass object with two properties: type and id
     *
     * @return stdClass
     */
    abstract public function getUserParams(Mage_Api2_Model_Request $request);

    /**
     * Check if request contains authentication info for adapter
     *
     * @return bool
     */
    abstract public function isApplicableToRequest(Mage_Api2_Model_Request $request);
}
