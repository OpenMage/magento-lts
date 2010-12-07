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
 * @copyright 2004-2007 The PHP Group
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   CVS: $Id: XMLOutput.php,v 1.6 2007/11/19 22:44:00 farell Exp $
 * @link      http://pear.php.net/package/PEAR_PackageFileManager
 * @since     File available since Release 1.2.0
 */

/**
 * Class for XML output
 *
 * @category  PEAR
 * @package   PEAR_PackageFileManager
 * @author    Greg Beaver <cellog@php.net>
 * @copyright 2004-2007 The PHP Group
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1.6.3
 * @link      http://pear.php.net/package/PEAR_PackageFileManager
 * @since     Class available since Release 1.2.0
 */

class PEAR_PackageFileManager_XMLOutput extends PEAR_Common
{
    /**
     * Generate part of an XML description with release information.
     *
     * @param array $pkginfo   array with release information
     * @param bool  $changelog whether the result will be in a changelog element
     *
     * @return string XML data
     * @access private
     */
    function _makeReleaseXml($pkginfo, $changelog = false)
    {
        $indent = $changelog ? "  " : "";
        $ret = "$indent  <release>\n";
        if (!empty($pkginfo['version'])) {
            $ret .= "$indent    <version>$pkginfo[version]</version>\n";
        }
        if (!empty($pkginfo['release_date'])) {
            $ret .= "$indent    <date>$pkginfo[release_date]</date>\n";
        }
        if (!empty($pkginfo['release_license'])) {
            $ret .= "$indent    <license>$pkginfo[release_license]</license>\n";
        }
        if (!empty($pkginfo['release_state'])) {
            $ret .= "$indent    <state>$pkginfo[release_state]</state>\n";
        }
        if (!empty($pkginfo['release_notes'])) {
            $ret .= "$indent    <notes>".htmlspecialchars($pkginfo['release_notes'])."</notes>\n";
        }
        if (!empty($pkginfo['release_warnings'])) {
            $ret .= "$indent    <warnings>".htmlspecialchars($pkginfo['release_warnings'])."</warnings>\n";
        }
        if (isset($pkginfo['release_deps']) && sizeof($pkginfo['release_deps']) > 0) {
            $ret .= "$indent    <deps>\n";
            foreach ($pkginfo['release_deps'] as $dep) {
                $ret .= "$indent      <dep type=\"$dep[type]\" rel=\"$dep[rel]\"";
                if (isset($dep['version'])) {
                    $ret .= " version=\"$dep[version]\"";
                }
                if (isset($dep['optional'])) {
                    $ret .= " optional=\"$dep[optional]\"";
                }
                if (isset($dep['name'])) {
                    $ret .= ">$dep[name]</dep>\n";
                } else {
                    $ret .= "/>\n";
                }
            }
            $ret .= "$indent    </deps>\n";
        }
        if (isset($pkginfo['configure_options'])) {
            $ret .= "$indent    <configureoptions>\n";
            foreach ($pkginfo['configure_options'] as $c) {
                $ret .= "$indent      <configureoption name=\"".
                    htmlspecialchars($c['name']) . "\"";
                if (isset($c['default'])) {
                    $ret .= " default=\"" . htmlspecialchars($c['default']) . "\"";
                }
                $ret .= " prompt=\"" . htmlspecialchars($c['prompt']) . "\"";
                $ret .= "/>\n";
            }
            $ret .= "$indent    </configureoptions>\n";
        }
        if (isset($pkginfo['provides'])) {
            foreach ($pkginfo['provides'] as $key => $what) {
                $ret .= "$indent    <provides type=\"$what[type]\" ";
                $ret .= "name=\"$what[name]\" ";
                if (isset($what['extends'])) {
                    $ret .= "extends=\"$what[extends]\" ";
                }
                $ret .= "/>\n";
            }
        }
        if (isset($pkginfo['filelist'])) {
            $ret .= "$indent    <filelist>\n";
            $ret .= $this->_doFileList($indent, $pkginfo['filelist'], '/');
            $ret .= "$indent    </filelist>\n";
        }
        $ret .= "$indent  </release>\n";
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