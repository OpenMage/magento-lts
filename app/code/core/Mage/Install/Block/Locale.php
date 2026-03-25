<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Install
 */

/**
 * Install localization block
 *
 * @package    Mage_Install
 */
class Mage_Install_Block_Locale extends Mage_Install_Block_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('install/locale.phtml');
    }

    /**
     * Retrieve locale object
     *
     * @return Zend_Locale
     */
    public function getLocale()
    {
        $locale = $this->getData('locale');
        if (is_null($locale)) {
            $locale = Mage::app()->getLocale()->getLocale();
            $this->setData('locale', $locale);
        }

        return $locale;
    }

    /**
     * Retrieve locale data post url
     *
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getCurrentStep()->getNextUrl();
        //return $this->getUrl('*/*/localePost');
    }

    /**
     * Retrieve locale change url
     *
     * @return string
     */
    public function getChangeUrl()
    {
        return $this->getUrl('*/*/localeChange');
    }

    /**
     * Retrieve locale dropdown HTML
     *
     * @return string
     */
    public function getLocaleSelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('config[locale]')
            ->setId('locale')
            ->setTitle(Mage::helper('install')->__('Locale'))
            ->setClass('required-entry')
            ->setValue($this->getLocale()->__toString())
            ->setOptions(Mage::app()->getLocale()->getTranslatedOptionLocales())
            ->getHtml();
    }

    /**
     * Retrieve timezone dropdown HTML
     *
     * @return string
     */
    public function getTimezoneSelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('config[timezone]')
            ->setId('timezone')
            ->setTitle(Mage::helper('install')->__('Time Zone'))
            ->setClass('required-entry')
            ->setValue($this->getTimezone())
            ->setOptions(Mage::app()->getLocale()->getOptionTimezones())
            ->getHtml();
    }

    /**
     * Retrieve timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        $timezone = Mage::getSingleton('install/session')->getTimezone()
            ? Mage::getSingleton('install/session')->getTimezone()
            : Mage::app()->getLocale()->getTimezone();
        if ($timezone == Mage_Core_Model_Locale::DEFAULT_TIMEZONE) {
            return 'America/Los_Angeles';
        }

        return $timezone;
    }

    /**
     * Retrieve currency dropdown html
     *
     * @return string
     */
    public function getCurrencySelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('config[currency]')
            ->setId('currency')
            ->setTitle(Mage::helper('install')->__('Default Currency'))
            ->setClass('required-entry')
            ->setValue($this->getCurrency())
            ->setOptions(Mage::app()->getLocale()->getOptionCurrencies())
            ->getHtml();
    }

    /**
     * Retrieve currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return Mage::getSingleton('install/session')->getCurrency()
            ? Mage::getSingleton('install/session')->getCurrency()
            : Mage::app()->getLocale()->getCurrency();
    }

    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = new Varien_Object();
            $this->setData('form_data', $data);
        }

        return $data;
    }
}
