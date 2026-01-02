<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Captcha
 */

use Carbon\Carbon;

/**
 * Implementation of Zend_Captcha
 *
 * @package    Mage_Captcha
 */
class Mage_Captcha_Model_Zend extends Zend_Captcha_Image implements Mage_Captcha_Model_Interface
{
    /**
     * Key in session for captcha code
     */
    public const SESSION_WORD = 'word';

    /**
     * Min captcha lengths default value
     */
    public const DEFAULT_WORD_LENGTH_FROM = 3;

    /**
     * Max captcha lengths default value
     */
    public const DEFAULT_WORD_LENGTH_TO   = 5;

    /**
     * Helper Instance
     * @var Mage_Captcha_Helper_Data
     */
    protected $_helper = null;

    /**
     * Captcha expire time
     * @var int
     */
    protected $_expiration;

    /**
     * Override default value to prevent a captcha cut off
     * @var int
     * @see Zend_Captcha_Image::$_fsize
     */
    protected $_fsize = 22;

    /**
     * Captcha form id
     * @var string
     */
    protected $_formId;

    /**
     * Generated word
     *
     * @var string
     */
    protected $_word;

    /**
     * Zend captcha constructor
     *
     * @param array $params
     */
    public function __construct($params)
    {
        if (!isset($params['formId'])) {
            throw new Exception('formId is mandatory');
        }

        $this->_formId = $params['formId'];
        $this->setExpiration($this->getTimeout());
    }

    /**
     * Returns key with respect of current form ID
     *
     * @param  string $key
     * @return string
     */
    protected function _getFormIdKey($key)
    {
        return $this->_formId . '_' . $key;
    }

    /**
     * Get Block Name
     *
     * @return string
     */
    public function getBlockName()
    {
        return 'captcha/captcha_zend';
    }

    /**
     * Whether captcha is required to be inserted to this form
     *
     * @param  null|string $login
     * @return bool
     */
    public function isRequired($login = null)
    {
        $nonAuthForms = ['wishlist_sharing', 'sendfriend_send'];

        if ((!in_array($this->_formId, $nonAuthForms) && $this->_isUserAuth())
            || !$this->_isEnabled() || !in_array($this->_formId, $this->_getTargetForms())
        ) {
            return false;
        }

        return ($this->_isShowAlways() || $this->_isOverLimitAttempts($login)
            || $this->getSession()->getData($this->_getFormIdKey('show_captcha'))
        );
    }

    /**
     * Check is overlimit attempts
     *
     * @param  string $login
     * @return bool
     */
    protected function _isOverLimitAttempts($login)
    {
        return ($this->_isOverLimitIpAttempt() || $this->_isOverLimitLoginAttempts($login));
    }

    /**
     * Returns number of allowed attempts for same login
     *
     * @return int
     */
    protected function _getAllowedAttemptsForSameLogin()
    {
        return (int) $this->_getHelper()->getConfigNode('failed_attempts_login');
    }

    /**
     * Returns number of allowed attempts from same IP
     *
     * @return int
     */
    protected function _getAllowedAttemptsFromSameIp()
    {
        return (int) $this->_getHelper()->getConfigNode('failed_attempts_ip');
    }

    /**
     * Check is overlimit saved attempts from one ip
     *
     * @return bool
     */
    protected function _isOverLimitIpAttempt()
    {
        $countAttemptsByIp = Mage::getResourceModel('captcha/log')->countAttemptsByRemoteAddress();
        return $countAttemptsByIp >= $this->_getAllowedAttemptsFromSameIp();
    }

    /**
     * Is Over Limit Login Attempts
     *
     * @param  string $login
     * @return bool
     */
    protected function _isOverLimitLoginAttempts($login)
    {
        if ($login != false) {
            $countAttemptsByLogin = Mage::getResourceModel('captcha/log')->countAttemptsByUserLogin($login);
            return ($countAttemptsByLogin >= $this->_getAllowedAttemptsForSameLogin());
        }

        return false;
    }

