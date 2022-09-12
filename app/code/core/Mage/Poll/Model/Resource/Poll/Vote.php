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
 * Poll vote resource model
 *
 * @category   Mage
 * @package    Mage_Poll
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Poll_Model_Resource_Poll_Vote extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize vote resource
     *
     */
    protected function _construct()
    {
        $this->_init('poll/poll_vote', 'vote_id');
    }

    /**
     * Perform actions after object save
     *
     * @param Mage_Core_Model_Abstract|Mage_Poll_Model_Poll_Vote $object
     * @return $this
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        /**
         * Increase answer votes
         */
        $answerTable = $this->getTable('poll/poll_answer');
        $pollAnswerData = ['votes_count' => new Zend_Db_Expr('votes_count+1')];
        $condition = ["{$answerTable}.answer_id=?" => $object->getPollAnswerId()];
        $this->_getWriteAdapter()->update($answerTable, $pollAnswerData, $condition);

        /**
         * Increase poll votes
         */
        $pollTable = $this->getTable('poll/poll');
        $pollData = ['votes_count' => new Zend_Db_Expr('votes_count+1')];
        $condition = ["{$pollTable}.poll_id=?" => $object->getPollId()];
        $this->_getWriteAdapter()->update($pollTable, $pollData, $condition);
        return $this;
    }
}
