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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Install state block
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Block_State extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        $this->setTemplate('install/state.phtml');
        $this->assign('steps', Mage::getSingleton('install/wizard')->getSteps());
    }
    
    /**
     * Get previous downloader steps
     * 
     * @return array
     */
    public function getDownloaderSteps()
    {
        if ($this->isDownloaderInstall()) {
            $steps = array(
                Mage::helper('install')->__('Welcome'),
                Mage::helper('install')->__('Validation'),
                Mage::helper('install')->__('Magento Connect Manager Deployment'),
            );
            return $steps; 
        } else {
            return array();
        }
    }

    /**
     * Checks for Magento Connect Manager installation method
     * 
     * @return bool
     */
    public function isDownloaderInstall() 
    {
        $session = Mage::app()->getCookie()->get('magento_downloader_session');
        return $session ? true : false;
    }
}
