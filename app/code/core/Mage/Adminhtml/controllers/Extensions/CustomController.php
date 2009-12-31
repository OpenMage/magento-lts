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

require_once 'Varien/Pear/Package.php';

/**
 * Extension controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Extensions_CustomController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('system/extensions/custom');

        $this->_addContent($this->getLayout()->createBlock('adminhtml/extensions_custom_edit'));

        $this->_addLeft($this->getLayout()->createBlock('adminhtml/extensions_custom_edit_tabs'));

        $this->renderLayout();
    }

    public function resetAction()
    {
        Mage::getSingleton('adminhtml/session')->unsCustomExtensionPackageFormData();
        $this->_redirect('*/*/edit');
    }

    public function loadAction()
    {
        $package = $this->getRequest()->getParam('id');
        if ($package) {
            $session = Mage::getSingleton('adminhtml/session');
            try {
                $data = $this->_loadPackageFile(Mage::getBaseDir('var') . DS . 'pear' . DS . $package);

                $data = array_merge($data, array('file_name' => $package));

                $session->setCustomExtensionPackageFormData($data);
                $session->addSuccess(Mage::helper('adminhtml')->__("Package %s data was successfully loaded", $package));
            }
            catch (Exception $e) {
                $session->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/edit');
    }

    private function _loadPackageFile($filenameNoExtension)
    {
        $data = null;

        // try to load xml-file
        $filename = $filenameNoExtension . '.xml';
        if (file_exists($filename)) {
            $xml = simplexml_load_file($filename);
            $data = Mage::helper('core')->xmlToAssoc($xml);
            if (!empty($data)) {
                return $data;
            }
        }

        // try to load ser-file
        $filename = $filenameNoExtension . '.ser';
        if (!is_readable($filename)) {
            throw new Exception(Mage::helper('adminhtml')->__('Failed to load %1$s.xml or %1$s.ser', basename($filenameNoExtension)));
        }
        $contents = file_get_contents($filename);
        $data = unserialize($contents);
        if (!empty($data)) {
            return $data;
        }

        throw new Exception('Failed to load package data.');
    }

    public function saveAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $p = $this->getRequest()->getPost();

        if (!empty($p['_create'])) {
            $create = true;
            unset($p['_create']);
        }

        if ($p['file_name'] == '') {
            $p['file_name'] = $p['name'];
        }

        $session->setCustomExtensionPackageFormData($p);
        try {
            $ext = Mage::getModel('adminhtml/extension');
            /* @var $ext Mage_Adminhtml_Model_Extension */
            $ext->setData($p);
            $output = $ext->getPear()->getOutput();
            if ($ext->savePackage()) {
                $session->addSuccess('Package data was successfully saved');
            } else {
                $session->addError('There was a problem saving package data');
                $this->_redirect('*/*/edit');
            }
            if (empty($create)) {
                $this->_redirect('*/*/edit');
            } else {
                $this->_forward('create');
            }
        }
        catch(Mage_Core_Exception $e){
            $session->addError($e->getMessage());
            $this->_redirect('*/*');
        }
        catch(Exception $e){
            $session->addError($e->getMessage());
            $this->_redirect('*/*');
        }
    }

    public function createAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        try {
            $p = $this->getRequest()->getPost();
            $session->setCustomExtensionPackageFormData($p);
            $ext = Mage::getModel('adminhtml/extension');
            $ext->setData($p);
            $result = $ext->createPackage();
            $pear = Varien_Pear::getInstance();
            if ($result) {
                $data = $pear->getOutput();
                $session->addSuccess($data[0]['output']);
                $this->_redirect('*/*');
                #$this->_forward('reset');
            } else {
                $session->addError($result->getMessage());
                $this->_redirect('*/*');
            }
        }
        catch(Mage_Core_Exception $e){
            $session->addError($e->getMessage());
            $this->_redirect('*/*');
        }
        catch(Exception $e){
            $session->addError($e->getMessage());
            $this->_redirect('*/*');
        }
    }

    public function releaseAction()
    {
        #Varien_Pear::getInstance()->runHtmlConsole(array('command'=>'list-channels'));
        if (empty($_POST)) {
            $serFiles = @glob(Mage::getBaseDir('var').DS.'pear'.DS.'*.ser');
            if (!$serFiles) {
                return;
            }
            $pkg = new Varien_Object();
            echo '<html><head><style type="text/css">* { font:normal 12px Arial }</style></head>
            <body><form method="post"><table border="1" cellpadding="3" cellspacing="0"><thead>
                    <tr><th>Update/Package</th><th>Version</th><th>State</th></tr>
                </thead><tbody>';
            foreach ($serFiles as $i=>$file) {
                $serialized = file_get_contents($file);
                $pkg->setData(unserialize($serialized));
                $n = $pkg->getName();
                echo '<tr><td><input type="checkbox" name="pkgs['.$i.'][name]" id="pkg_'.$i.'" value="'.$n.'"/>
                        <label for="pkg_'.$i.'">'.$n.'</label>
                        <input type="hidden" name="pkgs['.$i.'][file]" value="'.$file.'"/>
                    </td>
                    <td><input name="pkgs['.$i.'][release_version]" value="'.$pkg->getData('release_version').'"/></td>
                    <td><input name="pkgs['.$i.'][release_stability]" value="'.$pkg->getData('release_stability').'"/></td>
                </tr>';
                #echo "<pre>"; print_r($pkg->getData()); echo "</pre>"; exit;
            }
            echo '</tbody></table><button type="submit">Save and Generate Packages</button></form></body></html>';
        } else {
            @set_time_limit(0);
            ob_implicit_flush();
            foreach ($_POST['pkgs'] as $r) {
                if (empty($r['name'])) {
                    continue;
                }
                echo "<hr/><h4>".$r['name']."</h4>";

                $ext = Mage::getModel('adminhtml/extension');
                $ext->setData(unserialize(file_get_contents($r['file'])));
                $ext->setData('release_version', $r['release_version']);
                $ext->setData('release_stability', $r['release_stability']);
#echo "<pre>"; print_r($ext->getData()); echo "</pre>";
                $result = $ext->savePackage();
                if (!$result) {
                    echo "ERROR while creating the package";
                    continue;
                } else {
                    echo "Package created; ";
                }
                $result = $ext->createPackage();
                $pear = Varien_Pear::getInstance();
                if ($result) {
                    $data = $pear->getOutput();
                    print_r($data[0]['output']);
                } else {
                    echo "ERROR:";
                    print_r($result->getMessage());
                }
            }
            echo '<hr/><a href="'.$_SERVER['REQUEST_URI'].'">Refresh</a>';
        }
        exit;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/extensions/custom');
    }

    /**
     * Grid for loading packages
     *
     */
    public function loadtabAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/extensions_custom_edit_tab_load')->toHtml()
        );
    }

    /**
     * Grid for loading packages
     *
     */
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/extensions_custom_edit_tab_grid')->toHtml()
        );
    }

}
