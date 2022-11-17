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
 * @package    Mage_Poll
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Poll
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Poll_Model_Poll_Answer[] getItems()
 */
class Mage_Poll_Model_Resource_Poll_Answer_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection
     *
     */
    public function _construct()
    {
        $this->_init('poll/poll_answer');
    }

    /**
     * Add poll filter
     *
     * @param int $pollId
     * @return $this
     */
    public function addPollFilter($pollId)
    {
        $this->getSelect()->where("poll_id IN(?) ", $pollId);
        return $this;
    }

    /**
     * Count percent
     *
     * @param Mage_Poll_Model_Poll $pollObject
     * @return $this
     */
    public function countPercent($pollObject)
    {
        if (!$pollObject) {
            return;
        } else {
            foreach ($this->getItems() as $answer) {
                $answer->countPercent($pollObject);
            }
        }
        return $this;
    }
}
