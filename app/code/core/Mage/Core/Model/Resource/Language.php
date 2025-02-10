<?php
/**
 * Language resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Language extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('core/language', 'language_code');
    }
}
