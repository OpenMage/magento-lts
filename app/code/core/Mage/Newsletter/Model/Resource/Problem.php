<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Newsletter
 */

/**
 * Newsletter problem resource model
 *
 * @category   Mage
 * @package    Mage_Newsletter
 */
class Mage_Newsletter_Model_Resource_Problem extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('newsletter/problem', 'problem_id');
    }
}
