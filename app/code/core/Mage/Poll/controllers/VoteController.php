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
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Poll vote controller
 *
 * @category   Mage
 * @package    Mage_Poll
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Poll_VoteController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = ['add'];

    /**
     * Add Vote to Poll
     */
    public function addAction()
    {
        $pollId     = intval($this->getRequest()->getParam('poll_id'));
        $answerId   = intval($this->getRequest()->getParam('vote'));

        /** @var Mage_Poll_Model_Poll $poll */
        $poll = Mage::getModel('poll/poll')->load($pollId);

        /**
         * Check poll data
         */
        if ($poll->getId() && !$poll->getClosed() && !$poll->isVoted()) {
            $vote = Mage::getModel('poll/poll_vote')
                ->setPollAnswerId($answerId)
                ->setIpAddress(Mage::helper('core/http')->getRemoteAddr(true))
                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());

            $poll->addVote($vote);
            Mage::getSingleton('core/session')->setJustVotedPoll($pollId);
            Mage::dispatchEvent(
                'poll_vote_add',
                [
                    'poll'  => $poll,
                    'vote'  => $vote
                ]
            );
        }
        $this->_redirectReferer();
    }
}
