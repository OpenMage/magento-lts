<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Event
 */

use Carbon\Carbon;

/**
 * Event cron observer object
 *
 * @package    Varien_Event
 *
 * @method string getCronExpr()
 * @method bool   hasNow()
 * @method $this  setNow(int $time)
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
        $expressions = preg_split('#\s+#', $this->getCronExpr(), -1, PREG_SPLIT_NO_EMPTY);
        if (count($expressions) !== 5) {
            return false;
        }

        $date = getdate($this->getNow());

        return $this->matchCronExpression($expressions[0], $date['minutes'])
            && $this->matchCronExpression($expressions[1], $date['hours'])
            && $this->matchCronExpression($expressions[2], $date['mday'])
            && $this->matchCronExpression($expressions[3], $date['mon'])
            && $this->matchCronExpression($expressions[4], $date['wday']);
    }

    /**
     * @return int
     */
    public function getNow()
    {
        if (!$this->hasNow()) {
            $this->setNow(Carbon::now()->getTimestamp());
        }

        return $this->getDataByKey('now');
    }

    /**
     * @param  string $expr
     * @param  int    $num
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
                return false;
            }

            $expr = $exprArray[0];
            $mod = $exprArray[1];
            if (!is_numeric($mod)) {
                return false;
            }
        } else {
            $mod = 1;
        }

        // handle range
        if (str_contains($expr, '-')) {
            $exprArray = explode('-', $expr);
            if (count($exprArray) !== 2) {
                return false;
            }

            $min = $this->getNumeric($exprArray[0]);
            $max = $this->getNumeric($exprArray[1]);

            return ($min !== false) && ($max !== false)
                && ($num >= $min) && ($num <= $max) && ($num % $mod === 0);
        }

        // handle regular token
        $value = $this->getNumeric($expr);
        return ($value !== false) && ($num == $value) && ($num % $mod === 0);
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
}
