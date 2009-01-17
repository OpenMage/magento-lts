<?php

/**
 * base class
 */
require_once 'PEAR/Command/Common.php';
require_once 'Archive/Tar.php';

class PEAR_Command_Mage extends PEAR_Command_Common
{
    var $commands = array(
        'mage-package' => array(
            'summary' => 'Build Magento Package',
            'function' => 'doPackage',
            'shortcut' => 'mp',
            'options' => array(
                'targetdir' => array(
                    'shortopt' => 'T',
                    'doc' => 'Target directory for package file.',
                    'arg' => 'TARGETDIR',
                    ),
                ),
            'doc' => '[descfile]
Creates a Magento specific PEAR package from its description file.
'
            ),
        );

    var $pkginfofile;

    var $roles;

    var $xml;

    var $options;

    var $output;

    var $files = array();

    /**
     * PEAR_Command_Package constructor.
     *
     * @access public
     */
    function PEAR_Command_Mage(&$ui, &$config)
    {
        parent::PEAR_Command_Common($ui, $config);
    }

    function doPackage($command, $options, $params)
    {
        $this->output = '';
        $this->options = $options;
        $this->pkginfofile = isset($params[0]) ? $params[0] : 'package.xml';

        $this->xml = simplexml_load_file($this->pkginfofile);

        $result = $this->_collectFiles();
        if ($result instanceof PEAR_Error) {
            return $result;
        }

        $result = $this->_generateTarFile();
        if ($result instanceof PEAR_Error) {
            return $result;
        }

        if ($this->output) {
            $this->ui->outputData($this->output, $command);
        }

        return true;
    }

    function _collectFiles($node=null, $path='')
    {
        if (is_null($node)) {
            $node = $this->xml->contents->dir;
        }

        if ($node->getName()=='file') {
            $roles = $this->getRoles();
            $roleDir = $this->config->get($roles[(string)$node['role']]['dir_config']);
            $filePath = $roleDir.$path;
            if (!is_file($filePath)) {
                $result = new PEAR_Error('Could not find file: '.$filePath);
                return $result;
            }
            $this->files[$roleDir][] = $filePath;
        }
        elseif ($children = $node->children()) {
            foreach ($children as $child) {
                $result = $this->_collectFiles($child, $path.DIRECTORY_SEPARATOR.(string)$child['name']);
                if ($result instanceof PEAR_Error) {
                    return $result;
                }
            }
        }
        return true;
    }

    function _generateTarFile($compress=true)
    {
        $pkgver = (string)$this->xml->name.'-'.(string)$this->xml->version->release;
        $targetdir = !empty($this->options['targetdir']) ? $this->options['targetdir'].DIRECTORY_SEPARATOR : '';
        $tarname = $targetdir.$pkgver.($compress ? '.tgz' : '.tar');

        $tar = new Archive_Tar($tarname, $compress);
        $tar->setErrorHandling(PEAR_ERROR_RETURN);

        $result = $tar->create(array($this->pkginfofile));
        if (PEAR::isError($result)) {
            return $this->raiseError($result);
        }

        foreach ($this->files as $roleDir=>$files) {
            $result = $tar->addModify($files, $pkgver, $roleDir);
        }
        if (PEAR::isError($result)) {
            return $this->raiseError($result);
        }

        $this->output .= 'Successfully created '.$tarname."\n";

        return true;
    }

    function getRoles()
    {
        if (!$this->roles) {
            $this->roles = array(
                'magelocal' => array('name'=>'Magento Local module file', 'dir_config'=>'mage_local_dir'),
                'magecommunity' => array('name'=>'Magento Community module file', 'dir_config'=>'mage_community_dir'),
                'magecore' => array('name'=>'Magento Core team module file', 'dir_config'=>'mage_core_dir'),
                'magedesign' => array('name'=>'Magento User Interface (layouts, templates)', 'dir_config'=>'mage_design_dir'),
                'mageetc' => array('name'=>'Magento Global Configuration', 'dir_config'=>'mage_etc_dir'),
                'magelib' => array('name'=>'Magento PHP Library file', 'dir_config'=>'mage_lib_dir'),
                'magelocale' => array('name'=>'Magento Locale language file', 'dir_config'=>'mage_locale_dir'),
                'magemedia' => array('name'=>'Magento Media library', 'dir_config'=>'mage_media_dir'),
                'mageskin' => array('name'=>'Magento Theme Skin (Images, CSS, JS)', 'dir_config'=>'mage_skin_dir'),
                'mageweb' => array('name'=>'Magento Other web accessible file', 'dir_config'=>'mage_web_dir'),
                'magetest' => array('name'=>'Magento PHPUnit test', 'dir_config'=>'mage_test_dir'),
                'mage' => array('name'=>'Magento other', 'dir_config'=>'mage_dir'),
                'php' => array('dir_config'=>'php_dir'),
                'data' => array('dir_config'=>'data_dir'),
                'doc' => array('dir_config'=>'doc_dir'),
                'test' => array('dir_config'=>'test_dir'),
                'temp' => array('dir_config'=>'temp_dir'),
            );
        }

        return $this->roles;
    }
}