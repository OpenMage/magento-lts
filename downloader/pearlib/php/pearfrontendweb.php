<?php
/**
  +----------------------------------------------------------------------+
  | PHP Version 4                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2003 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 2.02 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available at through the world-wide-web at                           |
  | http://www.php.net/license/2_02.txt.                                 |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author: Christian Dickmann <dickmann@php.net>                        |
  |         Pierre-Alain Joye <pajoye@php.net>                           |
  |         Tias Guns <tias@ulyssis.org>                                 |
  +----------------------------------------------------------------------+

 * Web-based PEAR Frontend, include this file to display the fontend.
 * This file does the basic configuration, handles all requests and calls
 * the needed commands.
 *
 * @category   pear
 * @package    PEAR_Frontend_Web
 * @author     Christian Dickmann <dickmann@php.net>
 * @author     Pierre-Alain Joye <pajoye@php.net>
 * @author     Tias Guns <tias@ulyssis.org>
 * @copyright  1997-2007 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @version    CVS: $Id: pearfrontendweb.php,v 1.60 2007/06/17 14:33:52 tias Exp $
 * @link       http://pear.php.net/package/PEAR_Frontend_Web
 * @since      File available since Release 0.1
 */

/**
 * This is PEAR_Frontend_Web
 */
define('PEAR_Frontend_Web',1);
@session_start();

/**
 * base frontend class
 */
require_once 'PEAR/Frontend.php';
require_once 'PEAR/Registry.php';
require_once 'PEAR/Config.php';
require_once 'PEAR/Command.php';

// set $pear_user_config if it isn't set yet
// finds an existing file, or proposes the default location
if (!isset($pear_user_config) || $pear_user_config == '') {
    if (OS_WINDOWS) {
        $conf_name = 'pear.ini';
    } else {
        $conf_name = 'pear.conf';
    }
    
    $default_config_dirs = array(
        substr(dirname(__FILE__), 0, -strlen('PEAR/PEAR')), // strip PEAR/PEAR
        dirname($_SERVER['SCRIPT_FILENAME']),
        PEAR_CONFIG_SYSCONFDIR,
                );
    // set the default: __FILE__ without PEAR/PEAR/
    $pear_user_config = $default_config_dirs[0].DIRECTORY_SEPARATOR.$conf_name;

    foreach ($default_config_dirs as $confdir) {
        if (file_exists($confdir.DIRECTORY_SEPARATOR.$conf_name)) {
            $pear_user_config = $confdir.DIRECTORY_SEPARATOR.$conf_name;
            break;
        }
    }
    unset($conf_name, $default_config_dirs, $confdir);
}

// moving this here allows startup messages and errors to work properly
PEAR_Frontend::setFrontendClass('PEAR_Frontend_Web');
// Init PEAR Installer Code and WebFrontend
$GLOBALS['_PEAR_Frontend_Web_config'] = &PEAR_Config::singleton($pear_user_config, '');
$config  = &$GLOBALS['_PEAR_Frontend_Web_config'];

$ui = &PEAR_Command::getFrontendObject();
$ui->setConfig($config);

PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array($ui, "displayFatalError"));

// Cient requests an Image/Stylesheet/Javascript
// outputFrontendFile() does exit()
if (isset($_GET["css"])) {
    $ui->outputFrontendFile($_GET["css"], 'css');
}
if (isset($_GET["js"])) {
    $ui->outputFrontendFile($_GET["js"], 'js');
}
if (isset($_GET["img"])) {
    $ui->outputFrontendFile($_GET["img"], 'image');
}

$verbose = $config->get("verbose");
$cmdopts = array();
$opts    = array();
$params  = array();

