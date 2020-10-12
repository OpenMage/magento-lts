<?php

/* @var $this Mage_Core_Model_Resource_Setup */
$this->startSetup();

$setup = new Mage_Core_Model_Config();

//Update from Vanilla M1
//without custom theme -> OpenMage Theme
//with customised theme -> Legacy Mode with Info on first Login
//Update from OpenMage 19.4.7 with OpenMage theme V1
//with activated legacy mode -> Legacy Mode with Info on first Login
//with activated OpenMage theme -> OpenMage Theme
//Depending on the upgrade strategy we have to expect these folder structures in app/design/adminhtml/
//
//empty default folder
//default folder with modifications left
//default folder with complete old content from old M1 versions
//custom theme inside default folder
// 

$notice = [
    'severity'=>4,
    'title'=>'OpenMage admin theme legacy mode',
    'url' => 'https://www.openmage.org/admintheme',
    'description'=>'OpenMages\'s new admin theme is disabled because it is not clear if you modified the backend theme. Find more info on how to enable it...'];

// Check if a package value definition is present - just for shure - and don't change it
if (!Mage::getConfig()->getNode('stores/admin/admin/theme/package')) {
// Updating from OpenMage 19.4.7?
    if (Mage::getConfig()->getNode('stores/admin/admin/design/use_legacy_theme')!==false){
        if ((bool)Mage::getConfig()->getNode('stores/admin/admin/design/use_legacy_theme')){
            $setup->saveConfig('admin/theme/package', 'legacy', 'default', 0);
            $setup->deleteConfig('admin/design/use_legacy_theme','default',0);
            $this->getConnection()->insert($this->getTable('adminnotification/inbox'),$notice);
        } else {
            $setup->saveConfig('admin/theme/package', 'openmage', 'default', 0);
            $setup->deleteConfig('admin/design/use_legacy_theme','default',0);
        }
    } else {
        // Updating from M1

        // Check if stores/admin/design/theme/package is modified by custom module
        $legacyPackageOk = false;
        $adminHtmlOk = false;
        $legacyPackage = (string)Mage::getConfig()->getNode('stores/admin/design/theme/package');
        if ($legacyPackage == 'adminhtml') {
            // package setting is not modified
             $legacyPackageOk = true;
        }

        // Check if app/design/adminhtml/ folders are in the expected condition
        $dirIterator = new DirectoryIterator(Mage::getBaseDir('app') . DS . 'design' . DS . 'adminhtml');

        foreach ($dirIterator as $fileinfo) {
            if ($fileinfo->isDir() && !$fileinfo->isDot()) {
                $packages[$fileinfo->getFilename()] = $fileinfo->getFilename();
            }
        }

        if (isset($packages['base']) && isset($packages['openmage'])){
            if (count($packages)==2) {
                // app/design/adminhtml folder has new layout - looks good so far
                $adminHtmlOk = true;
            }
        }

        if ($legacyPackageOk && $adminHtmlOk) {
            $setup->saveConfig('admin/theme/package', 'openmage', 'default', 0);
        } else {
            $setup->saveConfig('admin/theme/package', 'legacy', 'default', 0);
            $this->getConnection()->insert($this->getTable('adminnotification/inbox'),$notice);
        }
    }
}
$this->endSetup();
