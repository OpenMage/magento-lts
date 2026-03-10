<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Newsletter
 */

/**
 * Newsletter problem resource model
 *
 * @package    Mage_Newsletter
 */
class Mage_Newsletter_Model_Resource_Problem extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('newsletter/problem', 'problem_id');
    }
}
