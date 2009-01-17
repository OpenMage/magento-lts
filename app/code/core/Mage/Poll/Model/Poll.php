<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Poll
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Poll model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Poll_Model_Poll extends Mage_Core_Model_Abstract
{
    const XML_PATH_POLL_CHECK_BY_IP = 'web/polls/poll_check_by_ip';

    protected $_pollCookieDefaultName = 'poll';
    protected $_answersCollection   = array();
    protected $_storeCollection     = array();

    protected function _construct()
    {
        $this->_init('poll/poll');
    }

    /**
     * Check if validation by IP option is enabled in config
     *
     * @return bool
     */
    public function isValidationByIp()
    {
        return (1 == Mage::getStoreConfig(self::XML_PATH_POLL_CHECK_BY_IP));
    }

    /**
     * Declare poll as voted
     *
     * @param   int $pollId
     * @return  Mage_Poll_Model_Poll
     */
    public function setVoted($pollId=null)
    {
        $pollId = $pollId === null ? $this->getId() : $pollId;
        Mage::getSingleton('core/cookie')->set($this->_pollCookieDefaultName . $pollId, $pollId);
        return $this;
    }

    /**
     * Check if poll is voted
     *
     * @param   int $pollId
     * @return  bool
     */
    public function isVoted($pollId = null)
    {
        $pollId = $pollId === null ? $this->getId() : $pollId;

        // check if it is in cookie
        $cookie = Mage::getSingleton('core/cookie')->get($this->_pollCookieDefaultName . $pollId);
        if (false !== $cookie) {
            return true;
        }

        // check by ip
        if (count($this->_getResource()->getVotedPollIdsByIp($_SERVER['REMOTE_ADDR'], $pollId))) {
            return true;
        }

        return false;
    }

    /**
     * Get random active pool identifier
     *
     * @return int
     */
    public function getRandomId()
    {
        return $this->_getResource()->getRandomId($this);
    }

    /**
     * Add vote to poll
     *
     * @return unknown
     */
    public function addVote(Mage_Poll_Model_Poll_Vote $vote)
    {
        if ($this->hasAnswer($vote->getPollAnswerId())) {
            $vote->setPollId($this->getId())
                ->save();
            $this->setVoted();
        }
        return $this;
    }

    /**
     * Check answer existing for poll
     *
     * @param   mixed $answer
     * @return  boll
     */
    public function hasAnswer($answer)
    {
        $answerId = false;
        if (is_numeric($answer)) {
            $answerId = $answer;
        }
        elseif ($answer instanceof Mage_Poll_Model_Poll_Answer) {
        	$answerId = $answer->getId();
        }

        if ($answerId) {
            return $this->_getResource()->checkAnswerId($this, $answerId);
        }
        return false;
    }

    public function resetVotesCount()
    {
        $this->_getResource()->resetVotesCount($this);
        return $this;
    }


    public function getVotedPollsIds()
    {
        $idsArray = array();

        // load from cookies
        foreach ($_COOKIE as $cookieName => $cookieValue) {
            $pattern = "/^" . $this->_pollCookieDefaultName . "([0-9]*?)$/";
            if (preg_match($pattern, $cookieName, $m)) {
                if ($m[1] != Mage::getSingleton('core/session')->getJustVotedPoll()) {
                    $idsArray[$m[1]] = $m[1];
                }
            }
        }

        // load from db for this ip
        foreach ($this->_getResource()->getVotedPollIdsByIp($_SERVER['REMOTE_ADDR']) as $pollId) {
            $idsArray[$pollId] = $pollId;
        }

        return $idsArray;
    }

    public function addAnswer($object)
    {
        $this->_answersCollection[] = $object;
        return $this;
    }

    public function getAnswers()
    {
        return $this->_answersCollection;
    }

    public function addStoreId($storeId)
    {
        $ids = $this->getStoreIds();
        if (!in_array($storeId, $ids)) {
            $ids[] = $storeId;
        }
        $this->setStoreIds($ids);
        return $this;
    }

    public function getStoreIds()
    {
        $ids = $this->_getData('store_ids');
        if (is_null($ids)) {
            $this->loadStoreIds();
            $ids = $this->getData('store_ids');
        }
        return $ids;
    }

    public function loadStoreIds()
    {
        $this->_getResource()->loadStoreIds($this);
    }

    public function getVotesCount()
    {
        return $this->_getData('votes_count');
    }

}