if (!file_exists($pear_user_config)) {
    // I think PEAR_Frontend_Web is running for the first time!
    // Create config and install it properly ...
    $ui->outputBegin(null);
    print('<h3>Preparing PEAR_Frontend_Web for its first time use...</h3>');

    // probable base_dir:
    $dir = dirname(__FILE__); // eg .../example/PEAR/PEAR/WebInstaller.php
    $dir = substr($dir, 0, strrpos($dir, DIRECTORY_SEPARATOR)); // eg .../example/PEAR
    $dir = substr($dir, 0, strrpos($dir, DIRECTORY_SEPARATOR)); // eg .../example
    $dir = '';
    // if it doesn\'t work because of symlinks or who knows what,
    // try with $pear_dir, it is set in the default frontend inclusion file
    if (isset($pear_dir) && (!is_dir($dir) || !is_writable($dir))) {
        $dir = $pear_dir;
        if (substr($pear_dir, -1) == DIRECTORY_SEPARATOR) {
            $dir = substr($pear_dir, 0, -1); // strip trailing /
        }
        $dir = substr($dir, 0, strrpos($dir, DIRECTORY_SEPARATOR)); // eg .../example
    }

    $dir .= DIRECTORY_SEPARATOR;
    if (!is_dir($dir)) {
        trigger_error('Can not find a base installation directory of PEAR ('.$dir.' doesn\'t work), so we can\'t create a config for it. Please supply it in the variable \'$pear_dir\'. The $pear_dir must have at least the subdirectory PEAR/ and be writable by this frontend.', E_USER_ERROR);
        die();
    }

    print('Saving config file ('.$pear_user_config.')...');
    // First of all set some config-vars:
    // Tries to be compatible with go-pear
    if (!isset($pear_dir)) {
        $pear_dir = $dir.'PEAR'; // default (go-pear compatible)
    }
    $cmd = PEAR_Command::factory('config-set', $config);
    $ok = $cmd->run('config-set', array(), array('php_dir',  $pear_dir));
    $ok = $cmd->run('config-set', array(), array('doc_dir',  $pear_dir.'/docs'));
    $ok = $cmd->run('config-set', array(), array('ext_dir',  $dir.'ext'));
    $ok = $cmd->run('config-set', array(), array('bin_dir',  $dir.'bin'));
    $ok = $cmd->run('config-set', array(), array('data_dir', $pear_dir.'/data'));
    $ok = $cmd->run('config-set', array(), array('test_dir', $pear_dir.'/test'));
    $ok = $cmd->run('config-set', array(), array('temp_dir', $dir.'temp'));
    $ok = $cmd->run('config-set', array(), array('download_dir', $dir.'temp/download'));
    $ok = $cmd->run('config-set', array(), array('cache_dir', $pear_dir.'/cache'));
    $ok = $cmd->run('config-set', array(), array('cache_ttl', 300));
    $ok = $cmd->run('config-set', array(), array('default_channel', 'pear.php.net'));
    $ok = $cmd->run('config-set', array(), array('preferred_mirror', 'pear.php.net'));

    print('Checking package registry...');
    // Register packages
    $packages = array(
                                'Archive_Tar',
                                'Console_Getopt',
                                'HTML_Template_IT',
                                'PEAR',
                                'PEAR_Frontend_Web',
                                'Structures_Graph'
                        );
    $reg = &$config->getRegistry();
    if (!file_exists($pear_dir.'/.registry')) {
        PEAR::raiseError('Directory "'.$pear_dir.'/.registry" does not exist. please check your installation');
    }

    foreach($packages as $pkg) {
        $info = $reg->packageInfo($pkg);
        foreach($info['filelist'] as $fileName => $fileInfo) {
            if($fileInfo['role'] == "php") {
                $info['filelist'][$fileName]['installed_as'] =
                    str_replace('{dir}',$dir, $fileInfo['installed_as']);
            }
        }
        $reg->updatePackage($pkg, $info, false);
    }

    print('<p><em>PEAR_Frontend_Web configured succesfully !</em></p>');
    $msg = sprintf('<p><a href="%s">Click here to continue</a></p>',
                    $_SERVER['PHP_SELF']);
    print($msg);
    $ui->outputEnd(null);
    die();
}

// Check _isProtected() override (disables the 'not protected' warning)
if (isset($pear_frontweb_protected) && $pear_frontweb_protected === true) {
    $GLOBALS['_PEAR_Frontend_Web_protected'] = true;
}

$cache_dir = $config->get('cache_dir');
if (!is_dir($cache_dir)) {
    include_once 'System.php';
    if (!System::mkDir('-p', $cache_dir)) {
        PEAR::raiseError('Directory "'.$cache_dir.'" does not exist and cannot be created. Please check your installation');
    }
}

if (isset($_GET['command']) && !is_null($_GET['command'])) {
    $command = $_GET['command'];
} else {
    $command = 'list';
}

// Prepare and begin output
$ui->outputBegin($command);

