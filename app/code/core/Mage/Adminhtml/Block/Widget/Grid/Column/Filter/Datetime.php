<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Date grid column filter
 *
 * @package    Mage_Adminhtml
 * @todo       date format
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Datetime extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Date
{
    //full day is 86400, we need 23 hours:59 minutes:59 seconds = 86399
    public const END_OF_DAY_IN_SECONDS = 86399;

    public function getValue($index = null)
    {
        if ($index) {
            if ($data = $this->getData('value', 'orig_' . $index)) {
                return $data;//date('Y-m-d', strtotime($data));
            }

            return null;
        }

        $value = $this->getData('value');
        if (is_array($value)) {
            $value['datetime'] = true;
        }

        if (!empty($value['to']) && !$this->getColumn()->getFilterTime()) {
            $datetimeTo = $value['to'];

            //calculate end date considering timezone specification
            $datetimeTo->setTimezone(
                Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE),
            );
            $datetimeTo->addDay(1)->subSecond(1);
            $datetimeTo->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
        }

        return $value;
    }

    /**
     * Convert given date to default (UTC) timezone
     *
     * @param  string         $date
     * @param  string         $locale
     * @return null|Zend_Date
     */
    protected function _convertDate($date, $locale)
    {
        if ($this->getColumn()->getFilterTime()) {
            try {
                $dateObj = $this->getLocale()->date(null, null, $locale, false);

                //set default timezone for store (admin)
                $dateObj->setTimezone(
                    Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE),
                );

                //set date with applying timezone of store
                $dateObj->set(
                    $date,
                    $this->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                    $locale,
                );

                //convert store date to default date in UTC timezone without DST
                $dateObj->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);

                return $dateObj;
            } catch (Exception) {
                return null;
            }
        }

        return parent::_convertDate($date, $locale);
    }

    /**
     * Render filter html
     *
     * @return string
     */
    public function getHtml()
    {
        $fromLabel = Mage::helper('adminhtml')->__('From');
        $toLabel = Mage::helper('adminhtml')->__('To');

        $htmlId = $this->_getHtmlId() . microtime(true);
        $format = $this->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        if ($this->getColumn()->getFilterTime()) {
            $format .= ' ' . $this->getLocale()->getTimeStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        }

        $html = '<div class="range"><div class="range-line date">'
            . '<span class="label">' . $fromLabel . '</span>'
            . '<input type="text" name="' . $this->_getHtmlName() . '[from]" id="' . $htmlId . '_from"'
                . ' placeholder="' . $fromLabel . '"'
                . ' value="' . $this->getEscapedValue('from') . '" class="input-text no-changes"/>'
            . '<img src="' . Mage::getDesign()->getSkinUrl('images/grid-cal.gif') . '" alt="" class="v-middle"'
                . ' id="' . $htmlId . '_from_trig"'
                . ' title="' . $this->escapeHtml(Mage::helper('adminhtml')->__('Date selector')) . '"/>'
            . '</div>';
        $html .= '<div class="range-line date">'
            . '<span class="label">' . $toLabel . '</span>'
            . '<input type="text" name="' . $this->_getHtmlName() . '[to]" id="' . $htmlId . '_to"'
                . ' placeholder="' . $toLabel . '"'
                . ' value="' . $this->getEscapedValue('to') . '" class="input-text no-changes"/>'
            . '<img src="' . Mage::getDesign()->getSkinUrl('images/grid-cal.gif') . '" alt="" class="v-middle"'
                . ' id="' . $htmlId . '_to_trig"'
                . ' title="' . $this->escapeHtml(Mage::helper('adminhtml')->__('Date selector')) . '"/>'
            . '</div></div>';
        $html .= '<input type="hidden" name="' . $this->_getHtmlName() . '[locale]"'
            . ' value="' . $this->getLocale()->getLocaleCode() . '"/>';
        return $html . ('<script type="text/javascript">
            Calendar.setup({
                inputField : "' . $htmlId . '_from",
                ifFormat : "' . $format . '",
                button : "' . $htmlId . '_from_trig",
                showsTime: ' . ($this->getColumn()->getFilterTime() ? 'true' : 'false') . ',
                align : "Bl",
                singleClick : true
            });
            Calendar.setup({
                inputField : "' . $htmlId . '_to",
                ifFormat : "' . $format . '",
                button : "' . $htmlId . '_to_trig",
                showsTime: ' . ($this->getColumn()->getFilterTime() ? 'true' : 'false') . ',
                align : "Bl",
                singleClick : true
            });
        </script>');
    }

    /**
     * Return escaped value for calendar
     *
     * @param  string $index
     * @return string
     */
    public function getEscapedValue($index = null)
    {
        if ($this->getColumn()->getFilterTime()) {
            $value = $this->getValue($index);
            if ($value instanceof Zend_Date) {
                return $value->toString(
                    $this->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                );
            }

            return $this->escapeHtml($value);
        }

        return $this->escapeHtml(parent::getEscapedValue($index));
    }
}
