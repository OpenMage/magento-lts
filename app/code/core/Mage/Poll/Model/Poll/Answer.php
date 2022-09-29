<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Poll
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Poll answers model
 *
 * @category   Mage
 * @package    Mage_Poll
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Poll_Model_Resource_Poll_Answer _getResource()
 * @method Mage_Poll_Model_Resource_Poll_Answer getResource()
 * @method Mage_Poll_Model_Resource_Poll_Answer_Collection getCollection()
 * @method Mage_Poll_Model_Resource_Poll_Answer_Collection getResourceCollection()
 *
 * @method int getAnswerOrder()
 * @method $this setAnswerOrder(int $value)
 * @method string getAnswerTitle()
 * @method $this setAnswerTitle(string $value)
 * @method float getPercent()
 * @method $this setPercent(float $round)
 * @method int getPollId()
 * @method $this setPollId(int $value)
 * @method int getVotesCount()
 * @method $this setVotesCount(int $value)
 */
class Mage_Poll_Model_Poll_Answer extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('poll/poll_answer');
    }

    /**
     * @param Mage_Poll_Model_Poll $poll
     * @return $this
     */
    public function countPercent($poll)
    {
        $this->setPercent(
            round(($poll->getVotesCount() > 0 ) ? ($this->getVotesCount() * 100 / $poll->getVotesCount()) : 0, 2)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _afterSave()
    {
        Mage::getModel('poll/poll')
            ->setId($this->getPollId())
            ->resetVotesCount();
        return parent::_afterSave();
    }

    /**
     * @inheritDoc
     */
    protected function _beforeDelete()
    {
        $this->setPollId($this->load($this->getId())->getPollId());
        return parent::_beforeDelete();
    }

    /**
     * @inheritDoc
     */
    protected function _afterDelete()
    {
        Mage::getModel('poll/poll')
            ->setId($this->getPollId())
            ->resetVotesCount();
        return parent::_afterDelete();
    }
}
