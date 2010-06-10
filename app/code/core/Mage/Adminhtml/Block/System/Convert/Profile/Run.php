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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profiles run block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Run extends Mage_Adminhtml_Block_Abstract
{
    public function getProfile()
    {
        return Mage::registry('current_convert_profile');
    }

    protected function _toHtml()
    {
        $profile = $this->getProfile();

        echo '<html><head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
        echo '<script type="text/javascript">var FORM_KEY = "'.Mage::getSingleton('core/session')->getFormKey().'";</script>';

        $headBlock = $this->getLayout()->createBlock('page/html_head');
        $headBlock->addJs('prototype/prototype.js');
        $headBlock->addJs('mage/adminhtml/loader.js');
        echo $headBlock->getCssJsHtml();

        echo '<style type="text/css">
    ul { list-style-type:none; padding:0; margin:0; }
    li { margin-left:0; border:1px solid #ccc; margin:2px; padding:2px 2px 2px 2px; font:normal 12px sans-serif; }
    img { margin-right:5px; }
    </style>
    <title>'.($profile->getId() ? $this->htmlEscape($profile->getName()) : $this->__('No Profile')).'</title>
</head><body>';
        echo '<ul>';
        echo '<li>';
        if ($profile->getId()) {
            echo '<img src="'.Mage::getDesign()->getSkinUrl('images/note_msg_icon.gif').'" class="v-middle" style="margin-right:5px"/>';
            echo $this->__("Starting profile execution, please wait...");
            echo '</li>';
            echo '<li style="background-color:#FFD;">';
            echo '<img src="'.Mage::getDesign()->getSkinUrl('images/fam_bullet_error.gif').'" class="v-middle" style="margin-right:5px"/>';
            echo $this->__("Warning: Please do not close the window during importing/exporting data");
        } else {
            echo '<img src="'.Mage::getDesign()->getSkinUrl('images/error_msg_icon.gif').'" class="v-middle" style="margin-right:5px"/>';
            echo $this->__("No profile loaded...");
        }
        echo '</li>';
        echo '</ul>';

        if ($profile->getId()) {

            echo '<ul id="profileRows">';

            ob_implicit_flush();
            $profile->run();
            foreach ($profile->getExceptions() as $e) {
                switch ($e->getLevel()) {
                    case Varien_Convert_Exception::FATAL:
                        $img = 'error_msg_icon.gif';
                        $liStyle = 'background-color:#FBB; ';
                        break;
                    case Varien_Convert_Exception::ERROR:
                        $img = 'error_msg_icon.gif';
                        $liStyle = 'background-color:#FDD; ';
                        break;
                    case Varien_Convert_Exception::WARNING:
                        $img = 'fam_bullet_error.gif';
                        $liStyle = 'background-color:#FFD; ';
                        break;
                    case Varien_Convert_Exception::NOTICE:
                        $img = 'fam_bullet_success.gif';
                        $liStyle = 'background-color:#DDF; ';
                        break;
                }
                echo '<li style="'.$liStyle.'">';
                echo '<img src="'.Mage::getDesign()->getSkinUrl('images/'.$img).'" class="v-middle"/>';
                echo $e->getMessage();
                if ($e->getPosition()) {
                    echo " <small>(".$e->getPosition().")</small>";
                }
                echo "</li>";
            }

            if($profile->getEntityType() == 'product' && $profile->getDirection() == 'import') {
                echo '<li id="liBeforeFinish" style="background-color:#FFD; display:none;">';
                echo '<img src="'.Mage::getDesign()->getSkinUrl('images/fam_bullet_error.gif').'" class="v-middle" style="margin-right:5px"/>';
                echo $this->__("Please wait while the indexes are being refreshed.");
                echo '<img id="before-finish-wait-img" src="'.Mage::getDesign()->getSkinUrl('images/rule-ajax-loader.gif').'" class="v-middle" style="margin-right:5px"/>';
                echo '</li>';
            }

            echo '<li id="liFinished" style="display:none;">';
            echo '<img src="'.Mage::getDesign()->getSkinUrl('images/note_msg_icon.gif').'" class="v-middle" style="margin-right:5px"/>';
            echo $this->__("Finished profile execution.");
            echo '</li>';
            echo "</ul>";

            $showFinished = true;
            $batchModel = Mage::getSingleton('dataflow/batch');
            /* @var $batchModel Mage_Dataflow_Model_Batch */
            if ($batchModel->getId()) {
                if ($batchModel->getAdapter()) {
                    $numberOfRecords = $profile->getData('gui_data/import/number_of_records');
                    if (!$numberOfRecords) {
                        $batchParams = $batchModel->getParams();
                        $numberOfRecords = isset($batchParams['number_of_records']) ? $batchParams['number_of_records'] : 1;
                    }

                    $showFinished = false;
                    $batchImportModel = $batchModel->getBatchImportModel();
                    $importIds = $batchImportModel->getIdCollection();
                    $countItems = count($importIds);

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
                                    . '<img id="#{id}_img" src="#{image}" class="v-middle" style="margin-right:5px"/>'
                                    . '<span id="#{id}_status" class="text">#{text}</span>'
                                    . '</li>',
                        'text'     => $this->__('Processed <strong>%s%% %s/%d</strong> records', '#{percent}', '#{updated}', $countItems),
                        'successText'  => $this->__('Imported <strong>%s</strong> records', '#{updated}')
                    );
echo '
<script type="text/javascript">
var countOfStartedProfiles = 0;
var countOfUpdated = 0;
var countOfError = 0;
var importData = [];
var totalRecords = ' . $countItems . ';
var config= '.Mage::helper('core')->jsonEncode($batchConfig).';
</script>
<script type="text/javascript">
function addImportData(data) {
    importData.push(data);
}

function execImportData() {
    if (importData.length == 0) {
        $("updatedRows_img").src = config.styles.message.icon;
        $("updatedRows").style.backgroundColor = config.styles.message.bg;
        Element.insert($("liFinished"), {before: config.tpl.evaluate({
            style: "background-color:"+config.styles.message.bg,
            image: config.styles.message.icon,
            text: config.tplSccTxt.evaluate({updated:(countOfUpdated-countOfError)}),
            id: "updatedFinish"
        })});

        if ($("liBeforeFinish")) {
            Element.insert($("liFinished"), {before: $("liBeforeFinish")});
            $("liBeforeFinish").show();
        }

        new Ajax.Request("' . $this->getUrl('*/*/batchFinish', array('id' => $batchModel->getId())) .'", {
            method: "post",
            parameters: {form_key: FORM_KEY},
            onComplete: function(transport) {
                if (transport.responseText.isJSON()) {
                    var response = transport.responseText.evalJSON();
                    if (response.error) {
                        Element.insert($("liFinished"), {before: config.tpl.evaluate({
                            style: "background-color:"+config.styles.error.bg,
                            image: config.styles.error.icon,
                            text: response.error.escapeHTML(),
                            id: "error-finish"
                        })});
                    }
                }

                if ($("before-finish-wait-img")) {
                    $("before-finish-wait-img").hide();
                }

                $(\'liFinished\').show();
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
    if (!$("updatedRows")) {
        Element.insert($("liFinished"), {before: config.tpl.evaluate({
            style: "background-color: #FFD;",
            image: config.styles.loader,
            text: config.tplTxt.evaluate({updated:countOfUpdated, percent:getPercent()}),
            id: "updatedRows"
        })});
    }
    countOfStartedProfiles++;
    if (!data.form_key) {
        data.form_key = FORM_KEY;
    }

    new Ajax.Request("'.$this->getUrl('*/*/batchRun').'", {
      method: "post",
      parameters: data,
      onSuccess: function(transport) {
        countOfStartedProfiles --;
        countOfUpdated += data["rows[]"].length;
        if (transport.responseText.isJSON()) {
            addProfileRow(transport.responseText.evalJSON());
        } else {
            Element.insert($("updatedRows"), {before: config.tpl.evaluate({
                style: "background-color:"+config.styles.error.bg,
                image: config.styles.error.icon,
                text: transport.responseText.escapeHTML(),
                id: "error-" + countOfStartedProfiles
            })});
            countOfError += data["rows[]"].length;
        }
        execImportData();
      }
    });
}

function getPercent() {
    return Math.ceil((countOfUpdated/totalRecords)*1000)/10;
}

function addProfileRow(data) {
    if (data.errors.length > 0) {
        for (var i=0, length=data.errors.length; i<length; i++) {
            Element.insert($("updatedRows"), {before: config.tpl.evaluate({
                style: "background-color:"+config.styles.error.bg,
                image: config.styles.error.icon,
                text: data.errors[i],
                id: "id-" + (countOfUpdated + i + 1)
            })});
            countOfError ++;
        }
    }
    $("updatedRows_status").update(config.tplTxt.evaluate({updated:countOfUpdated, percent:getPercent()}));
}
</script>
';


                    $jsonIds = array_chunk($importIds, $numberOfRecords);
                    foreach ($jsonIds as $part => $ids) {
                        $data = array(
                            'batch_id'   => $batchModel->getId(),
                            'rows[]'     => $ids
                        );
                        echo '<script type="text/javascript">addImportData('.Mage::helper('core')->jsonEncode($data).')</script>';
                    }
                    echo '<script type="text/javascript">execImportData()</script>';
                    //print $this->getUrl('*/*/batchFinish', array('id' => $batchModel->getId()));
                }
                else {
                    $batchModel->delete();
                }
            }

            if ($showFinished) {
                echo "<script type=\"text/javascript\">$('liFinished').show();</script>";
            }
        }
        /*
        echo '<li>';
        echo '<img src="'.Mage::getDesign()->getSkinUrl('images/note_msg_icon.gif').'" class="v-middle" style="margin-right:5px"/>';
        echo $this->__("Finished profile execution.");
        echo '</li>';
        echo "</ul>";
        */
        echo '</body></html>';
    }
}
