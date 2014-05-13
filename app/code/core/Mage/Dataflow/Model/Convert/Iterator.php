<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Dataflow_Model_Session_Adapter_Iterator extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    public function walk()
    {
        $sessionId = Mage::registry('current_dataflow_session_id');
        $import = Mage::getResourceModel('dataflow/import');
        $total = $import->loadTotalBySessionId($sessionId);

        $callbacks = array();
        if ($mapperCb = $this->_parseCallback($this->getVar('mapper'), 'mapRow')) {
            $callbacks[] = $mapperCb;
        }
        if ($adapterCb = $this->_parseCallback($this->getVar('adapter'), 'saveRow')) {
            $callbacks[] = $adapterCb;
        }
        $callbacks[] = array($this, 'updateProgress');

        echo $this->_getProgressBarHtml($sessionId, $total['cnt']);

        Mage::getModel('core/resource_iterator')
            ->walk($import->select($sessionId), $callbacks);
    }

    protected function _getProgressBarHtml($sessionId, $totalRows)
    {
        return '
<li>
    <div style="position:relative">
        <div id="progress_bar_'.$sessionId.'" style="position:absolute; background:green; height:2px; width:0; top:-2px; left:-2px; overflow:hidden; "></div>
        <div>
            '.$this->__('Total records: %s', '<strong>'.$totalRows.'</strong>').',
            '.$this->__('Processed records: %s', '<strong><span id="records_processed_'.$sessionId.'">0</span></strong>').',
            '.$this->__('ETA: %s', '<strong><span id="finish_eta_'.$sessionId.'">N/A</span></strong>').',
            '.$this->__('Memory Used: %s', '<strong><span id="memory_'.$sessionId.'">'.memory_get_usage(true).'</span></strong>').'
        </div>
    </div>
</li>
<script type="text/javascript">
function updateProgress(sessionId, idx, time, memory) {
    var total_rows = '.$totalRows.';
    var elapsed_time = time-'.time().';
    var total_time = Math.round(elapsed_time*total_rows/idx);
    var eta = total_time-elapsed_time;
    var eta_str = "";
    var eta_hours = Math.floor(eta/3600);
    var eta_minutes = Math.floor(eta/60)%60;

    if (total_rows==idx) {
        eta_str = "'.$this->__('Done').'";
    } else if (!eta_hours && !eta_minutes) {
        eta_str = "'.$this->__('Less than a minute').'";
    } else {
        if (eta_hours) {
            eta_str += eta_hours+" "+(eta_hours>1 ? "'.$this->__('hours').'" : "'.$this->__('hour').'");
        }
        if (eta_minutes) {
            eta_str += eta_minutes+" "+(eta_minutes>1 ? "'.$this->__('minutes').'" : "'.$this->__('minute').'");
        }
    }

    document.getElementById("records_processed_'.$sessionId.'").innerHTML= idx;
    document.getElementById("finish_eta_'.$sessionId.'").innerHTML = eta_str;
    document.getElementById("memory_'.$sessionId.'").innerHTML = memory;
    document.getElementById("progress_bar_'.$sessionId.'").style.width = (idx/total_rows*100)+"%";
}
</script>';
    }

    public function updateProgress($args)
    {
        $memory = !empty($args['memory']) ? $args['memory'] : '';
        echo '<script type="text/javascript">updateProgress("'.$args['row']['session_id'].'", "'.$args['idx'].'", "'.time().'", "'.$memory.'");</script>';
        echo '<li>'.$memory.'</li>';

        return array();
    }

    protected function _parseCallback($callback, $defaultMethod=null)
    {
        if (!preg_match('#^([a-z0-9_/]+)(::([a-z0-9_]+))?$#i', $callback, $match)) {
            return false;
        }
        if (!($model = Mage::getModel($match[1]))) {
            return false;
        }
        if (!($method = $match[3] ? $match[3] : $defaultMethod)) {
            return false;
        }
        return array($model, $method);
    }
}
