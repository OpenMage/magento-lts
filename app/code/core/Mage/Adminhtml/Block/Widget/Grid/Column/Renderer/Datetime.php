<?php
/**
 * Adminhtml grid item renderer datetime
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Date format string
     */
    protected static $_format = null;

    /**
     * Retrieve datetime format
     *
     * @return string
     */
    protected function _getFormat()
    {
        $format = $this->getColumn()->getFormat();
        if (!$format) {
            if (is_null(self::$_format)) {
                try {
                    self::$_format = Mage::app()->getLocale()->getDateTimeFormat(
                        Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
                    );
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
            $format = self::$_format;
        }
        return $format;
    }

    /**
     * Renders grid column
     *
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ($data = $this->_getValue($row)) {
            $format = $this->_getFormat();
            $useTimezone = $this->getColumn()->getUseTimezone() ?? true;
            $locale = $this->getColumn()->getLocale() ?? null;
            try {
                $data = Mage::app()->getLocale()
                    ->date($data, Varien_Date::DATETIME_INTERNAL_FORMAT, $locale, $useTimezone)
                    ->toString($format);
            } catch (Exception $e) {
                $data = Mage::app()->getLocale()
                    ->date($data, Varien_Date::DATETIME_INTERNAL_FORMAT)->toString($format);
            }
            return $data;
        }
        return $this->getColumn()->getDefault();
    }
}
