<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cron
 */

use Carbon\Carbon;

/**
 * Crontab schedule model
 *
 * @package    Mage_Cron
 *
 * @method Mage_Cron_Model_Resource_Schedule            _getResource()
 * @method Mage_Cron_Model_Resource_Schedule_Collection getCollection()
 * @method array[]|false|string[]                       getCronExprArr()
 * @method string                                       getExecutedAt()
 * @method string                                       getFinishedAt()
 * @method string                                       getJobCode()
 * @method string                                       getMessages()
 * @method Mage_Cron_Model_Resource_Schedule            getResource()
 * @method Mage_Cron_Model_Resource_Schedule_Collection getResourceCollection()
 * @method string                                       getScheduledAt()
 * @method string                                       getStatus()
 * @method $this                                        setCronExprArr(array[]|false|string[] $value)
 * @method $this                                        setExecutedAt(string $value)
 * @method $this                                        setFinishedAt(string $value)
 * @method $this                                        setIsError(bool $value)
 * @method $this                                        setJobCode(string $value)
 * @method $this                                        setMessages(string $value)
 * @method $this                                        setScheduledAt(string $value)
 * @method $this                                        setStatus(string $value)
 * @method $this                                        unsScheduleId()
 */
class Mage_Cron_Model_Schedule extends Mage_Core_Model_Abstract
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_RUNNING = 'running';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_MISSED = 'missed';

    public const STATUS_ERROR = 'error';

    protected function _construct()
    {
        $this->_init('cron/schedule');
    }

    public function getIsError(): bool
    {
        return !empty($this->getDataByKey('is_error'));
    }

    /**
     * @param  string              $expr
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setCronExpr($expr)
    {
        $value = preg_split('#\s+#', $expr, -1, PREG_SPLIT_NO_EMPTY);
        if (count($value) < 5 || count($value) > 6) {
            throw Mage::exception('Mage_Cron', 'Invalid cron expression: ' . $expr);
        }

        $this->setCronExprArr($value);
        return $this;
    }

    /**
     * Checks the observer's cron expression against time
     *
     * Supports $this->setCronExpr('* 0-5,10-59/5 2-10,15-25 january-june/2 mon-fri')
     *
     * @param  int|string $time
     * @return bool
     */
    public function trySchedule($time)
    {
        $exprArr = $this->getCronExprArr();
        if (!$exprArr || !$time) {
            return false;
        }

        if (!is_numeric($time)) {
            $time = Carbon::parse($time)->getTimestamp();
        }

        if ($time === false) {
            $time = null;
        }

        $date = getdate(Mage::getSingleton('core/date')->timestamp($time));

        $match = $this->matchCronExpression($exprArr[0], $date['minutes'])
            && $this->matchCronExpression($exprArr[1], $date['hours'])
            && $this->matchCronExpression($exprArr[2], $date['mday'])
            && $this->matchCronExpression($exprArr[3], $date['mon'])
            && $this->matchCronExpression($exprArr[4], $date['wday']);

        if ($match) {
            $this->setCreatedAt(date(Varien_Db_Adapter_Pdo_Mysql::TIMESTAMP_FORMAT));
            $this->setScheduledAt(Carbon::createFromTimestamp((int) $time)->format('Y-m-d H:i:00'));
        }

        return $match;
    }

    /**
     * @param  string              $expr
     * @param  int                 $num
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function matchCronExpression($expr, $num)
    {
        // handle ALL match
        if ($expr === '*') {
            return true;
        }

        // handle multiple options
        if (str_contains($expr, ',')) {
            foreach (explode(',', $expr) as $value) {
                if ($this->matchCronExpression($value, $num)) {
                    return true;
                }
            }

            return false;
        }

        // handle modulus
        if (str_contains($expr, '/')) {
            $exprArray = explode('/', $expr);
            if (count($exprArray) !== 2) {
                throw Mage::exception('Mage_Cron', "Invalid cron expression, expecting 'match/modulus': " . $expr);
            }

            if (!is_numeric($exprArray[1])) {
                throw Mage::exception('Mage_Cron', 'Invalid cron expression, expecting numeric modulus: ' . $expr);
            }

            $expr = $exprArray[0];
            $mod = $exprArray[1];
        } else {
            $mod = 1;
        }

        // handle all match by modulus
        if ($expr === '*') {
            $min = 0;
            $max = 60;
        } elseif (str_contains($expr, '-')) { // handle range
            $exprArray = explode('-', $expr);
            if (count($exprArray) !== 2) {
                throw Mage::exception('Mage_Cron', "Invalid cron expression, expecting 'from-to' structure: " . $expr);
            }

            $min = $this->getNumeric($exprArray[0]);
            $max = $this->getNumeric($exprArray[1]);
        } else { // handle regular token
            $min = $this->getNumeric($expr);
            $max = $min;
        }

        if ($min === false || $max === false) {
            throw Mage::exception('Mage_Cron', 'Invalid cron expression: ' . $expr);
        }

        return ($num >= $min) && ($num <= $max) && ($num % $mod === 0);
    }

    /**
     * @param  int|string       $value
     * @return false|int|string
     */
    public function getNumeric($value)
    {
        static $data = [
            'jan' => 1,
            'feb' => 2,
            'mar' => 3,
            'apr' => 4,
            'may' => 5,
            'jun' => 6,
            'jul' => 7,
            'aug' => 8,
            'sep' => 9,
            'oct' => 10,
            'nov' => 11,
            'dec' => 12,

            'sun' => 0,
            'mon' => 1,
            'tue' => 2,
            'wed' => 3,
            'thu' => 4,
            'fri' => 5,
            'sat' => 6,
        ];

        if (is_numeric($value)) {
            return $value;
        }

        if (is_string($value)) {
            $value = strtolower(substr($value, 0, 3));
            if (isset($data[$value])) {
                return $data[$value];
            }
        }

        return false;
    }

    /**
     * Sets a job to STATUS_RUNNING only if it is currently in STATUS_PENDING.
     * Returns true if status was changed and false otherwise.
     *
     * This is used to implement locking for cron jobs
     *
     * @param  self::STATUS_* $oldStatus
     * @return bool
     */
    public function tryLockJob($oldStatus = self::STATUS_PENDING)
    {
        $result = $this->_getResource()->trySetJobStatusAtomic($this->getId(), self::STATUS_RUNNING, $oldStatus);
        if ($result) {
            $this->setStatus(self::STATUS_RUNNING);
        }

        return $result;
    }
}
