<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * API2 User Abstract Class
 *
 * @package    Mage_Api2
 */
abstract class Mage_Api2_Model_Auth_User_Abstract
{
    /**
     * Customer/Admin identifier
     *
     * @var int
     */
    protected $_userId;

    /**
     * User Role
     *
     * @var int
     */
    protected $_role;

    /**
     * Retrieve user human-readable label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->getType();
    }

    /**
     * Retrieve user role
     *
     * @return int
     */
    abstract public function getRole();

    /**
     * Retrieve user type
     *
     * @return string
     */
    abstract public function getType();

    /**
     * Retrieve user identifier
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->_userId;
    }

    /**
     * Set user identifier
     *
     * @param int $userId User identifier
     * @return Mage_Api2_Model_Auth_User_Abstract
     */
    public function setUserId($userId)
    {
        $this->_userId = $userId;

        return $this;
    }
}
