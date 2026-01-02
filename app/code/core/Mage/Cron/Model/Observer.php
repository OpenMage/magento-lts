<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cron
 */

use Carbon\Carbon;

/**
 * Crontab observer
 *
 * @package    Mage_Cron
 */
class Mage_Cron_Model_Observer
{
    public const CACHE_KEY_LAST_SCHEDULE_GENERATE_AT   = 'cron_last_schedule_generate_at';

    public const CACHE_KEY_LAST_HISTORY_CLEANUP_AT     = 'cron_last_history_cleanup_at';

    public const XML_PATH_SCHEDULE_GENERATE_EVERY  = 'system/cron/schedule_generate_every';

    public const XML_PATH_SCHEDULE_AHEAD_FOR       = 'system/cron/schedule_ahead_for';

    public const XML_PATH_SCHEDULE_LIFETIME        = 'system/cron/schedule_lifetime';

    public const XML_PATH_HISTORY_CLEANUP_EVERY    = 'system/cron/history_cleanup_every';

    public const XML_PATH_HISTORY_SUCCESS          = 'system/cron/history_success_lifetime';

    public const XML_PATH_HISTORY_FAILURE          = 'system/cron/history_failure_lifetime';

    public const REGEX_RUN_MODEL = '#^([a-z0-9_]+/[a-z0-9_]+)::([a-z0-9_]+)$#i';

    protected $_pendingSchedules;

    /**
     * Process cron queue
     * Generate tasks schedule
     * Cleanup tasks schedule
     *
     * @param  Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     * @throws Throwable
     * @throws Zend_Cache_Exception
     */
    public function dispatch($observer)
    {
        $schedules = $this->getPendingSchedules();
        $jobsRoot = Mage::getConfig()->getNode('crontab/jobs');
        $defaultJobsRoot = Mage::getConfig()->getNode('default/crontab/jobs');

        /** @var Mage_Cron_Model_Schedule $schedule */
        foreach ($schedules->getIterator() as $schedule) {
            $jobCode = (string) $schedule->getJobCode();
            $jobConfig = $jobsRoot->{$jobCode};
            if (!$jobConfig || !$jobConfig->run) {
                $jobConfig = $defaultJobsRoot->{$jobCode};
                if (!$jobConfig || !$jobConfig->run) {
                    continue;
                }
            }

            $this->_processJob($schedule, $jobConfig);
        }

        $this->generate();
        $this->cleanup();
    }

    /**
     * Process cron queue for tasks marked as always
     *
     * @param  Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     * @throws Throwable
     */
    public function dispatchAlways($observer)
    {
        $jobsRoot = Mage::getConfig()->getNode('crontab/jobs');
        if ($jobsRoot instanceof Varien_Simplexml_Element) {
            foreach ($jobsRoot->children() as $jobCode => $jobConfig) {
                $this->_processAlwaysTask($jobCode, $jobConfig);
            }
        }

        $defaultJobsRoot = Mage::getConfig()->getNode('default/crontab/jobs');
        if ($defaultJobsRoot instanceof Varien_Simplexml_Element) {
            foreach ($defaultJobsRoot->children() as $jobCode => $jobConfig) {
                $this->_processAlwaysTask($jobCode, $jobConfig);
            }
        }
    }

    /**
     * @return Mage_Cron_Model_Resource_Schedule_Collection
     * @throws Mage_Core_Exception
     * @throws Zend_Cache_Exception
     */
    public function getPendingSchedules()
    {
        if (!$this->_pendingSchedules) {
            $this->_pendingSchedules = Mage::getModel('cron/schedule')->getCollection()
                ->addFieldToFilter('status', Mage_Cron_Model_Schedule::STATUS_PENDING)
                ->orderByScheduledAt()
                ->load();
        }

        return $this->_pendingSchedules;
    }

