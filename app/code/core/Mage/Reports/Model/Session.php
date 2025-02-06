<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Reports
 */

/**
 * Reports Session Model
 *
 * @category   Mage
 * @package    Mage_Reports
 *
 * @method $this unsData(string $value)
 */
class Mage_Reports_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /**
     * Initialize session name space
     *
     */
    public function __construct()
    {
        $this->init('reports');
    }
}
