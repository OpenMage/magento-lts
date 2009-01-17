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
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Abstract installation block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Install_Block_Abstract extends Mage_Core_Block_Template
{
    /**
     * Retrieve installer model
     *
     * @return Mage_Install_Model_Installer
     */
    public function getInstaller()
    {
        return Mage::getSingleton('install/installer');
    }
    
    /**
     * Retrieve wizard model
     *
     * @return Mage_Install_Model_Wizard
     */
    public function getWizard()
    {
        return Mage::getSingleton('install/wizard');
    }
    
    /**
     * Retrieve current installation step
     *
     * @return Varien_Object
     */
    public function getCurrentStep()
    {
        return $this->getWizard()->getStepByRequest($this->getRequest());
    }
}
