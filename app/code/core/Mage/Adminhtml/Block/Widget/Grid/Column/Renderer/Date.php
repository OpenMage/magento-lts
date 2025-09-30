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
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_defaultWidth = 160;
    /**
     * Date format string
     */
    protected static $_format = null;

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
                    self::$_format = Mage::app()->getLocale()->getDateFormat(
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
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            $format = $this->_getFormat();
            try {
                if ($this->getColumn()->getGmtoffset()) {
                    $data = Mage::app()->getLocale()
                        ->date($data, Varien_Date::DATETIME_INTERNAL_FORMAT)->toString($format);
                } else {
                    $data = Mage::getSingleton('core/locale')
                        ->date($data, Zend_Date::ISO_8601, null, false)->toString($format);
                }
            } catch (Exception $e) {
                if ($this->getColumn()->getTimezone()) {
                    $data = Mage::app()->getLocale()
                        ->date($data, Varien_Date::DATETIME_INTERNAL_FORMAT)->toString($format);
                } else {
                    $data = Mage::getSingleton('core/locale')->date($data, null, null, false)->toString($format);
                }
            }
            return $data;
        }
        return $this->getColumn()->getDefault();
    }
}
