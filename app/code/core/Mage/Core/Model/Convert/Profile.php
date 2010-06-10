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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profile
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Convert_Profile extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('core/convert_profile');
    }

    protected function _afterLoad()
    {
        if (is_string($this->getGuiData())) {
            $guiData = unserialize($this->getGuiData());
        } else {
            $guiData = '';
        }
        $this->setGuiData($guiData);

        parent::_afterLoad();
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (is_array($this->getGuiData())) {
            $this->_parseGuiData();
            $this->setGuiData(serialize($this->getGuiData()));
        }
    }

    protected function _afterSave()
    {
        if (is_string($this->getGuiData())) {
            $this->setGuiData(unserialize($this->getGuiData()));
        }

        Mage::getModel('core/convert_history')
            ->setProfileId($this->getId())
            ->setActionCode($this->getOrigData('profile_id') ? 'update' : 'create')
            ->save();

        parent::_afterSave();
    }

    public function run()
    {
        Mage::getModel('core/convert_history')
            ->setProfileId($this->getId())
            ->setActionCode('run')
            ->save();

        $xml = '<convert version="1.0"><profile name="default">'.$this->getActionsXml().'</profile></convert>';
        $profile = Mage::getModel('core/convert')->importXml($xml)->getProfile('default');
        try {
            $profile->run();
        } catch (Exception $e) {

        }
        $this->setExceptions($profile->getExceptions());
        return $this;
    }

    public function _parseGuiData()
    {
        $nl = "\r\n";
        $import = $this->getDirection()==='import';
        $p = $this->getGuiData();

        if ($this->getDataTransfer()==='interactive') {
//            $p['file']['type'] = 'file';
//            $p['file']['filename'] = $p['interactive']['filename'];
//            $p['file']['path'] = 'var/export';

            $interactiveXml = '<action type="dataflow/convert_adapter_http" method="'.($import?'load':'save').'">'.$nl;
            #$interactiveXml .= '    <var name="filename"><![CDATA['.$p['interactive']['filename'].']]></var>'.$nl;
            $interactiveXml .= '</action>';

            $fileXml = '';
        } else {
            $interactiveXml = '';

            $fileXml = '<action type="dataflow/convert_adapter_io" method="'.($import?'load':'save').'">'.$nl;
            $fileXml .= '    <var name="type">'.$p['file']['type'].'</var>'.$nl;
            $fileXml .= '    <var name="path">'.$p['file']['path'].'</var>'.$nl;
            $fileXml .= '    <var name="filename"><![CDATA['.$p['file']['filename'].']]></var>'.$nl;
            if ($p['file']['type']==='ftp') {
                $hostArr = explode(':', $p['file']['host']);
                $fileXml .= '    <var name="host"><![CDATA['.$hostArr[0].']]></var>'.$nl;
                if (isset($hostArr[1])) {
                    $fileXml .= '    <var name="port"><![CDATA['.$hostArr[1].']]></var>'.$nl;
                }
                if (!empty($p['file']['passive'])) {
                    $fileXml .= '    <var name="passive">true</var>'.$nl;
                }
                if (!empty($p['file']['user'])) {
                    $fileXml .= '    <var name="user"><![CDATA['.$p['file']['user'].']]></var>'.$nl;
                }
                if (!empty($p['file']['password'])) {
                    $fileXml .= '    <var name="password"><![CDATA['.$p['file']['password'].']]></var>'.$nl;
                }
            }
            $fileXml .= '</action>'.$nl.$nl;
        }

        switch ($p['parse']['type']) {
            case 'excel_xml':
                $parseFileXml = '<action type="dataflow/convert_parser_xml_excel" method="'.($import?'parse':'unparse').'">'.$nl;
                $parseFileXml .= '    <var name="single_sheet"><![CDATA['.($p['parse']['single_sheet']!==''?$p['parse']['single_sheet']:'_').']]></var>'.$nl;
                break;

            case 'csv':
                $parseFileXml = '<action type="dataflow/convert_parser_csv" method="'.($import?'parse':'unparse').'">'.$nl;
                $parseFileXml .= '    <var name="delimiter"><![CDATA['.$p['parse']['delimiter'].']]></var>'.$nl;
                $parseFileXml .= '    <var name="enclose"><![CDATA['.$p['parse']['enclose'].']]></var>'.$nl;
                break;
        }
        $parseFileXml .= '    <var name="fieldnames">'.$p['parse']['fieldnames'].'</var>'.$nl;
        $parseFileXml .= '</action>'.$nl.$nl;

        $mapXml = '';
        if (isset($p['map']) && is_array($p['map'])) {
            foreach ($p['map'] as $side=>$fields) {
                if (!is_array($fields)) {
                    continue;
                }
                foreach ($fields['db'] as $i=>$k) {
                    if ($k=='' || $k=='0') {
                        unset($p['map'][$side]['db'][$i]);
                        unset($p['map'][$side]['file'][$i]);
                    }
                }
            }
        }
        $mapXml .= '<action type="dataflow/convert_mapper_column" method="map">'.$nl;
        $map = $p['map'][$this->getEntityType()];
        if (sizeof($map['db'])>0) {
            $from = $map[$import?'file':'db'];
            $to = $map[$import?'db':'file'];
            foreach ($from as $i=>$f) {
                $mapXml .= '    <var name="'.$f.'"><![CDATA['.$to[$i].']]></var>'.$nl;
            }
        }
        if ($p['map']['only_specified']) {
            $mapXml .= '    <var name="_only_specified">'.$p['map']['only_specified'].'</var>'.$nl;
        }
        $mapXml .= '</action>'.$nl.$nl;

        $parsers = array(
            'product'=>'catalog/convert_parser_product',
            'customer'=>'customer/convert_parser_customer',
        );

        if ($import) {
            $parseDataXml = '<action type="'.$parsers[$this->getEntityType()].'" method="parse">'.$nl;
            $parseDataXml .= '    <var name="store"><![CDATA['.$this->getStoreId().']]></var>'.$nl;
            $parseDataXml .= '</action>'.$nl.$nl;
        } else {
            $parseDataXml = '<action type="'.$parsers[$this->getEntityType()].'" method="unparse">'.$nl;
            $parseDataXml .= '    <var name="store"><![CDATA['.$this->getStoreId().']]></var>'.$nl;
            $parseDataXml .= '</action>'.$nl.$nl;
        }

        $adapters = array(
            'product'=>'catalog/convert_adapter_product',
            'customer'=>'customer/convert_adapter_customer',
        );

        if ($import) {
            $entityXml = '<action type="'.$adapters[$this->getEntityType()].'" method="save">'.$nl;
            $entityXml .= '    <var name="store"><![CDATA['.$this->getStoreId().']]></var>'.$nl;
            $entityXml .= '</action>'.$nl.$nl;
        } else {
            $entityXml = '<action type="'.$adapters[$this->getEntityType()].'" method="load">'.$nl;
            $entityXml .= '    <var name="store"><![CDATA['.$this->getStoreId().']]></var>'.$nl;
            foreach ($p[$this->getEntityType()]['filter'] as $f=>$v) {
                if (empty($v)) {
                    continue;
                }
                if (is_scalar($v)) {
                    $entityXml .= '    <var name="filter/'.$f.'"><![CDATA['.$v.']]></var>'.$nl;
                } elseif (is_array($v)) {
                    foreach ($v as $a=>$b) {
                        if (empty($b)) {
                            continue;
                        }
                        $entityXml .= '    <var name="filter/'.$f.'/'.$a.'"><![CDATA['.$b.']]></var>'.$nl;
                    }
                }
            }
            $entityXml .= '</action>'.$nl.$nl;
        }

        if ($import) {
            $xml = $interactiveXml.$fileXml.$parseFileXml.$mapXml.$parseDataXml.$entityXml;
        } else {
            $xml = $entityXml.$parseDataXml.$mapXml.$parseFileXml.$fileXml.$interactiveXml;
        }

        $this->setGuiData($p);
        $this->setActionsXml($xml);
/*echo "<pre>".print_r($p,1)."</pre>";
echo "<xmp>".$xml."</xmp>";
die;*/
        return $this;
    }

    public function _parseGuiDataOrig()
    {
        $nl = "\r\n";
        $import = $this->getDirection()==='import';
        $p = $this->getGuiData();

        if ($this->getDataTransfer()==='interactive') {
//            $p['file']['type'] = 'file';
//            $p['file']['filename'] = $p['interactive']['filename'];
//            $p['file']['path'] = 'var/export';

            $interactiveXml = '<action type="varien/convert_adapter_http" method="'.($import?'load':'save').'">'.$nl;
            #$interactiveXml .= '    <var name="filename"><![CDATA['.$p['interactive']['filename'].']]></var>'.$nl;
            $interactiveXml .= '</action>';

            $fileXml = '';
        } else {
            $interactiveXml = '';

            $fileXml = '<action type="varien/convert_adapter_io" method="'.($import?'load':'save').'">'.$nl;
            $fileXml .= '    <var name="type">'.$p['file']['type'].'</var>'.$nl;
            $fileXml .= '    <var name="path">'.$p['file']['path'].'</var>'.$nl;
            $fileXml .= '    <var name="filename"><![CDATA['.$p['file']['filename'].']]></var>'.$nl;
            if ($p['file']['type']==='ftp') {
                $hostArr = explode(':', $p['file']['host']);
                $fileXml .= '    <var name="host"><![CDATA['.$hostArr[0].']]></var>'.$nl;
                if (isset($hostArr[1])) {
                    $fileXml .= '    <var name="port"><![CDATA['.$hostArr[1].']]></var>'.$nl;
                }
                if (!empty($p['file']['passive'])) {
                    $fileXml .= '    <var name="passive">true</var>'.$nl;
                }
                if (!empty($p['file']['user'])) {
                    $fileXml .= '    <var name="user"><![CDATA['.$p['file']['user'].']]></var>'.$nl;
                }
                if (!empty($p['file']['password'])) {
                    $fileXml .= '    <var name="password"><![CDATA['.$p['file']['password'].']]></var>'.$nl;
                }
            }
            $fileXml .= '</action>'.$nl.$nl;
        }

        switch ($p['parse']['type']) {
            case 'excel_xml':
                $parseFileXml = '<action type="varien/convert_parser_xml_excel" method="'.($import?'parse':'unparse').'">'.$nl;
                $parseFileXml .= '    <var name="single_sheet"><![CDATA['.($p['parse']['single_sheet']!==''?$p['parse']['single_sheet']:'_').']]></var>'.$nl;
                break;

            case 'csv':
                $parseFileXml = '<action type="varien/convert_parser_csv" method="'.($import?'parse':'unparse').'">'.$nl;
                $parseFileXml .= '    <var name="delimiter"><![CDATA['.$p['parse']['delimiter'].']]></var>'.$nl;
                $parseFileXml .= '    <var name="enclose"><![CDATA['.$p['parse']['enclose'].']]></var>'.$nl;
                break;
        }
        $parseFileXml .= '    <var name="fieldnames">'.$p['parse']['fieldnames'].'</var>'.$nl;
        $parseFileXml .= '</action>'.$nl.$nl;

        $mapXml = '';
        if (isset($p['map']) && is_array($p['map'])) {
            foreach ($p['map'] as $side=>$fields) {
                if (!is_array($fields)) {
                    continue;
                }
                foreach ($fields['db'] as $i=>$k) {
                    if ($k=='' || $k=='0') {
                        unset($p['map'][$side]['db'][$i]);
                        unset($p['map'][$side]['file'][$i]);
                    }
                }
            }
        }
        $mapXml .= '<action type="varien/convert_mapper_column" method="map">'.$nl;
        $map = $p['map'][$this->getEntityType()];
        if (sizeof($map['db'])>0) {
            $from = $map[$import?'file':'db'];
            $to = $map[$import?'db':'file'];
            foreach ($from as $i=>$f) {
                $mapXml .= '    <var name="'.$f.'"><![CDATA['.$to[$i].']]></var>'.$nl;
            }
        }
        if ($p['map']['only_specified']) {
            $mapXml .= '    <var name="_only_specified">'.$p['map']['only_specified'].'</var>'.$nl;
        }
        $mapXml .= '</action>'.$nl.$nl;

        $parsers = array(
            'product'=>'catalog/convert_parser_product',
            'customer'=>'customer/convert_parser_customer',
        );

        if ($import) {
            $parseDataXml = '<action type="'.$parsers[$this->getEntityType()].'" method="parse">'.$nl;
            $parseDataXml .= '    <var name="store"><![CDATA['.$this->getStoreId().']]></var>'.$nl;
            $parseDataXml .= '</action>'.$nl.$nl;
        } else {
            $parseDataXml = '<action type="'.$parsers[$this->getEntityType()].'" method="unparse">'.$nl;
            $parseDataXml .= '    <var name="store"><![CDATA['.$this->getStoreId().']]></var>'.$nl;
            $parseDataXml .= '</action>'.$nl.$nl;
        }

        $adapters = array(
            'product'=>'catalog/convert_adapter_product',
            'customer'=>'customer/convert_adapter_customer',
        );

        if ($import) {
            $entityXml = '<action type="'.$adapters[$this->getEntityType()].'" method="save">'.$nl;
            $entityXml .= '    <var name="store"><![CDATA['.$this->getStoreId().']]></var>'.$nl;
            $entityXml .= '</action>'.$nl.$nl;
        } else {
            $entityXml = '<action type="'.$adapters[$this->getEntityType()].'" method="load">'.$nl;
            $entityXml .= '    <var name="store"><![CDATA['.$this->getStoreId().']]></var>'.$nl;
            foreach ($p[$this->getEntityType()]['filter'] as $f=>$v) {
                if (empty($v)) {
                    continue;
                }
                if (is_scalar($v)) {
                    $entityXml .= '    <var name="filter/'.$f.'"><![CDATA['.$v.']]></var>'.$nl;
                } elseif (is_array($v)) {
                    foreach ($v as $a=>$b) {
                        if (empty($b)) {
                            continue;
                        }
                        $entityXml .= '    <var name="filter/'.$f.'/'.$a.'"><![CDATA['.$b.']]></var>'.$nl;
                    }
                }
            }
            $entityXml .= '</action>'.$nl.$nl;
        }

        if ($import) {
            $xml = $interactiveXml.$fileXml.$parseFileXml.$mapXml.$parseDataXml.$entityXml;
        } else {
            $xml = $entityXml.$parseDataXml.$mapXml.$parseFileXml.$fileXml.$interactiveXml;
        }

        $this->setGuiData($p);
        $this->setActionsXml($xml);
/*echo "<pre>".print_r($p,1)."</pre>";
echo "<xmp>".$xml."</xmp>";
die;*/
        return $this;
    }
}