    /**
     * Check is user auth
     *
     * @return bool
     */
    protected function _isUserAuth()
    {
        return Mage::app()->getStore()->isAdmin()
            ? Mage::getSingleton('admin/session')->isLoggedIn()
            : Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /**
     * Whether to respect case while checking the answer
     *
     * @return string
     */
    public function isCaseSensitive()
    {
        return (string) $this->_getHelper()->getConfigNode('case_sensitive');
    }

    /**
     * Get font to use when generating captcha
     *
     * @return string
     */
    public function getFont()
    {
        return $this->_getFontPath();
    }

    /**
     * After this time isCorrect() is going to return FALSE even if word was guessed correctly
     *
     * @return int
     */
    public function getTimeout()
    {
        if (!$this->_expiration) {
            /**
             * as "timeout" configuration parameter specifies timeout in minutes - we multiply it on 60 to set
             * expiration in seconds
             */
            $this->_expiration = (int) $this->_getHelper()->getConfigNode('timeout') * 60;
        }

        return $this->_expiration;
    }

    /**
     * Get captcha image directory
     *
     * @return string
     */
    public function getImgDir()
    {
        return $this->_helper->getImgDir();
    }

    /**
     * Get captcha image base URL
     *
     * @return string
     */
    public function getImgUrl()
    {
        return $this->_helper->getImgUrl();
    }

    /**
     * Checks whether captcha was guessed correctly by user
     *
     * @param  string $word
     * @return bool
     */
    public function isCorrect($word)
    {
        $storedWord = $this->getWord();
        $this->_clearWord();

        if (!$word || !$storedWord) {
            return false;
        }

        if (!$this->isCaseSensitive()) {
            $storedWord = strtolower($storedWord);
            $word = strtolower($word);
        }

        return $word == $storedWord;
    }

    /**
     * Returns session instance
     *
     * @return Mage_Customer_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Return full URL to captcha image
     *
     * @return string
     */
    public function getImgSrc()
    {
        return $this->getImgUrl() . $this->getId() . $this->getSuffix();
    }

    /**
     * log Attempt
     *
     * @param  string $login
     * @return $this
     */
    public function logAttempt($login)
    {
        if ($this->_isEnabled() && in_array($this->_formId, $this->_getTargetForms())) {
            Mage::getResourceModel('captcha/log')->logAttempt($login);
            if ($this->_isOverLimitLoginAttempts($login)) {
                $this->getSession()->setData($this->_getFormIdKey('show_captcha'), 1);
            }
        }

        return $this;
    }

    /**
     * Returns path for the font file, chosen to generate captcha
     *
     * @return string
     */
    protected function _getFontPath()
    {
        $font = (string) $this->_getHelper()->getConfigNode('font');
        $fonts = $this->_getHelper()->getFonts();

        if (isset($fonts[$font])) {
            $fontPath = $fonts[$font]['path'];
        } else {
            $fontData = array_shift($fonts);
            $fontPath = $fontData['path'];
        }

        return $fontPath;
    }

    /**
     * Returns captcha helper
     *
     * @return Mage_Captcha_Helper_Data
     */
    protected function _getHelper()
    {
        if (is_null($this->_helper)) {
            $this->_helper = Mage::helper('captcha');
        }

        return $this->_helper;
    }

    /**
     * Generate word used for captcha render
     *
     * @return string
     */
    protected function _generateWord()
    {
        $word = '';
        $symbols = $this->_getSymbols();
        $wordLen = $this->_getWordLen();
        for ($i = 0; $i < $wordLen; $i++) {
            $word .= $symbols[array_rand($symbols)];
        }

        return $word;
    }

    /**
     * Get symbols array to use for word generation
     *
     * @return array
     */
    protected function _getSymbols()
    {
        return str_split((string) $this->_getHelper()->getConfigNode('symbols'));
    }

    /**
     * Returns length for generating captcha word. This value may be dynamic.
     *
     * @return int
     */
    protected function _getWordLen()
    {
        $from = 0;
        $to = 0;
        $length = (string) $this->_getHelper()->getConfigNode('length');
        if (!is_numeric($length)) {
            if (preg_match('/(\d+)-(\d+)/', $length, $matches)) {
                $from = (int) $matches[1];
                $to = (int) $matches[2];
            }
        } else {
            $from = (int) $length;
            $to = (int) $length;
        }

        if (($to < $from) || ($from < 1) || ($to < 1)) {
            $from = self::DEFAULT_WORD_LENGTH_FROM;
            $to = self::DEFAULT_WORD_LENGTH_TO;
        }

        return mt_rand($from, $to);
    }

    /**
     * Whether to show captcha for this form every time
     *
     * @return bool
     */
    protected function _isShowAlways()
    {
        // setting the allowed attempts to 0 is like setting mode to always
        if ($this->_getAllowedAttemptsForSameLogin() == 0 || $this->_getAllowedAttemptsFromSameIp() == 0) {
            return true;
        }

        if ((string) $this->_getHelper()->getConfigNode('mode') == Mage_Captcha_Helper_Data::MODE_ALWAYS) {
            return true;
        }

        $alwaysFor = $this->_getHelper()->getConfigNode('always_for');
        foreach ($alwaysFor as $nodeFormId => $isAlwaysFor) {
            if ($isAlwaysFor && $this->_formId == $nodeFormId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether captcha is enabled at this area
     *
     * @return string
     */
    protected function _isEnabled()
    {
        return (string) $this->_getHelper()->getConfigNode('enable');
    }

    /**
     * Retrieve list of forms where captcha must be shown
     *
     * For frontend this list is based on current website
     *
     * @return array
     */
    protected function _getTargetForms()
    {
        $formsString = (string) $this->_getHelper()->getConfigNode('forms');
        return explode(',', $formsString);
    }

    /**
     * Get captcha word
     *
     * @return null|string
     */
    public function getWord()
    {
        $sessionData = $this->getSession()->getData($this->_getFormIdKey(self::SESSION_WORD));
        if (!is_array($sessionData)) {
            return null;
        }

        return Carbon::now()->getTimestamp() < $sessionData['expires'] ? $sessionData['data'] : null;
    }

    /**
     * Set captcha word
     *
     * @param  string            $word
     * @return Zend_Captcha_Word
     */
    protected function _setWord($word)
    {
        $this->getSession()->setData(
            $this->_getFormIdKey(self::SESSION_WORD),
            ['data' => $word, 'expires' => Carbon::now()->getTimestamp() + $this->getTimeout()],
        );
        $this->_word = $word;
        return $this;
    }

    /**
     * Set captcha word
     *
     * @return $this
     */
    protected function _clearWord()
    {
        $this->getSession()->unsetData($this->_getFormIdKey(self::SESSION_WORD));
        $this->_word = '';
        return $this;
    }

    /**
     * Override function to generate less curly captcha that will not cut off
     *
     * @return int
     * @see Zend_Captcha_Image::_randomSize()
     */
    protected function _randomSize()
    {
        return mt_rand(280, 300) / 100;
    }

    /**
     * Overlap of the parent method
     *
     * Now deleting old captcha images make crontab script
     * @see Mage_Captcha_Model_Observer::deleteExpiredImages
     */
    protected function _gc()
    {
        //do nothing
    }
}
