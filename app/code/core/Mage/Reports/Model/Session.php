<?php
/**
 * Reports Session Model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Reports
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
