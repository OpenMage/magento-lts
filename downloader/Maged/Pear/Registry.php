<?php

class Maged_Pear_Registry extends PEAR_Registry
{
    function _initializeDepDB()
    {
        if (!isset($this->_dependencyDB)) {
            static $initializing = false;
            if (!$initializing) {
                $initializing = true;
                if (!$this->_config) { // never used?
                    if (OS_WINDOWS) {
                        $file = 'pear.ini';
                    } else {
                        $file = '.pearrc';
                    }
                    $this->_config = &new PEAR_Config($this->statedir . DIRECTORY_SEPARATOR .
                        $file, '-'); // NO SYSTEM INI FILE
                    $this->_config->setRegistry($this);
                    $this->_config->set('php_dir', $this->install_dir);
                }
                $this->_dependencyDB = &PEAR_DependencyDB::singleton($this->_config);
                if (PEAR::isError($this->_dependencyDB)) {
                    // attempt to recover by removing the dep db
                    if (file_exists($this->_config->get('php_dir', null, 'pear.php.net') .
                        DIRECTORY_SEPARATOR . '.depdb')) {
                        @unlink($this->_config->get('php_dir', null, 'pear.php.net') .
                            DIRECTORY_SEPARATOR . '.depdb');
                    }
                    $this->_dependencyDB = &PEAR_DependencyDB::singleton($this->_config);
                    if (PEAR::isError($this->_dependencyDB)) {
                        echo $this->_dependencyDB->getMessage();
                        echo 'Unrecoverable error';
                        exit(1);
                    }
                }
                $initializing = false;
            }
        }
    }
}
