<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml grid item renderer date
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Sales_Grid_Column_Renderer_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Date
{
    /**
     * Retrieve date format
     *
     * @return string
     */
    protected function _getFormat()
    {
        $format = $this->getColumn()->getFormat();
        if (!$format) {
            if (is_null(self::$_format)) {
                try {
                    $localeCode = Mage::app()->getLocale()->getLocaleCode();
                    $localeData = new Zend_Locale_Data();
                    self::$_format = match ($this->getColumn()->getPeriodType()) {
                        'month' => $localeData::getContent($localeCode, 'dateitem', 'yM'),
                        'year' => $localeData::getContent($localeCode, 'dateitem', 'y'),
                        default => Mage::app()->getLocale()->getDateFormat(
                            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
                        ),
                    };
                } catch (Exception) {
                }
            }

            $format = self::$_format;
        }

        return $format;
    }

    /**
     * Renders grid column
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            $dateFormat = match ($this->getColumn()->getPeriodType()) {
                'month' => 'yyyy-MM',
                'year' => 'yyyy',
                default => Varien_Date::DATE_INTERNAL_FORMAT,
            };

            $format = $this->_getFormat();
            try {
                $data = ($this->getColumn()->getGmtoffset())
                    ? Mage::app()->getLocale()->date($data, $dateFormat)->toString($format)
                    : Mage::getSingleton('core/locale')->date($data, $dateFormat, null, false)->toString($format);
            } catch (Exception) {
                $data = ($this->getColumn()->getTimezone())
                    ? Mage::app()->getLocale()->date($data, $dateFormat)->toString($format)
                    : Mage::getSingleton('core/locale')->date($data, $dateFormat, null, false)->toString($format);
            }

            return $data;
        }

        return $this->getColumn()->getDefault();
    }
}
