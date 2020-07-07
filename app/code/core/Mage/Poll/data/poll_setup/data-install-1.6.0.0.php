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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Poll
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var Mage_Core_Model_Resource_Setup $installer */

$installer = $this;

$pollModel = Mage::getModel('poll/poll');

$pollModel  ->setDatePosted(now())
            ->setPollTitle('What is your favorite color')
            ->setStoreIds(array(1));

$answers  = array(
                array('Green', 4),
                array('Red', 1),
                array('Black', 0),
                array('Magenta', 2)
                );

foreach ($answers as $key => $answer) {
    $answerModel = Mage::getModel('poll/poll_answer');
    $answerModel->setAnswerTitle($answer[0])
                ->setVotesCount($answer[1]);

    $pollModel->addAnswer($answerModel);
}

$pollModel->save();
