<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * String translation model
 *
 * @category   Mage
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_Translate_String _getResource()
 * @method Mage_Core_Model_Resource_Translate_String getResource()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getTranslate()
 * @method $this setTranslate(string $value)
 * @method array getStoreTranslations()
 * @method $this setStoreTranslations(array $value)
 * @method string getLocale()
 * @method $this setLocale(string $value)
 */
class Mage_Core_Model_Translate_String extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('core/translate_string');
    }

    /**
     * @param string $string
     * @return $this
     */
    public function setString($string)
    {
        $this->setData('string', $string);
        //$this->setData('string', strtolower($string));
        return $this;
    }

    /**
     * Retrieve string
     *
     * @return string
     */
    public function getString()
    {
        //return strtolower($this->getData('string'));
        return $this->getData('string');
    }
}