// Handle some different Commands
    switch ($command) {
        case 'install':
        case 'uninstall':
        case 'upgrade':
            if ($_GET['command'] == 'install') {
                // also install dependencies
                $opts['onlyreqdeps'] = true;
                if (isset($_GET['force']) && $_GET['force'] == 'on') {
                    $opts['force'] = true;
                }
            }

            if (strpos($_GET['pkg'], '\\\\') !== false) {
                $_GET['pkg'] = stripslashes($_GET['pkg']);
            }
            $params = array($_GET["pkg"]);
            $cmd = PEAR_Command::factory($command, $config);
            $ok = $cmd->run($command, $opts, $params);

            $ui->finishOutput('Back', array('link' => $_SERVER['PHP_SELF'].'?command=info&pkg='.$_GET['pkg'],
                'text' => 'View package information'));
            break;
        case 'run-scripts' :
            $params = array($_GET['pkg']);
            $cmd = PEAR_Command::factory($command, $config);
            $ok = $cmd->run($command, $opts, $params);
            break;
        case 'info':
        case 'remote-info':
            $reg = &$config->getRegistry();
            // we decide what it is:
            $pkg = $reg->parsePackageName($_GET['pkg']);
            if ($reg->packageExists($pkg['package'], $pkg['channel'])) {
                $command = 'info';
            } else {
                $command = 'remote-info';
            }

            $params = array(strtolower($_GET['pkg']));
            $cmd = PEAR_Command::factory($command, $config);
            $ok = $cmd->run($command, $opts, $params);

            break;
        case 'search':
            if (!isset($_POST['search']) || $_POST['search'] == '') {
                // unsubmited, show forms
                $ui->outputSearch();
            } else {
                if ($_POST['channel'] == 'all') {
                    $opts['allchannels'] = true;
                } else {
                    $opts['channel'] = $_POST['channel'];
                }
                $opts['channelinfo'] = true;

                // submited, do search
                switch ($_POST['search']) {
                    case 'name':
                        $params = array($_POST['input']);
                        break;
                    case 'description':
                        $params = array($_POST['input'], $_POST['input']);
                        break;
                    default:
                        PEAR::raiseError('Can\'t search for '.$_POST['search']);
                        break;
                }

                $cmd = PEAR_Command::factory($command, $config);
                $ok = $cmd->run($command, $opts, $params);
            }

            break;
        case 'config-show':
            $cmd = PEAR_Command::factory($command, $config);
            $res = $cmd->run($command, $opts, $params);

            // if this code is reached, the config vars are submitted
            $set = PEAR_Command::factory('config-set', $config);
            foreach($GLOBALS['_PEAR_Frontend_Web_Config'] as $var => $value) {
                if ($var == 'Filename') {
                    continue; // I hate obscure bugs
                }
                if ($value != $config->get($var)) {
                    print('Saving '.$var.'... ');
                    $res = $set->run('config-set', $opts, array($var, $value));
                    $config->set($var, $value);
                }
            }
            print('<p><b>Config saved succesfully!</b></p>');

            $ui->finishOutput('Back', array('link' => $_SERVER['PHP_SELF'].'?command='.$command, 'text' => 'Back to the config'));
            break;
        case 'list-files':
            $params = array($_GET['pkg']);
            $cmd = PEAR_Command::factory($command, $config);
            $res = $cmd->run($command, $opts, $params);
            break;
        case 'list-docs':
            if (!isset($_GET['pkg'])) {
                PEAR::raiseError('The webfrontend-command list-docs needs at least one \'pkg\' argument.');
                break;
            }
            
            require_once('PEAR/Frontend/Web/Docviewer.php');
            $reg = $config->getRegistry();
            $pkg = $reg->parsePackageName($_GET['pkg']);

            $docview = new PEAR_Frontend_Web_Docviewer($ui);
            $docview->outputListDocs($pkg['package'], $pkg['channel']);
            break;
        case 'doc-show':
            if (!isset($_GET['pkg']) || !isset($_GET['file'])) {
                PEAR::raiseError('The webfrontend-command list-docs needs one \'pkg\' and one \'file\' argument.');
                break;
            }
            
            require_once('PEAR/Frontend/Web/Docviewer.php');
            $reg = $config->getRegistry();
            $pkg = $reg->parsePackageName($_GET['pkg']);

            $docview = new PEAR_Frontend_Web_Docviewer($ui);
            $docview->outputDocShow($pkg['package'], $pkg['channel'], $_GET['file']);
            break;
        case 'list-all':
            // Deprecated, use 'list-categories' is used instead
            if (isset($_GET['chan']) && $_GET['chan'] != '') {
                $opts['channel'] = $_GET['chan'];
            }
            $opts['channelinfo'] = true;
            $cmd = PEAR_Command::factory($command, $config);
            $res = $cmd->run($command, $opts, $params);

            break;
        case 'list-categories':
        case 'list-packages':
            if (isset($_GET['chan']) && $_GET['chan'] != '') {
                $opts['channel'] = $_GET['chan'];
            } else {
                // show 'table of contents' before all channel output
                $ui->outputTableOfChannels();

                $opts['allchannels'] = true;
            }
            if (isset($_GET['opt']) && $_GET['opt'] == 'packages') {
                $opts['packages'] = true;
            }
            $cmd = PEAR_Command::factory($command, $config);
            $res = $cmd->run($command, $opts, $params);

            break;
        case 'list-category':
            if (isset($_GET['chan']) && $_GET['chan'] != '') {
                $opts['channel'] = $_GET['chan'];
            }
            $params = array($_GET['cat']);
            $cmd = PEAR_Command::factory($command, $config);
            $res = $cmd->run($command, $opts, $params);

            break;
        case 'list':
            $opts['allchannels'] = true;
            $opts['channelinfo'] = true;
            $cmd = PEAR_Command::factory($command, $config);
            $res = $cmd->run($command, $opts, $params);

            break;
        case 'list-upgrades':
            $opts['channelinfo'] = true;
            $cmd = PEAR_Command::factory($command, $config);
            $res = $cmd->run($command, $opts, $params);
            $ui->outputUpgradeAll();

            break;
        case 'upgrade-all':
            $cmd = PEAR_Command::factory($command, $config);
            $ok = $cmd->run($command, $opts, $params);

            $ui->finishOutput('Back', array('link' => $_SERVER['PHP_SELF'].'?command=list',
                'text' => 'Click here to go back'));
            break;
        case 'channel-info':
            if (isset($_GET['chan']))
                $params[] = $_GET['chan'];
            $cmd = PEAR_Command::factory($command, $config);
            $ok = $cmd->run($command, $opts, $params);

            break;
        case 'channel-discover':
            if (isset($_GET['chan']))
                $params[] = $_GET['chan'];
            $cmd = PEAR_Command::factory($command, $config);
            $ui->startSession();
            $ok = $cmd->run($command, $opts, $params);

            $ui->finishOutput('Channel Discovery', array('link' =>
                $_SERVER['PHP_SELF'] . '?command=channel-info&chan=' . urlencode($_GET['chan']),
                'text' => 'Click Here for ' . htmlspecialchars($_GET['chan']) . ' Information'));
            break;
        case 'channel-delete':
            if (isset($_GET["chan"]))
                $params[] = $_GET["chan"];
            $cmd = PEAR_Command::factory($command, $config);
            $ok = $cmd->run($command, $opts, $params);

            $ui->finishOutput('Delete Channel', array('link' =>
                $_SERVER['PHP_SELF'] . '?command=list-channels',
                'text' => 'Click here to list all channels'));
            break;
        case 'list-channels':
            $cmd = PEAR_Command::factory($command, $config);
            $ok = $cmd->run($command, $opts, $params);

            break;
        case 'channel-update':
            if (isset($_GET['chan'])) {
                $params = array($_GET['chan']);
            }
            $cmd = PEAR_Command::factory($command, $config);
            $ok = $cmd->run($command, $opts, $params);

            break;
        case 'update-channels':
            // update every channel manually,
            // fixes bug PEAR/#10275 (XML_RPC dependency)
            // will be fixed in next pear release
            $reg = &$config->getRegistry();
            $channels = $reg->getChannels();
            $command = 'channel-update';
            $cmd = PEAR_Command::factory($command, $config);
            
            $success = true;
            $ui->startSession();
            foreach ($channels as $channel) {
                if ($channel->getName() != '__uri') {
                    $success &= $cmd->run($command, $opts,
                                          array($channel->getName()));
                }
            }

            $ui->finishOutput('Update Channel List', array('link' =>
                $_SERVER['PHP_SELF'] . '?command=list-channels',
                'text' => 'Click here to list all channels'));
            break;
        default:
            $cmd = PEAR_Command::factory($command, $config);
            $res = $cmd->run($command, $opts, $params);

            break;
    }

$ui->outputEnd($command);

?>
