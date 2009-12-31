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
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *  osCommerce import run block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oscommerce_Block_Adminhtml_Import_Run extends Mage_Adminhtml_Block_Abstract
{
    public function getImportModel()
    {
        return Mage::registry('oscommerce_adminhtml_import');
    }

    protected function _toHtml()
    {
        $importModel = $this->getImportModel();

        echo '<html><head>';

        $headBlock = $this->getLayout()->createBlock('page/html_head');
        $headBlock->addJs('prototype/prototype.js');
        echo $headBlock->getCssJsHtml();

        echo '<style type="text/css">
    ul { list-style-type:none; padding:0; margin:0; }
    li { margin-left:0; border:solid #CCC 1px; margin:2px; padding:2px 2px 2px 2px; font:normal 12px sans-serif; }
    img { margin-right:5px; }
    </style>
    <title>'.($importModel->getId() ? $this->htmlEscape($importModel->getName()) : $this->__('No osCommerce profile')).'</title>
</head><body>';
        echo '<ul>';
        echo '<li>';
        if ($importModel->getId()) {
            echo '<img src="'.Mage::getDesign()->getSkinUrl('images/note_msg_icon.gif').'" class="v-middle" style="margin-right:5px"/>';
            echo $this->__("Starting profile execution, please wait...");
            echo '</li>';
            echo '<li style="background-color:#FFD;">';
            echo '<img src="'.Mage::getDesign()->getSkinUrl('images/fam_bullet_error.gif').'" class="v-middle" style="margin-right:5px"/>';
            echo $this->__("Warning: Please don't close window during importing/exporting data");
            echo '</li>';
        } else {
            echo '<img src="'.Mage::getDesign()->getSkinUrl('images/error_msg_icon.gif').'" class="v-middle" style="margin-right:5px"/>';
            echo $this->__("No osCommerce profile loaded...");
        }
        echo '</li>';
        echo '</ul>';

        if ($importModel->getId()) {
            echo '<ul id="profileRows">';
            ob_implicit_flush();
            $showFinished = false;
            $countItems = 0;
            $batchConfig = array(
            'styles' => array(
            'error' => array(
            'icon' => Mage::getDesign()->getSkinUrl('images/error_msg_icon.gif'),
            'bg'   => '#FDD'
            ),
            'message' => array(
            'icon' => Mage::getDesign()->getSkinUrl('images/fam_bullet_success.gif'),
            'bg'   => '#DDF'
            ),
            'loader'  => Mage::getDesign()->getSkinUrl('images/ajax-loader.gif')
            ),
            'template' => '<li style="#{style}" id="#{id}">'
            . '<img src="#{image}" class="v-middle" style="margin-right:5px"/>'
            . '<span class="text">#{text}</span>'
            . '</li>',
            'text'     => $this->__('processed <strong>%s%% %s/%s</strong> records', '#{percent}', '#{updated}', '#{total}'),
            'successText'  => $this->__('Total imported <strong>%s</strong> records (%s)', '#{updated}', '#{totalImported}')
            );


            echo '<li id="liFinished" style="display:none;">';
            echo '<img src="'.Mage::getDesign()->getSkinUrl('images/note_msg_icon.gif').'" class="v-middle" style="margin-right:5px"/>';
            echo $this->__("Finished profile execution.");
            echo '</li>';


            echo "</ul>";
echo '
<script type="text/javascript">
var countOfStartedProfiles = 0;
var countOfUpdated = 0;
var countOfTotalUpdated = 0;
var countOfError = 0;
var importData = [];
var maxRows = 0;
var savedRows = 0;
var totalRecords = {"categories":0,"products":0,"customers":0,"orders":0};
var totalImportedRecords = {"categories":0,"products":0,"customers":0,"orders":0};
var config= '.Mage::helper('core')->jsonEncode($batchConfig).';
</script>
<script type="text/javascript">
function addImportData(data) {
    importData.push(data);
}

function execImportData() {

    if (importData.length == 0) {
        resetAllCount();
        var totalImported = "";
        for (var idx in totalImportedRecords) {
            totalImported += (totalImported?", ":"") + idx.ucFirst() + " <strong>" + totalImportedRecords[idx] + "</strong> '.$this->__('records').'";
        }
        Element.insert($("liFinished"), {before: config.tpl.evaluate({
            style: "background-color:"+config.styles.message.bg,
            image: config.styles.message.icon,
            text: config.tplSccTxt.evaluate({updated:(countOfTotalUpdated-countOfError), totalImported:totalImported}),
            id: "updatedFinish"
        })});
        new Ajax.Request("' . $this->getUrl('*/*/batchFinish', array('id' => $importModel->getId())) .'", {
            parameters: {form_key: \''.Mage::getSingleton('core/session')->getFormKey().'\'},
            onComplete: function() {
                $(\'liFinished\').show();
                Element.toggle(window.opener.$(\'import_processing\'));
                Element.toggle(window.opener.$(\'import_done\'));
            }
        });
    } else {
        sendImportData(importData.shift());
    }
}

function sendImportData(data) {
    if (!config.tpl) {
        config.tpl = new Template(config.template);
        config.tplTxt = new Template(config.text);
        config.tplSccTxt = new Template(config.successText);
    }
    if (!$("updatedRows-"+data["import_type"])) {
        resetAllCount();
        Element.insert($("liFinished"), {before: config.tpl.evaluate({
            style: "background-color: #FFD;",
            image: config.styles.loader,
            text: data["import_type"].ucFirst() + " " +  config.tplTxt.evaluate({updated:countOfUpdated, percent:getPercent(data), total:totalRecords[data["import_type"]]}),
            id: "updatedRows-"+data["import_type"]
        })});
    }
    countOfStartedProfiles++;

    data.form_key = \''.Mage::getSingleton('core/session')->getFormKey().'\'
    new Ajax.Request("'.$this->getUrl('*/*/batchRun').'", {
      method: "post",
      parameters: data,
      onSuccess: function(transport) {

        countOfStartedProfiles --;
        if (transport.responseText.isJSON()) {
            savedRows = parseInt(transport.responseText.evalJSON()["savedRows"]);
            countOfUpdated += savedRows;
            countOfTotalUpdated += savedRows;
            addProfileRow(transport.responseText.evalJSON(),data);
            if (data["is_done"] == true) {
                $("updatedRows-"+data["import_type"]).down("img").src = config.styles.message.icon;
                $("updatedRows-"+data["import_type"]).style.backgroundColor = config.styles.message.bg;
            }
        } else {
            Element.insert($("updatedRows"), {before: config.tpl.evaluate({
                style: "background-color:"+config.styles.error.bg,
                image: config.styles.error.icon,
                text: transport.responseText.escapeHTML(),
                id: "error-" + countOfStartedProfiles
            })});
            countOfError += data["from"].length;
        }
        execImportData();
      },
      onFailure: function() {
        alert("error");
      }

    });
}

function getPercent(data) {
    if (parseInt(totalRecords[data["import_type"]]) == 0)	{
        return 0;
    } else {
        totalImportedRecords[data["import_type"]] = countOfUpdated;
        return Math.ceil((countOfUpdated/totalRecords[data["import_type"]])*1000)/10;
    }
}

function addProfileRow(data, Info) {
    if (data.errors.length > 0) {
        for (var i=0, length=data.errors.length; i<length; i++) {
            Element.insert($("updatedRows-"+Info["import_type"]), {before: config.tpl.evaluate({
                style: "background-color:"+config.styles.error.bg,
                image: config.styles.error.icon,
                text: data.errors[i],
                id: "id-" + (countOfUpdated + i + 1)
            })});
            countOfError ++;
        }
    }
    $("updatedRows-"+Info["import_type"]).down(".text").update(Info["import_type"].ucFirst() + " " + config.tplTxt.evaluate({updated:countOfUpdated, percent:getPercent(Info), total:totalRecords[Info["import_type"]]}));
}

function resetAllCount()
{
    countOfStartedProfiles = 0;
    countOfUpdated = 0;
    countOfError = 0;
}



String.prototype.ucFirst = function () {
    return this.substr(0,1).toUpperCase() + this.substr(1,this.length);
};


</script>
';

//            echo '<ul id="profileRows">';


            if ($totalRecords = $importModel->getTotalRecords()) {

                    $maxRows = $importModel->getResource()->getMaxRows();
                    echo '<script type="text/javascript">maxRows='.$maxRows.';</script>';
                    foreach($totalRecords as $importType => $totalRecord) {
                        echo '<script type="text/javascript">totalRecords["'.$importType.'"]='.$totalRecord.';</script>';
                            $page =  floor($totalRecord/$maxRows) + 1;
                            for ($i = 0; $i < $page; $i++) {
                                $data = array(
                                    'import_id'   => $importModel->getId(),
                                    'import_type' => $importType,
                                    'from'        => ($i > 0 ? $i * $maxRows:$i),
                                    'is_done'     => ($i == $page - 1)?true:false
                                );
                                echo '<script type="text/javascript">addImportData('.Mage::helper('core')->jsonEncode($data).')</script>';
                            }

//                        if ($importType=='categories') {
//                            $data = array(
//                                'import_id'   => $importModel->getId(),
//                                'import_type' => $importType,
//                                'page'        => 'all',
//                                'is_done'     => true
//                            );
//                            echo '<script type="text/javascript">addImportData('.Mage::helper('core')->jsonEncode($data).')</script>';
//
//                        } else {
//                            $page =  floor($totalRecord/$maxRows) + 1;
//                            for ($i = 0; $i < $page; $i++) {
//                                $data = array(
//                                    'import_id'   => $importModel->getId(),
//                                    'import_type' => $importType,
//                                    'from'        => ($i > 0 ? $i * $maxRows:$i),
//                                    'is_done'     => ($i == $page - 1)?true:false
//                                );
//                                echo '<script type="text/javascript">addImportData('.Mage::helper('core')->jsonEncode($data).')</script>';
//                            }
//                        }
                    }
                    echo '<script type="text/javascript">execImportData()</script>';

            }

        }
        echo '</body></html>';
        exit;
    }
}
