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
 * @category    Varien
 * @package     Varien_Event
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Event cron observer object
 *
 * @category   Varien
 * @package    Varien_Event
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method string getCronExpr()
 * @method bool hasNow()
 * @method $this setNow(int $time)
 */
class Varien_Event_Observer_Cron extends Varien_Event_Observer
{
    /**
     * Checkes the observer's cron string against event's name
     *
     * Supports $this->setCronExpr('* 0-5,10-59/5 2-10,15-25 january-june/2 mon-fri')
     *
     * @param Varien_Event $event
     * @return boolean
     */
    public function isValidFor(Varien_Event $event)
    {
        $e = preg_split('#\s+#', $this->getCronExpr(), null, PREG_SPLIT_NO_EMPTY);
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
        if ($expr==='*') {
            return true;
        }

        // handle multiple options
        if (strpos($expr,',')!==false) {
            foreach (explode(',',$expr) as $e) {
                if ($this->matchCronExpression($e, $num)) {
                    return true;
                }
            }
            return false;
        }

        // handle modulus
        if (strpos($expr,'/')!==false) {
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
        if (strpos($expr,'-')!==false) {
            $e = explode('-', $expr);
            if (count($e) !== 2) {
                return false;
            }

            $from = $this->getNumeric($e[0]);
            $to = $this->getNumeric($e[1]);

            return ($from!==false) && ($to!==false)
                && ($num>=$from) && ($num<=$to) && ($num%$mod===0);
        }

        // handle regular token
        $value = $this->getNumeric($expr);
        return ($value!==false) && ($num==$value) && ($num%$mod===0);
    }

    /**
     * @param string|int $value
     * @return string|int|false
     */
    public function getNumeric($value)
    {
        static $data = array(
            'jan'=>1,
            'feb'=>2,
            'mar'=>3,
            'apr'=>4,
            'may'=>5,
            'jun'=>6,
            'jul'=>7,
            'aug'=>8,
            'sep'=>9,
            'oct'=>10,
            'nov'=>11,
            'dec'=>12,

            'sun'=>0,
            'mon'=>1,
            'tue'=>2,
            'wed'=>3,
            'thu'=>4,
            'fri'=>5,
            'sat'=>6,
        );

        if (is_numeric($value)) {
            return $value;
        }

        if (is_string($value)) {
            $value = strtolower(substr($value,0,3));
            if (isset($data[$value])) {
                return $data[$value];
            }
        }

        return false;
    }
}
