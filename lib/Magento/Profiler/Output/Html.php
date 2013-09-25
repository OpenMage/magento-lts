<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Profiler
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class that represents profiler output in Html format
 */
class Magento_Profiler_Output_Html extends Magento_Profiler_OutputAbstract
{
    /**
     * Display profiling results
     */
    public function display()
    {
        $out  = '<table border="1" cellspacing="0" cellpadding="2">';
        $out .= '<caption>' . $this->_renderCaption() . '</caption>';
        $out .= '<tr>';
        foreach (array_keys($this->_getColumns()) as $columnLabel) {
            $out .= '<th>' . $columnLabel . '</th>';
        }
        $out .= '</tr>';
        foreach ($this->_getTimers() as $timerId) {
            $out .= '<tr>';
            foreach ($this->_getColumns() as $columnId) {
                $out .= '<td title="' . $timerId . '">' . $this->_renderColumnValue($timerId, $columnId) . '</td>';
            }
            $out .= '</tr>';
        }
        $out .= '</table>';

        echo $out;
    }

    /**
     * Render timer id column value
     *
     * @param string $timerId
     * @return string
     */
    protected function _renderTimerId($timerId)
    {
        $nestingSep = preg_quote(Magento_Profiler::NESTING_SEPARATOR, '/');
        return preg_replace('/.+?' . $nestingSep . '/', '&middot;&nbsp;&nbsp;', $timerId);
    }
}
