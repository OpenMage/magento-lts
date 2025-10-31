<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cron
 */

/**
 * Crontab schedule model
 *
 * @package    Mage_Cron
 *
 * @method Mage_Cron_Model_Resource_Schedule _getResource()
 * @method Mage_Cron_Model_Resource_Schedule getResource()
 * @method Mage_Cron_Model_Resource_Schedule_Collection getCollection()
 * @method $this setIsError(bool $value)
 * @method string getJobCode()
 * @method $this setJobCode(string $value)
 * @method string getStatus()
 * @method $this setStatus(string $value)
 * @method string getMessages()
 * @method $this setMessages(string $value)
 * @method string getScheduledAt()
 * @method $this setScheduledAt(string $value)
 * @method string getExecutedAt()
 * @method $this setExecutedAt(string $value)
 * @method string getFinishedAt()
 * @method $this setFinishedAt(string $value)
 * @method $this unsScheduleId()
 * @method array[]|false|string[] getCronExprArr()
 * @method $this setCronExprArr(array[]|false|string[] $value)
 */
class Mage_Cron_Model_Schedule extends Mage_Core_Model_Abstract
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_RUNNING = 'running';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_MISSED = 'missed';

    public const STATUS_ERROR = 'error';

    public function _construct()
    {
        $this->_init('cron/schedule');
    }

    public function getIsError(): bool
    {
        return !empty($this->getData('is_error'));
    }

    /**
     * @param string $expr
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setCronExpr($expr)
    {
        $match = preg_split('#\s+#', $expr, -1, PREG_SPLIT_NO_EMPTY);
        if (count($match) < 5 || count($match) > 6) {
            throw Mage::exception('Mage_Cron', 'Invalid cron expression: ' . $expr);
        }

        $this->setCronExprArr($match);
        return $this;
    }

    /**
     * Checks the observer's cron expression against time
     *
     * Supports $this->setCronExpr('* 0-5,10-59/5 2-10,15-25 january-june/2 mon-fri')
     *
     * @param string|int $time
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function trySchedule($time)
    {
        $expr = $this->getCronExprArr();
        if (!$expr || !$time) {
            return false;
        }

        if (!is_numeric($time)) {
            $time = strtotime($time);
        }

        if ($time === false) {
            $time = null;
        }

        $date = getdate(Mage::getSingleton('core/date')->timestamp($time));

        $match = $this->matchCronExpression($expr[0], $date['minutes'])
            && $this->matchCronExpression($expr[1], $date['hours'])
            && $this->matchCronExpression($expr[2], $date['mday'])
            && $this->matchCronExpression($expr[3], $date['mon'])
            && $this->matchCronExpression($expr[4], $date['wday']);

        if ($match) {
            $this->setCreatedAt(date(Varien_Db_Adapter_Pdo_Mysql::TIMESTAMP_FORMAT));
            $this->setScheduledAt(date('Y-m-d H:i:00', (int) $time));
        }

        return $match;
    }

    /**
     * @param string $expr
     * @param int $num
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
            $value = explode('/', $expr);
            if (count($value) !== 2) {
                throw Mage::exception('Mage_Cron', "Invalid cron expression, expecting 'match/modulus': " . $expr);
            }

            if (!is_numeric($value[1])) {
                throw Mage::exception('Mage_Cron', 'Invalid cron expression, expecting numeric modulus: ' . $expr);
            }

            $expr = $value[0];
            $mod = $value[1];
        } else {
            $mod = 1;
        }

        // handle all match by modulus
        if ($expr === '*') {
            $from = 0;
            $to = 60;
        } elseif (str_contains($expr, '-')) { // handle range
            $value = explode('-', $expr);
            if (count($value) !== 2) {
                throw Mage::exception('Mage_Cron', "Invalid cron expression, expecting 'from-to' structure: " . $expr);
            }

            $from = $this->getNumeric($value[0]);
            $to = $this->getNumeric($value[1]);
        } else { // handle regular token
            $from = $this->getNumeric($expr);
            $to = $from;
        }

        if ($from === false || $to === false) {
            throw Mage::exception('Mage_Cron', 'Invalid cron expression: ' . $expr);
        }

        return ($num >= $from) && ($num <= $to) && ($num % $mod === 0);
    }

    /**
     * @param int|string $value
     * @return int|string|false
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
     * @param string $oldStatus
     * This is used to implement locking for cron jobs.
     *
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
