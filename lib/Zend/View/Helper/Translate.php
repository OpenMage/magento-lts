<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Translate.php 8419 2008-02-26 16:49:38Z darby $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Locale */
#require_once 'Zend/Locale.php';

/**
 * Translation view helper
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Zend_View_Helper_Translate
{
    /**
     * Translation object
     * @var Zend_Translate_Adapter
     */
    protected $_translator;

    /**
     * Constructor for manually handling
     *
     * @param Zend_Translate|Zend_Translate_Adapter $translate
     */
    public function __construct($translate = null)
    {
        if (!empty($translate)) {
            $this->setTranslator($translate);
        }
    }

    /**
     * Translate a message
     * You can give multiple params or an array of params.
     * If you want to output another locale just set it as last single parameter
     * Example 1: translate('%1\$s + %2\$s', $value1, $value2, $locale);
     * Example 2: translate('%1\$s + %2\$s', array($value1, $value2), $locale);
     *
     * @param string           $messageid
     * @return string  Translated message
     */
    public function translate($messageid = null)
    {
        if (null === $messageid) {
            return $this;
        }

        if (null === ($translate = $this->getTranslator())) {
            return $messageid;
        }

        $options = func_get_args();
        array_shift($options);

        $count   = count($options);
        $locale  = null;
        if ($count > 0) {
            if (Zend_Locale::isLocale($options[$count - 1])) {
                $locale = array_pop($options);
            }
        }
        if ((count($options) == 1) and (is_array($options[0]))) {
            $options = $options[0];
        }
        $message = $translate->translate($messageid, $locale);
        return vsprintf($message, $options);
    }

    /**
     * Sets a translation Adapter for translation
     *
     * @param  Zend_Translate|Zend_Translate_Adapter $translate
     * @return Zend_View_Helper_Translate
     */
    public function setTranslator($translate)
    {
        if ($translate instanceof Zend_Translate_Adapter) {
            $this->_translator = $translate;
        } elseif ($translate instanceof Zend_Translate) {
            $this->_translator = $translate->getAdapter();
        } else {
            #require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception("You must set an instance of Zend_Translate or Zend_Translate_Adapter");
        }
        return $this;
    }

    /**
     * Retrieve translation object
     *
     * If none is currently registered, attempts to pull it from the registry
     * using the key 'Zend_Translate'.
     *
     * @return Zend_Translate_Adapter|null
     */
    public function getTranslator()
    {
        if (null === $this->_translator) {
            #require_once 'Zend/Registry.php';
            if (Zend_Registry::isRegistered('Zend_Translate')) {
                $this->setTranslator(Zend_Registry::get('Zend_Translate'));
            }
        }
        return $this->_translator;
    }

    /**
     * Set's an new locale for all further translations
     *
     * @param  string|Zend_Locale $locale
     * @return Zend_View_Helper_Translate
     */
    public function setLocale($locale = null)
    {
        if (null === ($translate = $this->getTranslator())) {
            #require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception("You must set an instance of Zend_Translate or Zend_Translate_Adapter");
        }
        $translate->setLocale($locale);
        return $this;
    }

    /**
     * Returns the set locale for translations
     *
     * @return string|Zend_Locale
     */
    public function getLocale()
    {
        if (null === ($translate = $this->getTranslator())) {
            #require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception("You must set an instance of Zend_Translate or Zend_Translate_Adapter");
        }
        return $translate->getLocale();
    }
}
