<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * String translation model
 *
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_Translate_String _getResource()
 * @method string                                    getLocale()
 * @method Mage_Core_Model_Resource_Translate_String getResource()
 * @method int                                       getStoreId()
 * @method array                                     getStoreTranslations()
 * @method string                                    getTranslate()
 * @method $this                                     setLocale(string $value)
 * @method $this                                     setStoreId(int $value)
 * @method $this                                     setStoreTranslations(array $value)
 * @method $this                                     setTranslate(string $value)
 */
class Mage_Core_Model_Translate_String extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('core/translate_string');
    }

    /**
     * @param  string $string
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