    /**
     * Generate cron schedule
     *
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Throwable
     * @throws Zend_Cache_Exception
     */
    public function generate()
    {
        /**
         * check if schedule generation is needed
         */
        $lastRun = Mage::app()->loadCache(self::CACHE_KEY_LAST_SCHEDULE_GENERATE_AT);
        if ($lastRun > Carbon::now()->subMinutes(Mage::getStoreConfigAsInt(self::XML_PATH_SCHEDULE_GENERATE_EVERY))->getTimestamp()) {
            return $this;
        }

        $schedules = $this->getPendingSchedules();
        $exists = [];
        foreach ($schedules->getIterator() as $schedule) {
            $exists[$schedule->getJobCode() . '/' . $schedule->getScheduledAt()] = 1;
        }

        /**
         * generate global crontab jobs
         */
        $config = Mage::getConfig()->getNode('crontab/jobs');
        if ($config instanceof Mage_Core_Model_Config_Element) {
            $this->_generateJobs($config->children(), $exists);
        }

        /**
         * generate configurable crontab jobs
         */
        $config = Mage::getConfig()->getNode('default/crontab/jobs');
        if ($config instanceof Mage_Core_Model_Config_Element) {
            $this->_generateJobs($config->children(), $exists);
        }

        /**
         * save time schedules generation was ran with no expiration
         */
        Mage::app()->saveCache(Carbon::now()->getTimestamp(), self::CACHE_KEY_LAST_SCHEDULE_GENERATE_AT, ['crontab'], null);

        return $this;
    }

    /**
     * Generate jobs for config information
     *
     * @param  SimpleXMLElement    $jobs
     * @param  array               $exists
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Throwable
     */
    protected function _generateJobs($jobs, $exists)
    {
        $scheduleAheadFor = Mage::getStoreConfig(self::XML_PATH_SCHEDULE_AHEAD_FOR) * 60;
        $schedule = Mage::getModel('cron/schedule');

        foreach ($jobs as $jobCode => $jobConfig) {
            $cronExpr = null;
            if ($jobConfig->schedule->config_path) {
                $cronExpr = Mage::getStoreConfig((string) $jobConfig->schedule->config_path);
            }

            if (empty($cronExpr) && $jobConfig->schedule->cron_expr) {
                $cronExpr = (string) $jobConfig->schedule->cron_expr;
            }

            if (!$cronExpr || $cronExpr == 'always') {
                continue;
            }

            $now = Carbon::now()->getTimestamp();
            $timeAhead = $now + $scheduleAheadFor;
            $schedule->setJobCode($jobCode)
                ->setCronExpr($cronExpr)
                ->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING);

            for ($time = $now; $time < $timeAhead; $time += 60) {
                $timestamp = Carbon::createFromTimestamp($time)->format('Y-m-d H:i:00');
                if (!empty($exists[$jobCode . '/' . $timestamp])) {
                    // already scheduled
                    continue;
                }

                if (!$schedule->trySchedule($time)) {
                    // time does not match cron expression
                    continue;
                }

                $schedule->unsScheduleId()->save();
            }
        }

