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
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * String translation model
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
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
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
