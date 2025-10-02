<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Event
 */

/**
 * Event cron observer object
 *
 * @package    Varien_Event
 *
 * @method string getCronExpr()
 * @method bool hasNow()
 * @method $this setNow(int $time)
 */
class Varien_Event_Observer_Cron extends Varien_Event_Observer
{
    /**
     * Checks the observer's cron string against event's name
     *
     * Supports $this->setCronExpr('* 0-5,10-59/5 2-10,15-25 january-june/2 mon-fri')
     *
     * @return bool
     */
    public function isValidFor(Varien_Event $event)
    {
        $e = preg_split('#\s+#', $this->getCronExpr(), -1, PREG_SPLIT_NO_EMPTY);
        if (count($e) !== 5) {
            return false;
        }

        $d = getdate($this->getNow());

        return $this->matchCronExpression($e[0], $d['minutes'])
            && $this->matchCronExpression($e[1], $d['hours'])
            && $this->matchCronExpression($e[2], $d['mday'])
            && $this->matchCronExpression($e[3], $d['mon'])
            && $this->matchCronExpression($e[4], $d['wday']);
    }

    /**
     * @return int
     */
    public function getNow()
    {
        if (!$this->hasNow()) {
            $this->setNow(time());
        }
        return $this->getData('now');
    }

    /**
     * @param string $expr
     * @param int $num
     * @return bool
     */
    public function matchCronExpression($expr, $num)
    {
        // handle ALL match
        if ($expr === '*') {
            return true;
        }

        // handle multiple options
        if (str_contains($expr, ',')) {
            foreach (explode(',', $expr) as $e) {
                if ($this->matchCronExpression($e, $num)) {
                    return true;
                }
            }
            return false;
        }

        // handle modulus
        if (str_contains($expr, '/')) {
            $e = explode('/', $expr);
            if (count($e) !== 2) {
                return false;
            }
            $expr = $e[0];
            $mod = $e[1];
            if (!is_numeric($mod)) {
                return false;
            }
        } else {
            $mod = 1;
        }

        // handle range
        if (str_contains($expr, '-')) {
            $e = explode('-', $expr);
            if (count($e) !== 2) {
                return false;
            }

            $from = $this->getNumeric($e[0]);
            $to = $this->getNumeric($e[1]);

            return ($from !== false) && ($to !== false)
                && ($num >= $from) && ($num <= $to) && ($num % $mod === 0);
        }

        // handle regular token
        $value = $this->getNumeric($expr);
        return ($value !== false) && ($num == $value) && ($num % $mod === 0);
    }

    /**
     * @param string|int $value
     * @return string|int|false
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
}
