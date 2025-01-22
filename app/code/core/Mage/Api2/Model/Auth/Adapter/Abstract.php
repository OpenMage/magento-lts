<?php

/**
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