        return $this;
    }

    /**
     * Clean up the history of tasks
     *
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Cache_Exception
     */
    public function cleanup()
    {
        // check if history cleanup is needed
        $lastCleanup = Mage::app()->loadCache(self::CACHE_KEY_LAST_HISTORY_CLEANUP_AT);
        if ($lastCleanup > Carbon::now()->subMinutes(Mage::getStoreConfigAsInt(self::XML_PATH_HISTORY_CLEANUP_EVERY))->getTimestamp()) {
            return $this;
        }

        $history = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('status', ['in' => [
                Mage_Cron_Model_Schedule::STATUS_SUCCESS,
                Mage_Cron_Model_Schedule::STATUS_MISSED,
                Mage_Cron_Model_Schedule::STATUS_ERROR,
            ]])
            ->load();

        $historyLifetimes = [
            Mage_Cron_Model_Schedule::STATUS_SUCCESS => Mage::getStoreConfig(self::XML_PATH_HISTORY_SUCCESS) * 60,
            Mage_Cron_Model_Schedule::STATUS_MISSED => Mage::getStoreConfig(self::XML_PATH_HISTORY_FAILURE) * 60,
            Mage_Cron_Model_Schedule::STATUS_ERROR => Mage::getStoreConfig(self::XML_PATH_HISTORY_FAILURE) * 60,
        ];

        $now = Carbon::now()->getTimestamp();
        foreach ($history->getIterator() as $record) {
            if (empty($record->getExecutedAt())
                || (Carbon::parse($record->getExecutedAt())->getTimestamp() < $now - $historyLifetimes[$record->getStatus()])
            ) {
                $record->delete();
            }
        }

        // save time history cleanup was ran with no expiration
        Mage::app()->saveCache(Carbon::now()->getTimestamp(), self::CACHE_KEY_LAST_HISTORY_CLEANUP_AT, ['crontab'], null);

        return $this;
    }

    /**
     * Processing cron task which is marked as always
     *
     * @param  string              $jobCode
     * @param  SimpleXMLElement    $jobConfig
     * @return $this|void
     * @throws Mage_Core_Exception
     * @throws Throwable
     */
    protected function _processAlwaysTask($jobCode, $jobConfig)
    {
        if (!$jobConfig || !$jobConfig->run) {
            return;
        }

        $cronExpr = isset($jobConfig->schedule->cron_expr) ? (string) $jobConfig->schedule->cron_expr : '';
        if ($cronExpr != 'always') {
            return;
        }

        $schedule = $this->_getAlwaysJobSchedule($jobCode);
        if ($schedule !== false) {
            $this->_processJob($schedule, $jobConfig, true);
        }

        return $this;
    }

    /**
     * Process cron task
     *
     * @param  Mage_Cron_Model_Schedule $schedule
     * @param  SimpleXMLElement         $jobConfig
     * @param  bool                     $isAlways
     * @return $this|void
     * @throws Throwable
     */
    protected function _processJob($schedule, $jobConfig, $isAlways = false)
    {
        $runConfig = $jobConfig->run;
        if (!$isAlways) {
            $scheduleLifetime = Mage::getStoreConfig(self::XML_PATH_SCHEDULE_LIFETIME) * 60;
            $now = Carbon::now()->getTimestamp();
            $time = Carbon::parse($schedule->getScheduledAt())->getTimestamp();
            if ($time > $now) {
                return;
            }
        }

        $arguments = [];
        $errorStatus = Mage_Cron_Model_Schedule::STATUS_ERROR;
        try {
            if (!$isAlways) {
                if ($time < $now - $scheduleLifetime) {
                    $errorStatus = Mage_Cron_Model_Schedule::STATUS_MISSED;
                    Mage::throwException(Mage::helper('cron')->__('Too late for the schedule.'));
                }
            }

            if ($runConfig->model) {
                if (!preg_match(self::REGEX_RUN_MODEL, (string) $runConfig->model, $run)) {
                    Mage::throwException(Mage::helper('cron')->__('Invalid model/method definition, expecting "model/class::method".'));
                }

                if (!($model = Mage::getModel($run[1])) || !method_exists($model, $run[2])) {
                    Mage::throwException(Mage::helper('cron')->__('Invalid callback: %s::%s does not exist', $run[1], $run[2]));
                }

                $callback = [$model, $run[2]];
                $arguments = [$schedule];
            }

            if (empty($callback)) {
                Mage::throwException(Mage::helper('cron')->__('No callbacks found'));
            }

            if (!$isAlways) {
                if (!$schedule->tryLockJob()) {
                    // another cron started this job intermittently, so skip it
                    return;
                }

                /**
                though running status is set in tryLockJob we must set it here because the object
                was loaded with a pending status and will set it back to pending if we don't set it here
                 */
                $schedule->setStatus(Mage_Cron_Model_Schedule::STATUS_RUNNING);
            }

            $schedule
                ->setExecutedAt(date(Varien_Db_Adapter_Pdo_Mysql::TIMESTAMP_FORMAT))
                ->save();

            call_user_func_array($callback, $arguments);

            $schedule
                ->setStatus(Mage_Cron_Model_Schedule::STATUS_SUCCESS)
                ->setFinishedAt(date(Varien_Db_Adapter_Pdo_Mysql::TIMESTAMP_FORMAT));
        } catch (Exception $exception) {
            $schedule->setStatus($errorStatus)
                ->setMessages($exception->__toString());
        }

        if ($schedule->getIsError()) {
            $schedule->setStatus(Mage_Cron_Model_Schedule::STATUS_ERROR);
        }

        $schedule->save();

        return $this;
    }

    /**
     * Get job for task marked as always
     *
     * @param  string                   $jobCode
     * @return Mage_Cron_Model_Schedule
     * @throws Mage_Core_Exception
     * @throws Throwable
     */
    protected function _getAlwaysJobSchedule($jobCode)
    {
        /** @var Mage_Cron_Model_Schedule $schedule */
        $schedule = Mage::getModel('cron/schedule')->load($jobCode, 'job_code');
        if ($schedule->getId() === null) {
            $timestamp = Carbon::now()->format('Y-m-d H:i:00');
            $schedule->setJobCode($jobCode)
                ->setCreatedAt($timestamp)
                ->setScheduledAt($timestamp);
        }

        $schedule->setStatus(Mage_Cron_Model_Schedule::STATUS_RUNNING)->save();
        return $schedule;
    }
}
