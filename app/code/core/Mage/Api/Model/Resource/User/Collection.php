<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Api User Resource Collection
 *
 * @package    Mage_Api
 *
 * @extends Mage_Core_Model_Resource_Db_Collection_Abstract<Mage_Api_Model_User>
 */
class Mage_Api_Model_Resource_User_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('api/user');
    }
}
