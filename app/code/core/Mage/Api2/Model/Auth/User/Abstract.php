<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 User Abstract Class
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
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
