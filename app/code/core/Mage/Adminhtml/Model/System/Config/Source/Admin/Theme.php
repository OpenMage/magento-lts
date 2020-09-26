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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2020 OpenMage project
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Admin system config theme select source
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Aike Kristian Terjung <at@akt-web.de>
 */
class Mage_Adminhtml_Model_System_Config_Source_Admin_Theme
{
    protected $_options;

    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $packages = [];

        $dirIterator = new DirectoryIterator(Mage::getBaseDir('app') . DS . 'design' . DS . 'adminhtml');

        foreach ($dirIterator as $fileinfo) {
            if ($fileinfo->isDir() && !$fileinfo->isDot() && $fileinfo->getFilename()!='base') {
                $packages[$fileinfo->getFilename()] = $fileinfo->getFilename();
            }
        }

        return $packages;
    }

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->getAllOptions();
        }

        $options = $this->_options;

        array_unshift($options, array('value' => 'legacy', 'label' => Mage::helper('adminhtml')->__('legacy theme/mode for backward compatibilty')));

        return $options;
    }
}
