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
 * @package     Mage_Connect
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Maged_BruteForce_Validator
{
    const MODEL_KEY_ATTEMPTS_COUNT = "brute-force-attempts-count";
    const MODEL_KEY_BAD_ATTEMPTS_COUNT = "brute-force-bad-attempts-count";
    const MODEL_KEY_LAST_BAD_TIME = "brute-force-last-bad-time";
    const MODEL_KEY_DIFF_TIME_TO_ATTEMPT = "brute-force-diff-time-to-attempt";

    const DEFAULT_ATTEMPTS_COUNT = 3;
    const DEFAULT_BAD_ATTEMPTS_COUNT = 0;
    const DEFAULT_DIFF_TIME_TO_ATTEMPT = 180;// 3 minutes


    /** @var Maged_Model_BruteForce_ModelConfigInterface */
    protected $model;

    /**
     * BruteForce constructor.
     * @param Maged_Model_BruteForce_ModelConfigInterface $model
     */
    public function __construct(Maged_Model_BruteForce_ModelConfigInterface $model)
    {
        $this->model = $model;
    }

    /**
     * @return bool
     */
    public function isCanLogin()
    {
        $badAttempts = $this->getBadAttempts();
        $configAttemptsCount = $this->getConfigAttemptsCount();

        if ($badAttempts >= $configAttemptsCount and $badAttempts % $configAttemptsCount === 0) {
            $lastBadLogin = intval($this->model->get(self::MODEL_KEY_LAST_BAD_TIME));
            if ($lastBadLogin > 0) {
                $timeDiff = $this->model->get(self::MODEL_KEY_DIFF_TIME_TO_ATTEMPT, self::DEFAULT_DIFF_TIME_TO_ATTEMPT);
                $currentTime = time();
                $checkTime = $lastBadLogin + $timeDiff;
                if ($checkTime > $currentTime) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @return int
     */
    protected function getBadAttempts()
    {
        return (int)$this->model->get(self::MODEL_KEY_BAD_ATTEMPTS_COUNT, self::DEFAULT_BAD_ATTEMPTS_COUNT);
    }

    /**
     * @return int
     */
    protected function getConfigAttemptsCount()
    {
        return (int)$this->model->get(self::MODEL_KEY_ATTEMPTS_COUNT, self::DEFAULT_ATTEMPTS_COUNT);
    }

    /**
     * @return int
     */
    public function getTimeToAttempt()
    {
        return (int)$this->model->get(self::MODEL_KEY_DIFF_TIME_TO_ATTEMPT, self::DEFAULT_DIFF_TIME_TO_ATTEMPT);
    }

    /**
     * @return $this
     */
    public function doGoodLogin()
    {
        $this->reset();
        return $this;
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->model
            ->set(self::MODEL_KEY_BAD_ATTEMPTS_COUNT, self::DEFAULT_BAD_ATTEMPTS_COUNT)
            ->set(self::MODEL_KEY_DIFF_TIME_TO_ATTEMPT, self::DEFAULT_DIFF_TIME_TO_ATTEMPT)
            ->delete(self::MODEL_KEY_LAST_BAD_TIME)
            ->save();
    }

    /**
     * @return $this
     */
    public function doBadLogin()
    {
        $badAttempts = $this->getBadAttempts() + 1;
        $configAttemptsCount = $this->getConfigAttemptsCount();
        $timeToNextLogin = $this->getDiffTimeToNextAttempt();

        if ($badAttempts % $configAttemptsCount == 0 and $badAttempts != $configAttemptsCount) {
            $timeToNextLogin += self::DEFAULT_DIFF_TIME_TO_ATTEMPT;
        }

        $this->model
            ->set(self::MODEL_KEY_BAD_ATTEMPTS_COUNT, $badAttempts)
            ->set(self::MODEL_KEY_DIFF_TIME_TO_ATTEMPT, $timeToNextLogin)
            ->set(self::MODEL_KEY_ATTEMPTS_COUNT, $configAttemptsCount)
            ->set(self::MODEL_KEY_LAST_BAD_TIME, time())
            ->save();

        return $this;
    }

    /**
     * @return int
     */
    protected function getDiffTimeToNextAttempt()
    {
        return (int)$this->model->get(self::MODEL_KEY_DIFF_TIME_TO_ATTEMPT, self::DEFAULT_DIFF_TIME_TO_ATTEMPT);
    }
}
