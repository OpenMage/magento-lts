<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * @package    Mage_Customer
 *
 * @method DateTime getTime()
 * @method $this    setTime(string $value)
 */
class Mage_Customer_Block_Widget_Dob extends Mage_Customer_Block_Widget_Abstract
{
    /**
     * Date inputs
     *
     * @var array
     */
    protected $_dateInputs = [];

    protected function _construct()
    {
        parent::_construct();

        // default template location
        $this->setTemplate('customer/widget/dob.phtml');
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->_getAttribute('dob')->getIsVisible();
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return (bool) $this->_getAttribute('dob')->getIsRequired();
    }

    /**
     * @param  string $date
     * @return $this
     */
    public function setDate($date)
    {
        if ($date) {
            try {
                $dateTime = new DateTime($date);
                $this->setTime($dateTime);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        $this->setData('date', $date);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasTime()
    {
        return ($this->getTime() instanceof DateTime);
    }

    /**
     * @return string
     */
    public function getDay()
    {
        return ($this->hasTime()) ? $this->getTime()->format('d') : '';
    }

    /**
     * @return string
     */
    public function getMonth()
    {
        return ($this->hasTime()) ? $this->getTime()->format('m') : '';
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return ($this->hasTime()) ? $this->getTime()->format('o') : '';
    }

    /**
     * Returns format which will be applied for DOB in javascript
     *
     * @return string
     */
    public function getDateFormat()
    {
        return Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    /**
     * Add date input html
     *
     * @param string $code
     * @param string $html
     */
    public function setDateInput($code, $html)
    {
        $this->_dateInputs[$code] = $html;
    }

    /**
     * Sort date inputs by dateformat order of current locale
     *
     * @return string
     */
    public function getSortedDateInputs()
    {
        $strtr = [
            '%b' => '%1$s',
            '%B' => '%1$s',
            '%m' => '%1$s',
            '%d' => '%2$s',
            '%e' => '%2$s',
            '%Y' => '%3$s',
            '%y' => '%3$s',
        ];

        $dateFormat = preg_replace('/[^\%\w]/', '\\1', $this->getDateFormat());

        return sprintf(
            strtr($dateFormat, $strtr),
            $this->_dateInputs['m'],
            $this->_dateInputs['d'],
            $this->_dateInputs['y'],
        );
    }
}
