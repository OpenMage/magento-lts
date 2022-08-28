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
 * @category    Mage
 * @package     Mage_Poll
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Poll model
 *
 * @method Mage_Poll_Model_Resource_Poll _getResource()
 * @method Mage_Poll_Model_Resource_Poll getResource()
 * @method Mage_Poll_Model_Resource_Poll_Collection getCollection()
 *
 * @method int getActive()
 * @method $this setActive(int $value)
 * @method int getAnswersDisplay()
 * @method $this setAnswersDisplay(int $value)
 * @method int getClosed()
 * @method $this setClosed(int $value)
 * @method string getDateClosed()
 * @method $this setDateClosed(string $value)
 * @method string getDatePosted()
 * @method $this setDatePosted(string $value)
 * @method array getExcludeFilter()
 * @method $this setExcludeFilter(array $value)
 * @method string getPollTitle()
 * @method $this setPollTitle(string $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method $this setStoreIds(array $value)
 * @method int getStoreFilter()
 * @method $this setStoreFilter(int $value)
 * @method $this setVotesCount(int $value)
 *
 * @category    Mage
 * @package     Mage_Poll
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Poll_Model_Poll extends Mage_Core_Model_Abstract
{
    const XML_PATH_POLL_CHECK_BY_IP = 'web/polls/poll_check_by_ip';

    protected $_pollCookieDefaultName = 'poll';

    /**
     * @var Mage_Poll_Model_Poll_Answer[]
     */
    protected $_answersCollection   = [];
    protected $_storeCollection     = [];

    protected function _construct()
    {
        $this->_init('poll/poll');
    }

    /**
     * Retrieve Cookie Object
     *
     * @return Mage_Core_Model_Cookie
     */
    public function getCookie()
    {
        return Mage::app()->getCookie();
    }

    /**
     * Get Cookie Name
     *
     * @param int $pollId
     * @return string
     */
    public function getCookieName($pollId = null)
    {
        return $this->_pollCookieDefaultName . $this->getPollId($pollId);
    }

    /**
     * Retrieve defined or current Id
     *
     * @deprecated since 1.7.0.0
     * @param int $pollId
     * @return int
     */
    public function getPoolId($pollId = null)
    {
        return $this->getPollId($pollId);
    }

    /**
     * Retrieve defined or current Id
     *
     * @param string $pollId
     * @return string
     */
    public function getPollId($pollId = null)
    {
        if (is_null($pollId)) {
            $pollId = $this->getId();
        }
        return $pollId;
    }

    /**
     * Check if validation by IP option is enabled in config
     *
     * @return bool
     */
    public function isValidationByIp()
    {
        return (Mage::getStoreConfig(self::XML_PATH_POLL_CHECK_BY_IP) == 1);
    }

    /**
     * Declare poll as voted
     *
     * @param   int $pollId
     * @return  $this
     */
    public function setVoted($pollId = null)
    {
        $this->getCookie()->set($this->getCookieName($pollId), $this->getPollId($pollId));

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
        $pollId = $this->getPollId($pollId);

        // check if it is in cookie
        $cookie = $this->getCookie()->get($this->getCookieName($pollId));
        if ($cookie !== false) {
            return true;
        }

        // check by ip
        if (count($this->_getResource()->getVotedPollIdsByIp(Mage::helper('core/http')->getRemoteAddr(), $pollId))) {
            return true;
        }

        return false;
    }

    /**
     * Get random active pool identifier
     *
     * @return string
     */
    public function getRandomId()
    {
        return $this->_getResource()->getRandomId($this);
    }

    /**
     * Get all ids for not closed polls
     *
     * @return array
     */
    public function getAllIds()
    {
        return $this->_getResource()->getAllIds($this);
    }

    /**
     * Add vote to poll
     *
     * @param Mage_Poll_Model_Poll_Vote $vote
     * @return $this
     * @throws Exception
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
     * @return  bool
     */
    public function hasAnswer($answer)
    {
        $answerId = false;
        if (is_numeric($answer)) {
            $answerId = $answer;
        } elseif ($answer instanceof Mage_Poll_Model_Poll_Answer) {
            $answerId = $answer->getId();
        }

        if ($answerId) {
            return $this->_getResource()->checkAnswerId($this, $answerId);
        }
        return false;
    }

    /**
     * @return $this
     */
    public function resetVotesCount()
    {
        $this->_getResource()->resetVotesCount($this);
        return $this;
    }

    /**
     * @return array
     */
    public function getVotedPollsIds()
    {
        $idsArray = [];

        foreach ($this->getCookie()->get() as $cookieName => $cookieValue) {
            $pattern = '#^' . preg_quote($this->_pollCookieDefaultName, '#') . '(\d+)$#';
            $match   = [];
            if (preg_match($pattern, $cookieName, $match)) {
                if ($match[1] != Mage::getSingleton('core/session')->getJustVotedPoll()) {
                    $idsArray[$match[1]] = $match[1];
                }
            }
        }

        // load from db for this ip
        foreach ($this->_getResource()->getVotedPollIdsByIp(Mage::helper('core/http')->getRemoteAddr()) as $pollId) {
            $idsArray[$pollId] = $pollId;
        }

        return $idsArray;
    }

    /**
     * @param Mage_Poll_Model_Poll_Answer $object
     * @return $this
     */
    public function addAnswer($object)
    {
        $this->_answersCollection[] = $object;
        return $this;
    }

    /**
     * @return Mage_Poll_Model_Poll_Answer[]
     */
    public function getAnswers()
    {
        return $this->_answersCollection;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function addStoreId($storeId)
    {
        $ids = $this->getStoreIds();
        if (!in_array($storeId, $ids)) {
            $ids[] = $storeId;
        }
        $this->setStoreIds($ids);
        return $this;
    }

    /**
     * @return array
     */
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

    /**
     * @return int
     */
    public function getVotesCount()
    {
        return $this->_getData('votes_count');
    }
}
