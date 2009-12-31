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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Poll
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Poll block
 *
 * @file        Poll.php
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Poll_Block_ActivePoll extends Mage_Core_Block_Template
{
    protected $_templates, $_voted;

    public function __construct()
    {
        parent::__construct();

        $pollModel = Mage::getModel('poll/poll');
        // get last voted poll (from session only)
        $pollId = Mage::getSingleton('core/session')->getJustVotedPoll();
        if (empty($pollId)) {
            // get random not voted yet poll
            $votedIds = $pollModel->getVotedPollsIds();
            $pollId = $pollModel->setExcludeFilter($votedIds)
                ->setStoreFilter(Mage::app()->getStore()->getId())
                ->getRandomId();
        }
        if (empty($pollId)) {
            return false;
        }
        $poll = $pollModel->load($pollId);

        $pollAnswers = Mage::getModel('poll/poll_answer')
            ->getResourceCollection()
            ->addPollFilter($pollId)
            ->load()
            ->countPercent($poll);

        // correct rounded percents to be always equal 100
        $percentsSorted = array();
        $answersArr = array();
        foreach ($pollAnswers as $key => $answer) {
            $percentsSorted[$key] = $answer->getPercent();
            $answersArr[$key] = $answer;
        }
        asort($percentsSorted);
        $total = 0;
        foreach ($percentsSorted as $key => $value) {
            $total += $value;
        }
        // change the max value only
        if ($total > 0 && $total !== 100) {
            $answersArr[$key]->setPercent($value + 100 - $total);
        }

        $this->assign('poll', $poll)
             ->assign('poll_answers', $pollAnswers)
             ->assign('action', Mage::getUrl('poll/vote/add', array('poll_id' => $pollId, '_secure' => true)));

        $this->_voted = Mage::getModel('poll/poll')->isVoted($pollId);
        Mage::getSingleton('core/session')->setJustVotedPoll(false);
    }

    public function setPollTemplate($template, $type)
    {
        $this->_templates[$type] = $template;
        return $this;
    }

    protected function _toHtml()
    {
        if( $this->_voted === true ) {
            $this->setTemplate($this->_templates['results']);
        } else {
            $this->setTemplate($this->_templates['poll']);
        }
        return parent::_toHtml();
    }
}
