<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity_Increment_Numeric extends Mage_Eav_Model_Entity_Increment_Abstract
{
    /**
     * @return string
     */
    public function getNextId()
    {
        $last = $this->getLastId();

        if (empty($last)) {
            $last = 0;
        } elseif (!empty($prefix = (string) $this->getPrefix()) && str_starts_with($last, $prefix)) {
            $last = (int) substr($last, strlen($prefix));
        } else {
            $last = (int) $last;
        }

        $next = $last + 1;

        return $this->format($next);
    }
}
