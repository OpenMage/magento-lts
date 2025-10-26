<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Convert profile edit tab
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_Run extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('system/convert/profile/run.phtml');
    }

    public function getRunButtonHtml()
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
            ->setClass('save')->setLabel($this->__('Run Profile in Popup'))
            ->setOnClick('runProfile(true)')
            ->toHtml();
    }

    public function getProfileId()
    {
        return Mage::registry('current_convert_profile')->getId();
    }

    public function getImportedFiles()
    {
        $files = [];
        $path = Mage::app()->getConfig()->getTempVarDir() . '/import';
        if (!is_readable($path)) {
            return $files;
        }

        $dir = dir($path);
        while (($entry = $dir->read()) !== false) {
            if ($entry != '.'
                && $entry != '..'
                && strtolower(substr($entry, strrpos($entry, '.') + 1)) == $this->getParseType()
            ) {
                $files[] = $entry;
            }
        }

        sort($files);
        $dir->close();
        return $files;
    }

    public function getParseType()
    {
        $data = Mage::registry('current_convert_profile')->getGuiData();
        if ($data) {
            return ($data['parse']['type'] == 'excel_xml') ? 'xml' : $data['parse']['type'];
        }
    }
}
