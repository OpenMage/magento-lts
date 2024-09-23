<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

/**
 * @category   Mage
 * @package    Mage_Eav
 *
 * @method false|int getTime()
 * @method $this setTime(false|int $value)
 */
class Mage_Eav_Block_Widget_Date extends Mage_Eav_Block_Widget_Abstract
{
    protected array $_dateInputs = [];

    public function _construct()
    {
        parent::_construct();

        // default template location
        $this->setTemplate('eav/widget/date.phtml');
    }

    /**
     * @return $this
     */
    public function setDate(string $date)
    {
        $this->setTime($date ? strtotime($date) : false);
        $this->setData('date', $date);
        return $this;
    }

    /**
     * @return false|string
     */
    public function getDay()
    {
        return $this->getTime() ? date('d', $this->getTime()) : '';
    }

    /**
     * @return false|string
     */
    public function getMonth()
    {
        return $this->getTime() ? date('m', $this->getTime()) : '';
    }

    /**
     * @return false|string
     */
    public function getYear()
    {
        return $this->getTime() ? date('Y', $this->getTime()) : '';
    }

    /**
     * Returns format which will be applied for date in javascript
     */
    public function getDateFormat(): string
    {
        return Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    /**
     * Add date input html
     */
    public function setDateInput(string $code, string $html): void
    {
        $this->_dateInputs[$code] = $html;
    }

    /**
     * Sort date inputs by dateformat order of current locale
     */
    public function getSortedDateInputs(): string
    {
        $strtr = [
            '%b' => '%1$s',
            '%B' => '%1$s',
            '%m' => '%1$s',
            '%d' => '%2$s',
            '%e' => '%2$s',
            '%Y' => '%3$s',
            '%y' => '%3$s'
        ];

        $dateFormat = preg_replace('/[^\%\w]/', '\\1', $this->getDateFormat());

        return sprintf(
            strtr($dateFormat, $strtr),
            $this->_dateInputs['m'],
            $this->_dateInputs['d'],
            $this->_dateInputs['y']
        );
    }
}
