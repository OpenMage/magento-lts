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
class Mage_Adminhtml_Extensions_ConsoleController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('system/extensions/console');

        $this->_addContent($this->getLayout()->createBlock('adminhtml/extensions_console_edit'));

        $this->renderLayout();
    }

    public function outputAction()
    {
        $pear = Varien_Pear::getInstance();
        $input = $this->getRequest()->getParam('argv');
        $argv = preg_split('#\s+#', $input);
        $command = false;
        $options = array();
        $params = array();

        foreach ($argv as $arg) {
            if ($arg[0]==='-') {
                $opt = '';
                if ($arg[1]==='-') {
                    $opt = substr($arg, 2);
                }
                if ($opt) {
                    $options[$opt] = 1;
                }
            } elseif (empty($command)) {
                $command = $arg;
            } else {
                $params[] = $arg;
            }
        }

        $run = new Varien_Object();
        if ($command) {
            $run->setComment(Mage::helper('adminhtml')->__('Running:').' "'.$input.'"'."\r\n\r\n");
            $run->setCommand($command);
            $run->setOptions($options);
            $run->setParams($params);
        } else {
            $run->setComment(Mage::helper('adminhtml')->__('Invalid input:').' "'.$input.'"'."\r\n\r\n");
        }
        $pear->runHtmlConsole($run);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/extensions');
    }
}
