<?php

class Mage_Adminhtml_Model_Extension_Collection extends Mage_Adminhtml_Model_Extension_Collection_Abstract
{
    protected function _fetchPackages()
    {
        $_files = @glob(Mage::getBaseDir('var') . DS . 'pear' . DS . '{*\.ser,*\.xml}', GLOB_BRACE);
        $_items = array();
        foreach ($_files as $_file) {
            $_file = str_replace(Mage::getBaseDir('var') . DS . 'pear' . DS, '', $_file);
            if ($_file == 'package.xml') {
                continue;
            }
            $_file = preg_replace('/\.ser|\.xml/', '', $_file);
            $_items[] = array(
                'filename' => $_file,
                'filename_id' => $_file
            );
        }

        return $_items;
    }
}