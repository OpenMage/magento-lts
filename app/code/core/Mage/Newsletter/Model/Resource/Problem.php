<?php
/**
 * Newsletter problem resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Newsletter
 */
class Mage_Newsletter_Model_Resource_Problem extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('newsletter/problem', 'problem_id');
    }
}
