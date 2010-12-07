<?php
/**
 * PEAR_Command_Install (remote-install, remote-upgrade, remote-upgrade-all, remote-uninstall commands)
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2006 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id: Remoteinstall.php,v 1.3 2006/04/02 23:14:59 cellog Exp $
 * @link       http://pear.php.net/package/PEAR
 * @since      File available since Release 0.1
 */

/**
 * base class
 */
require_once 'PEAR/Command/Install.php';

define('PEAR_REMOTEINSTALL_OK', 1);
/**
 * PEAR commands for installation or deinstallation/upgrading of
 * packages.
 *
 * @category   pear
 * @package    PEAR
 * @author     Stig Bakken <ssb@php.net>
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2006 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 0.3.1
 * @link       http://pear.php.net/package/PEAR
 * @since      Class available since Release 0.1
 */
class PEAR_Command_RemoteInstall extends PEAR_Command_Install
{
    // {{{ properties

    var $commands = array(
        'remote-install' => array(
            'summary' => 'Install Package',
            'function' => 'doInstall',
            'shortcut' => 'inr',
            'options' => array(
                'force' => array(
                    'shortopt' => 'f',
                    'doc' => 'will overwrite newer installed packages',
                    ),
                'nodeps' => array(
                    'shortopt' => 'n',
                    'doc' => 'ignore dependencies, install anyway',
                    ),
                'register-only' => array(
                    'shortopt' => 'r',
                    'doc' => 'do not install files, only register the package as installed',
                    ),
                'soft' => array(
                    'shortopt' => 's',
                    'doc' => 'soft install, fail silently, or upgrade if already installed',
                    ),
                'nobuild' => array(
                    'shortopt' => 'B',
                    'doc' => 'don\'t build C extensions',
                    ),
                'nocompress' => array(
                    'shortopt' => 'Z',
                    'doc' => 'request uncompressed files when downloading',
                    ),
                'installroot' => array(
                    'shortopt' => 'R',
                    'arg' => 'DIR',
                    'doc' => 'root directory used when installing files (ala PHP\'s INSTALL_ROOT)',
                    ),
                'ignore-errors' => array(
                    'doc' => 'force install even if there were errors',
                    ),
                'alldeps' => array(
                    'shortopt' => 'a',
                    'doc' => 'install all required and optional dependencies',
                    ),
                'onlyreqdeps' => array(
                    'shortopt' => 'o',
                    'doc' => 'install all required dependencies',
                    ),
                'remoteconfig' => array(
                    'shortopt' => 'F',
                    'arg' => 'URL',
                    'doc' => 'also install to ftp site using remote config file (ftp://host.com/pear.conf)'
                    ),
                'offline' => array(
                    'shortopt' => 'O',
                    'doc' => 'do not attempt to download any urls or contact channels',
                    ),
                'pretend' => array(
                    'shortopt' => 'p',
                    'doc' => 'Only list the packages that would be downloaded',
                    ),
                ),
            'doc' => '[channel/]<package> ...
Installs one or more PEAR packages.  You can specify a package to
install in four ways:

"Package-1.0.tgz" : installs from a local file

"http://example.com/Package-1.0.tgz" : installs from
anywhere on the net.

"package.xml" : installs the package described in
package.xml.  Useful for testing, or for wrapping a PEAR package in
another package manager such as RPM.

"Package[-version/state][.tar]" : queries your default channel\'s server
({config master_server}) and downloads the newest package with
the preferred quality/state ({config preferred_state}).

To retrieve Package version 1.1, use "Package-1.1," to retrieve
Package state beta, use "Package-beta."  To retrieve an uncompressed
file, append .tar (make sure there is no file by the same name first)

To download a package from another channel, prefix with the channel name like
"channel/Package"

More than one package may be specified at once.  It is ok to mix these
four ways of specifying packages.
'),
        'remote-upgrade' => array(
            'summary' => 'Upgrade Package',
            'function' => 'doInstall',
            'shortcut' => 'upr',
            'options' => array(
                'force' => array(
                    'shortopt' => 'f',
                    'doc' => 'overwrite newer installed packages',
                    ),
                'nodeps' => array(
                    'shortopt' => 'n',
                    'doc' => 'ignore dependencies, upgrade anyway',
                    ),
                'register-only' => array(
                    'shortopt' => 'r',
                    'doc' => 'do not install files, only register the package as upgraded',
                    ),
                'nobuild' => array(
                    'shortopt' => 'B',
                    'doc' => 'don\'t build C extensions',
                    ),
                'nocompress' => array(
                    'shortopt' => 'Z',
                    'doc' => 'request uncompressed files when downloading',
                    ),
                'installroot' => array(
                    'shortopt' => 'R',
                    'arg' => 'DIR',
                    'doc' => 'root directory used when installing files (ala PHP\'s INSTALL_ROOT)',
                    ),
                'ignore-errors' => array(
                    'doc' => 'force install even if there were errors',
                    ),
                'alldeps' => array(
                    'shortopt' => 'a',
                    'doc' => 'install all required and optional dependencies',
                    ),
                'onlyreqdeps' => array(
                    'shortopt' => 'o',
                    'doc' => 'install all required dependencies',
                    ),
                'remoteconfig' => array(
                    'shortopt' => 'F',
                    'arg' => 'URL',
                    'doc' => 'also upgrade on ftp site using remote config file (ftp://host.com/pear.conf)'
                    ),
                'offline' => array(
                    'shortopt' => 'O',
                    'doc' => 'do not attempt to download any urls or contact channels',
                    ),
                'pretend' => array(
                    'shortopt' => 'p',
                    'doc' => 'Only list the packages that would be downloaded',
                    ),
                ),
            'doc' => '<package> ...
Upgrades one or more PEAR packages.  See documentation for the
"install" command for ways to specify a package.

When upgrading, your package will be updated if the provided new
package has a higher version number (use the -f option if you need to
upgrade anyway).

More than one package may be specified at once.
'),
        'remote-upgrade-all' => array(
            'summary' => 'Upgrade All Packages',
            'function' => 'doInstall',
            'shortcut' => 'uar',
            'options' => array(
                'nodeps' => array(
                    'shortopt' => 'n',
                    'doc' => 'ignore dependencies, upgrade anyway',
                    ),
                'register-only' => array(
                    'shortopt' => 'r',
                    'doc' => 'do not install files, only register the package as upgraded',
                    ),
                'nobuild' => array(
                    'shortopt' => 'B',
                    'doc' => 'don\'t build C extensions',
                    ),
                'nocompress' => array(
                    'shortopt' => 'Z',
                    'doc' => 'request uncompressed files when downloading',
                    ),
                'installroot' => array(
                    'shortopt' => 'R',
                    'arg' => 'DIR',
                    'doc' => 'root directory used when installing files (ala PHP\'s INSTALL_ROOT)',
                    ),
                'ignore-errors' => array(
                    'doc' => 'force install even if there were errors',
                    ),
                'remoteconfig' => array(
                    'shortopt' => 'F',
                    'arg' => 'URL',
                    'doc' => 'also upgrade on ftp site using remote config file (ftp://host.com/pear.conf)'
                    ),
                ),
            'doc' => '
Upgrades all packages that have a newer release available.  Upgrades are
done only if there is a release available of the state specified in
"preferred_state" (currently {config preferred_state}), or a state considered
more stable.
'),
        'remote-uninstall' => array(
            'summary' => 'Un-install Package',
            'function' => 'doUninstall',
            'shortcut' => 'unr',
            'options' => array(
                'nodeps' => array(
                    'shortopt' => 'n',
                    'doc' => 'ignore dependencies, uninstall anyway',
                    ),
                'register-only' => array(
                    'shortopt' => 'r',
                    'doc' => 'do not remove files, only register the packages as not installed',
                    ),
                'installroot' => array(
                    'shortopt' => 'R',
                    'arg' => 'DIR',
                    'doc' => 'root directory used when installing files (ala PHP\'s INSTALL_ROOT)',
                    ),
                'ignore-errors' => array(
                    'doc' => 'force install even if there were errors',
                    ),
                'remoteconfig' => array(
                    'shortopt' => 'F',
                    'arg' => 'URL',
                    'doc' => 'also uninstall on ftp site using remote config file (ftp://host.com/pear.conf)'
                    ),
                'offline' => array(
                    'shortopt' => 'O',
                    'doc' => 'do not attempt to uninstall remotely',
                    ),
                ),
            'doc' => '[channel/]<package> ...
Uninstalls one or more PEAR packages.  More than one package may be
specified at once.  Prefix with channel name to uninstall from a
channel not in your default channel ({config default_channel})
'),
    );

    // }}}
    // {{{ constructor

    /**
     * PEAR_Command_Install constructor.
     *
     * @access public
     */
    function PEAR_Command_Remoteinstall(&$ui, &$config)
    {
        parent::PEAR_Command_Install($ui, $config);
    }

    // }}}

    /**
     * For unit testing purposes
     */
    function &getDownloader(&$ui, $options, &$config)
    {
        if (!class_exists('PEAR_Downloader')) {
            require_once 'PEAR/Downloader.php';
        }
        $a = &new PEAR_Downloader($ui, $options, $config);
        return $a;
    }

    /**
     * For unit testing purposes
     */
    function &getInstaller(&$ui)
    {
        if (!class_exists('PEAR_RemoteInstaller')) {
            require_once 'PEAR/RemoteInstaller.php';
        }
        $a = &new PEAR_RemoteInstaller($ui);
        return $a;
    }

    // {{{ doInstall()

    function doInstall($command, $options, $params)
    {
        if (empty($this->installer)) {
            $this->installer = &$this->getInstaller($this->ui);
        }
        if (isset($options['remoteconfig'])) {
            $e = $this->config->readFTPConfigFile($options['remoteconfig']);
            if (!PEAR::isError($e)) {
                $this->installer->setConfig($this->config);
            }
        } elseif (!$this->config->get('remote_config')) {
            return $this->raiseError('Error: ' . $command . ' expects either option ' .
                '"remoteconfig" be set, or remote_config configuration variable be used');
        } else {
            $e = $this->config->readFTPConfigFile($this->config->get('remote_config'));
            if (!PEAR::isError($e)) {
                $this->installer->setConfig($this->config);
            }
        }
        $command = str_replace('remote-', '', $command); // fool parent
        return parent::doInstall($command, $options, $params);
    }

    // }}}
    // {{{ doUninstall()

    function doUninstall($command, $options, $params)
    {
        if (empty($this->installer)) {
            $this->installer = &$this->getInstaller($this->ui);
        }
        if (isset($options['remoteconfig'])) {
            $e = $this->config->readFTPConfigFile($options['remoteconfig']);
            if (!PEAR::isError($e)) {
                $this->installer->setConfig($this->config);
            }
        } elseif (!$this->config->get('remote_config')) {
            return $this->raiseError('Error: ' . $command . ' expects either option ' .
                '"remoteconfig" be set, or remote_config configuration variable be used');
        } else {
            $e = $this->config->readFTPConfigFile($this->config->get('remote_config'));
            if (!PEAR::isError($e)) {
                $this->installer->setConfig($this->config);
            }
        }
        $command = 'uninstall'; // fool parent
        return parent::doUninstall($command, $options, $params);
    }

    // }}}
}

/**
 * This is a hack for PEAR 1.4.x to work
 * @ignore
 */
class Net_FTP {}
?>
