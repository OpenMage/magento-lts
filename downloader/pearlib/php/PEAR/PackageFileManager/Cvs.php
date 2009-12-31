<?php
/**
 * The CVS list plugin generator for both PEAR_PackageFileManager,
 * and PEAR_PackageFileManager2 classes.
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
 * @copyright 2003-2007 The PHP Group
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   CVS: $Id: Cvs.php,v 1.15 2007/11/19 22:44:00 farell Exp $
 * @link      http://pear.php.net/package/PEAR_PackageFileManager
 * @since     File available since Release 0.1
 */

require_once 'PEAR/PackageFileManager/File.php';

/**
 * Generate a file list from a CVS checkout.
 *
 * Note that this will <b>NOT</b> work on a
 * repository, only on a checked out CVS module
 *
 * @category  PEAR
 * @package   PEAR_PackageFileManager
 * @author    Greg Beaver <cellog@php.net>
 * @copyright 2003-2007 The PHP Group
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1.6.3
 * @link      http://pear.php.net/package/PEAR_PackageFileManager
 * @since     Class available since Release 0.1
 */

class PEAR_PackageFileManager_CVS extends PEAR_PackageFileManager_File
{
    /**
     * List of CVS-specific files that may exist in CVS but should be
     * ignored when building the package's file list.
     * @var array
     * @access private
     */
    var $_cvsIgnore = array('.cvsignore');

    /**
     * Return a list of all files in the CVS repository
     *
     * This function is like {@link parent::dirList()} except
     * that instead of retrieving a regular filelist, it first
     * retrieves a listing of all the CVS/Entries files in
     * $directory and all of the subdirectories.  Then, it
     * reads the Entries file, and creates a listing of files
     * that are a part of the CVS repository.  No check is
     * made to see if they have been modified, but newly
     * added or removed files are ignored.
     *
     * @param string $directory full path to the directory you want the list of
     *
     * @return array list of files in a directory
     * @uses _recurDirList()
     * @uses _readCVSEntries()
     */
    function dirList($directory)
    {
        static $in_recursion = false;
        if (!$in_recursion) {
            // include only CVS/Entries files
            $this->_setupIgnore(array('*/CVS/Entries'), 0);
            $this->_setupIgnore(array(), 1);
            $in_recursion = true;
            $entries      = parent::dirList($directory);
            $in_recursion = false;
        } else {
            return parent::dirList($directory);
        }
        if (!$entries || !is_array($entries)) {
            if (strcasecmp(get_class($this->_parent),
                'PEAR_PackageFileManager') == 0) {
                $code = PEAR_PACKAGEFILEMANAGER_NOCVSENTRIES;
            } else {
                $code = PEAR_PACKAGEFILEMANAGER2_NOCVSENTRIES;
            }
            return $this->_parent->raiseError($code, $directory);
        }
        return $this->_readCVSEntries($entries);
    }

    /**
     * Iterate over the CVS Entries files, and retrieve every
     * file in the repository
     *
     * @param array $entries array of full paths to CVS/Entries files
     *
     * @uses _getCVSEntries()
     * @uses _isCVSFile()
     * @return array
     * @access private
     */
    function _readCVSEntries($entries)
    {
        $ret    = array();
        $ignore = array_merge((array) $this->_options['ignore'], $this->_cvsIgnore);
        // implicitly ignore packagefile
        $ignore[] = $this->_options['packagefile'];
        $include  = $this->_options['include'];

        $this->ignore = array(false, false);
        $this->_setupIgnore($ignore, 1);
        $this->_setupIgnore($include, 0);
        foreach ($entries as $cvsentry) {
            $directory = @dirname(@dirname($cvsentry));
            if (!$directory) {
                continue;
            }
            $d = $this->_getCVSEntries($cvsentry);
            if (!is_array($d)) {
                continue;
            }
            foreach ($d as $entry) {
                if ($ignore) {
                    if ($this->_checkIgnore($this->_getCVSFileName($entry),
                          $directory . '/' . $this->_getCVSFileName($entry), 1)) {
                        continue;
                    }
                }
                if ($include) {
                    if ($this->_checkIgnore($this->_getCVSFileName($entry),
                          $directory . '/' . $this->_getCVSFileName($entry), 0)) {
                        continue;
                    }
                }
                if ($this->_isCVSFile($entry)) {
                    $ret[] = $directory . '/' . $this->_getCVSFileName($entry);
                }
            }
        }
        return $ret;
    }

    /**
     * Retrieve the filename from an entry
     *
     * This method assumes that the entry is a file,
     * use _isCVSFile() to verify before calling
     *
     * @param string $cvsentry a line in a CVS/Entries file
     *
     * @return string the filename (no path information)
     * @access private
     */
    function _getCVSFileName($cvsentry)
    {
        $stuff = explode('/', $cvsentry);
        array_shift($stuff);
        return array_shift($stuff);
    }

    /**
     * Retrieve the entries in a CVS/Entries file
     *
     * @param string $cvsentryfilename full path to a CVS/Entries file
     *
     * @return array each line of the entries file, output of file()
     * @uses function file()
     * @access private
     */
    function _getCVSEntries($cvsentryfilename)
    {
        $cvsfile = @file($cvsentryfilename);
        if (is_array($cvsfile)) {
            return $cvsfile;
        } else {
            return false;
        }
    }

    /**
     * Check whether an entry is a file or a directory
     *
     * @param string $cvsentry a line in a CVS/Entries file
     *
     * @return boolean
     * @access private
     */
    function _isCVSFile($cvsentry)
    {
        // make sure we ignore entries that have either been removed or added,
        // but not committed yet
        return $cvsentry{0} == '/' && !strpos($cvsentry, 'dummy timestamp');
    }
}
?>