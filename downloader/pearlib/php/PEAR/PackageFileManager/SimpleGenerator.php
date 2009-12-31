<?php
/**
 * Class for XML output
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  PEAR
 * @package   PEAR_PackageFileManager
 * @author    Greg Beaver <cellog@php.net>
 * @copyright 2005-2007 The PHP Group
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   CVS: $Id: SimpleGenerator.php,v 1.9 2007/11/19 22:44:00 farell Exp $
 * @link      http://pear.php.net/package/PEAR_PackageFileManager
 * @since     File available since Release 1.3.0
 */

require_once 'PEAR/PackageFile/Generator/v1.php';

/**
 * Class for XML output
 *
 * @category  PEAR
 * @package   PEAR_PackageFileManager
 * @author    Greg Beaver <cellog@php.net>
 * @copyright 2005-2007 The PHP Group
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1.6.3
 * @link      http://pear.php.net/package/PEAR_PackageFileManager
 * @since     Class available since Release 1.3.0
 */

class PEAR_PackageFileManager_SimpleGenerator extends PEAR_PackageFile_Generator_v1
{
    var $_options;

    /**
     * remove a warning about missing parameters - don't delete this
     */
    function PEAR_PackageFileManager_SimpleGenerator()
    {
    }

    /**
     * @param array $opts list of generation options
     *
     * @return void
     */
    function setPackageFileManagerOptions($opts)
    {
        $this->_options = $opts;
    }

    /**
     * Return an XML document based on the package info (as returned
     * by the PEAR_Common::infoFrom* methods).
     *
     * @param array $pkginfo package info
     *
     * @return string XML data
     * @access public
     * @deprecated use a PEAR_PackageFile_v* object's generator instead
     */
    function xmlFromInfo($pkginfo)
    {
        include_once 'PEAR/PackageFile.php';
        include_once 'PEAR/Config.php';
        $config = &PEAR_Config::singleton();
        $packagefile = &new PEAR_PackageFile($config);
        $pf = &$packagefile->fromArray($pkginfo);
        parent::PEAR_PackageFile_Generator_v1($pf);
        $ret = $this->toXml();
        if (!$ret) {
            $errors = $pf->getValidationWarnings();
            return PEAR::raiseError('Invalid package.xml file', null, null, null, $errors);
        }
        return $ret;
    }

    function getFileRoles()
    {
        return PEAR_Common::getFileRoles();
    }

    function getReplacementTypes()
    {
        return PEAR_Common::getReplacementTypes();
    }

    /**
     * Validate XML package definition file.
     *
     * @param string $info        Filename of the package archive or of the
     *                            package definition file
     * @param array  &$errors     Array that will contain the errors
     * @param array  &$warnings   Array that will contain the warnings
     * @param string $dir_prefix  (optional) directory where source files
     *                            may be found, or empty if they are not available
     *
     * @access public
     * @return boolean
     * @deprecated use the validation of PEAR_PackageFile objects
     */
    function validatePackageInfo($info, &$errors, &$warnings, $dir_prefix = '')
    {
        include_once 'PEAR/PackageFile.php';
        include_once 'PEAR/Config.php';
        $config = &PEAR_Config::singleton();
        $packagefile = &new PEAR_PackageFile($config);
        PEAR::staticPushErrorHandling(PEAR_ERROR_RETURN);
        if (is_array($info)) {
            $pf = &$packagefile->fromArray($info);
            if (!$pf->validate(PEAR_VALIDATE_NORMAL)) {
                foreach ($pf->getValidationWarnings() as $err) {
                    if ($error['level'] == 'error') {
                        $errors[] = $error['message'];
                    } else {
                        $warnings[] = $error['message'];
                    }
                }
                return false;
            }
        } else {
            $pf = &$packagefile->fromAnyFile($info, PEAR_VALIDATE_NORMAL);
        }
        PEAR::staticPopErrorHandling();
        if (PEAR::isError($pf)) {
            $errs = $pf->getUserinfo();
            if (is_array($errs)) {
                foreach ($errs as $error) {
                    if ($error['level'] == 'error') {
                        $errors[] = $error['message'];
                    } else {
                        $warnings[] = $error['message'];
                    }
                }
            }
            return false;
        }
        return true;
    }

    /**
     * @param array $list
     *
     * @return string
     * @access protected
     */
    function recursiveXmlFilelist($list)
    {
        $this->_dirs = array();
        foreach ($list as $file => $attributes) {
            $this->_addDir($this->_dirs, explode('/', dirname($file)), $file, $attributes);
        }
        if (!isset($this->_dirs['dirs'])) {
            $this->_dirs['dirs'] = array();
        }
        if (count($this->_dirs['dirs']) != 1 || isset($this->_dirs['files'])) {
            $this->_dirs = array('dirs' => array('/' => $this->_dirs));
        }
        return $this->_formatDir($this->_dirs, '', '', true);
    }

    /**
     * @param array       &$dirs
     * @param array       $dir
     * @param string|null $file
     * @param array|null  $attributes
     *
     * @return void
     * @access private
     */
    function _addDir(&$dirs, $dir, $file = null, $attributes = null)
    {
        if ($dir == array() || $dir == array('.')) {
            $dirs['files'][basename($file)] = $attributes;
            return;
        }
        $curdir = array_shift($dir);
        if (!isset($dirs['dirs'][$curdir])) {
            $dirs['dirs'][$curdir] = array();
        }
        $this->_addDir($dirs['dirs'][$curdir], $dir, $file, $attributes);
    }

    /**
     * @param array  $dirs
     * @param string $indent
     * @param string $curdir
     * @param bool   $toplevel
     *
     * @return string
     * @access private
     */
    function _formatDir($dirs, $indent = '', $curdir = '', $toplevel = false)
    {
        $ret = '';
        if (!count($dirs)) {
            return '';
        }
        if (isset($dirs['dirs'])) {
            uksort($dirs['dirs'], 'strnatcasecmp');
            foreach ($dirs['dirs'] as $dir => $contents) {
                if ($dir == '/') {
                    $usedir = '/';
                } else {
                    if ($curdir == '/') {
                        $curdir = '';
                    }
                    $usedir = "$curdir/$dir";
                }
                $ret .= "$indent   <dir name=\"$dir\"";
                if ($toplevel) {
                    $ret .= ' baseinstalldir="' . $this->_options['baseinstalldir'] . '"';
                } else {
                    if (isset($this->_options['installexceptions'][$dir])) {
                        $ret .= ' baseinstalldir="' . $this->_options['installexceptions'][$dir] . '"';
                    }
                }
                $ret .= ">\n";
                $ret .= $this->_formatDir($contents, "$indent ", $usedir);
                $ret .= "$indent   </dir> <!-- $usedir -->\n";
            }
        }
        if (isset($dirs['files'])) {
            uksort($dirs['files'], 'strnatcasecmp');
            foreach ($dirs['files'] as $file => $attribs) {
                $ret .= $this->_formatFile($file, $attribs, $indent);
            }
        }
        return $ret;
    }

    /**
     * Generate the <file> tag
     *
     * @param string $file       fullname to file
     * @param array  $attributes file attributes
     * @param string $indent     string to indent xml tag
     *
     * @return string
     * @access private
     */
    function _formatFile($file, $attributes, $indent)
    {
        $ret = "$indent   <file role=\"$attributes[role]\"";
        if (isset($this->_options['installexceptions'][$file])) {
            $ret .= ' baseinstalldir="' . $this->_options['installexceptions'][$file] . '"';
        }
        if (isset($attributes['md5sum'])) {
            $ret .= " md5sum=\"$attributes[md5sum]\"";
        }
        if (isset($attributes['platform'])) {
            $ret .= " platform=\"$attributes[platform]\"";
        }
        if (!empty($attributes['install-as'])) {
            $ret .= ' install-as="' .
                htmlspecialchars($attributes['install-as']) . '"';
        }
        $ret .= ' name="' . htmlspecialchars($file) . '"';
        if (empty($attributes['replacements'])) {
            $ret .= "/>\n";
        } else {
            $ret .= ">\n";
            foreach ($attributes['replacements'] as $r) {
                $ret .= "$indent    <replace";
                foreach ($r as $k => $v) {
                    $ret .= " $k=\"" . htmlspecialchars($v) .'"';
                }
                $ret .= "/>\n";
            }
            $ret .= "$indent   </file>\n";
        }
        return $ret;
    }

    /**
     * Generate the <filelist> tag
     *
     * @param string $indent   string to indent xml tag
     * @param array  $filelist list of files included in release
     * @param string $curdir
     *
     * @access private
     * @return string XML data
     */
    function _doFileList($indent, $filelist, $curdir)
    {
        $ret = '';
        foreach ($filelist as $file => $fa) {
            if (isset($fa['##files'])) {
                $ret .= "$indent      <dir";
            } else {
                $ret .= "$indent      <file";
            }

            if (isset($fa['role'])) {
                $ret .= " role=\"$fa[role]\"";
            }
            if (isset($fa['baseinstalldir'])) {
                $ret .= ' baseinstalldir="' .
                    htmlspecialchars($fa['baseinstalldir']) . '"';
            }
            if (isset($fa['md5sum'])) {
                $ret .= " md5sum=\"$fa[md5sum]\"";
            }
            if (isset($fa['platform'])) {
                $ret .= " platform=\"$fa[platform]\"";
            }
            if (!empty($fa['install-as'])) {
                $ret .= ' install-as="' .
                    htmlspecialchars($fa['install-as']) . '"';
            }
            $ret .= ' name="' . htmlspecialchars($file) . '"';
            if (isset($fa['##files'])) {
                $ret .= ">\n";
                $recurdir = $curdir;
                if ($recurdir == '///') {
                    $recurdir = '';
                }
                $ret .= $this->_doFileList("$indent ", $fa['##files'], $recurdir . $file . '/');
                $displaydir = $curdir;
                if ($displaydir == '///' || $displaydir == '/') {
                    $displaydir = '';
                }
                $ret .= "$indent      </dir> <!-- $displaydir$file -->\n";
            } else {
                if (empty($fa['replacements'])) {
                    $ret .= "/>\n";
                } else {
                    $ret .= ">\n";
                    foreach ($fa['replacements'] as $r) {
                        $ret .= "$indent        <replace";
                        foreach ($r as $k => $v) {
                            $ret .= " $k=\"" . htmlspecialchars($v) .'"';
                        }
                        $ret .= "/>\n";
                    }
                    $ret .= "$indent      </file>\n";
                }
            }
        }
        return $ret;
    }
}
?>