<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Varien
 * @package     Varien_Io
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Filesystem client
 *
 * @category   Varien
 * @package    Varien_Io
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Io_File extends Varien_Io_Abstract
{
    /**
     * Save initial working directory
     *
     * @var string
     */
    protected $_iwd;

    /**
     * Use virtual current working directory for application integrity
     *
     * @var string
     */
    protected $_cwd;

    /**
     * Used to grep ls() output
     *
     * @const
     */
    const GREP_FILES = 'files_only';

    /**
     * Used to grep ls() output
     *
     * @const
     */
    const GREP_DIRS = 'dirs_only';

    /**
     * If this variable is set to TRUE, our library will be able to automaticaly create
     * non-existant directories.
     *
     * @var bool
     * @access protected
     */
    protected $_allowCreateFolders = false;

    /**
     * Stream open file pointer
     *
     * @var resource
     */
    protected $_streamHandler;

    /**
     * Stream mode filename
     *
     * @var string
     */
    protected $_streamFileName;

    /**
     * Stream mode chmod
     *
     * @var string
     */
    protected $_streamChmod;

    /**
     * Lock file
     *
     * @var bool
     */
    protected $_streamLocked = false;

    public function __construct()
    {
        // Initialize shutdown function
        register_shutdown_function(array($this, 'destruct'));
    }

    /**
     * stream close on shutdown
     */
    public function destruct()
    {
        if ($this->_streamHandler) {
            $this->streamClose();
        }
    }

    /**
     * Open file in stream mode
     * For set folder for file use open method
     *
     * @param string $fileName
     * @param string $mode
     * @return bool
     */
    public function streamOpen($fileName, $mode = 'w+', $chmod = 0666)
    {
        $writeableMode = preg_match('#^[wax]#i', $mode);
        if ($writeableMode && !is_writeable($this->_cwd)) {
            throw new Exception('Permission denied for write to ' . $this->getFilteredPath($this->_cwd));
        }

        if (!ini_get('auto_detect_line_endings')) {
            ini_set('auto_detect_line_endings', 1);
        }

        @chdir($this->_cwd);
        $this->_streamHandler = @fopen($fileName, $mode);
        @chdir($this->_iwd);
        if ($this->_streamHandler === false) {
            throw new Exception('Error write to file ' . $this->getFilteredPath($fileName));
        }

        $this->_streamFileName = $fileName;
        $this->_streamChmod = $chmod;
        return true;
    }

    /**
     * Lock file
     *
     * @return bool
     */
    public function streamLock($exclusive = true)
    {
        if (!$this->_streamHandler) {
            return false;
        }
        $this->_streamLocked = true;
        $lock = $exclusive ? LOCK_EX : LOCK_SH;
        return flock($this->_streamHandler, $lock);
    }

    /**
     * Unlock file
     *
     * @return bool
     */
    public function streamUnlock()
    {
        if (!$this->_streamHandler || !$this->_streamLocked) {
            return false;
        }
        $this->_streamLocked = false;
        return flock($this->_streamHandler, LOCK_UN);
    }

    /**
     * Binary-safe file read
     *
     * @param int $length
     * @return string
     */
    public function streamRead($length = 1024)
    {
        if (!$this->_streamHandler) {
            return false;
        }
        if (feof($this->_streamHandler)) {
            return false;
        }
        return @fgets($this->_streamHandler, $length);
    }

    /**
     * Gets line from file pointer and parse for CSV fields
     *
     * @return string
     */
    public function streamReadCsv($delimiter = ',', $enclosure = '"')
    {
        if (!$this->_streamHandler) {
            return false;
        }
        return @fgetcsv($this->_streamHandler, 0, $delimiter, $enclosure);
    }

    /**
     * Binary-safe file write
     *
     * @param string $str
     * @return bool
     */
    public function streamWrite($str)
    {
        if (!$this->_streamHandler) {
            return false;
        }
        return @fwrite($this->_streamHandler, $str);
    }

    /**
     * Format line as CSV and write to file pointer
     *
     * @param array $row
     * @param string $delimiter
     * @param string $enclosure
     * @return bool|int
     */
    public function streamWriteCsv(array $row, $delimiter = ',', $enclosure = '"')
    {
        if (!$this->_streamHandler) {
            return false;
        }

        return @fputcsv($this->_streamHandler, $row, $delimiter, $enclosure);
    }

    /**
     * Close an open file pointer
     * Set chmod on a file
     *
     * @return bool
     */
    public function streamClose()
    {
        if (!$this->_streamHandler) {
            return false;
        }

        if ($this->_streamLocked) {
            $this->streamUnlock();
        }
        @fclose($this->_streamHandler);
        $this->chmod($this->_streamFileName, $this->_streamChmod);
        return true;
    }

    /**
     * Retrieve open file statistic
     *
     * @param string $part the part of statistic
     * @param mixed $default default value for part
     * @return array|bool
     */
    public function streamStat($part = null, $default = null)
    {
        if (!$this->_streamHandler) {
            return false;
        }
        $stat = @fstat($this->_streamHandler);
        if (!is_null($part)) {
            return isset($stat[$part]) ? $stat[$part] : $default;
        }
        return $stat;
    }

    /**
     * Retrieve stream methods exception
     *
     * @return Exception
     */
    public function getStreamException()
    {
        return $this->_streamException;
    }

    /**
     * Open a connection
     *
     * Possible arguments:
     * - path     default current path
     *
     * @param array $args
     * @return boolean
     */
    public function open(array $args=array())
    {
        if (!empty($args['path']) && $this->_allowCreateFolders) {
            $this->checkAndCreateFolder($args['path']);
        }

        $this->_iwd = getcwd();
        $this->cd(!empty($args['path']) ? $args['path'] : $this->_iwd);
        return true;
    }

    /**
     * Used to set {@link _allowCreateFolders} value
     *
     * @param mixed $flag
     * @access public
     * @return void
     */
    public function setAllowCreateFolders($flag)
    {
        $this->_allowCreateFolders = $flag;
        return $this;
    }

    /**
     * Close a connection
     *
     * @return boolean
     */
    public function close()
    {
        return true;
    }

    /**
     * Create a directory
     *
     * @param string $dir
     * @param int $mode
     * @param boolean $recursive
     * @return boolean
     */
    public function mkdir($dir, $mode=0777, $recursive=true)
    {
        if ($this->_cwd) {
            chdir($this->_cwd);
        }

        $result = @mkdir($dir, $mode, $recursive);
        if ($result) {
            @chmod($dir, $mode);
        }
        if ($this->_iwd) {
            chdir($this->_iwd);
        }
        return $result;
    }

    /**
     * Delete a directory
     *
     * @param string $dir
     * @return boolean
     */
    public function rmdir($dir, $recursive = false)
    {
        if ($this->_cwd) {
            @chdir($this->_cwd);
        }
        $result = self::rmdirRecursive($dir, $recursive);
        @chdir($this->_iwd);
        return $result;
    }

    /**
     * Delete a directory recursively
     * @param string $dir
     * @param bool $recursive
     * @return bool
     */
    public static function rmdirRecursive($dir, $recursive = true)
    {
        if ($recursive) {
            if (is_dir($dir)) {
                foreach (scandir($dir) as $item) {
                    if (!strcmp($item, '.') || !strcmp($item, '..')) {
                        continue;
                    }
                    self::rmdirRecursive($dir . "/" . $item, $recursive);
                }
                $result = @rmdir($dir);
            } else {
                $result = @unlink($dir);
            }
        } else {
            $result = @rmdir($dir);
        }
        return $result;
    }

    /**
     * Get current working directory
     *
     * @return string
     */
    public function pwd()
    {
        return $this->_cwd;
    }

    /**
     * Change current working directory
     *
     * @param string $dir
     * @return boolean
     * @throws Exception
     */
    public function cd($dir)
    {
        if( is_dir($dir) ) {
            @chdir($this->_iwd);
            $this->_cwd = realpath($dir);
            return true;
        } else {
            throw new Exception('Unable to list current working directory.');
        }
    }

    /**
     * Read a file to result, file or stream
     *
     * If $dest is null the output will be returned.
     * Otherwise it will be saved to the file or stream and operation result is returned.
     *
     * @param string $filename
     * @param string|resource $dest
     * @return boolean|string
     */
    public function read($filename, $dest=null)
    {
        if (!is_null($dest)) {
            chdir($this->_cwd);
            $result = @copy($filename, $dest);
            chdir($this->_iwd);
            return $result;
        }

        chdir($this->_cwd);
        $result = @file_get_contents($filename);
        chdir($this->_iwd);

        return $result;
    }

    /**
     * Write a file from string, file or stream
     *
     * @param string $filename
     * @param string|resource $src
     * @param int $mode
     *
     * @return int|boolean
     */
    public function write($filename, $src, $mode=null)
    {
        if (!$this->_IsValidSource($src) || !$this->_isFilenameWriteable($filename)) {
            return false;
        }

        $srcIsFile = $this->_checkSrcIsFile($src);
        if ($srcIsFile) {
            $src = realpath($src);
            $result = $this->cp($src, $filename);
        } else {
            $result = $this->filePutContent($filename, $src);
        }

        if (!is_null($mode) && $result !== false) {
            $this->chmod($filename, $mode);
        }

        return $result;
    }

    /**
     * Check source is valid
     *
     * @param string|resource $src
     * @return bool
     */
    protected function _IsValidSource($src)
    {
        // In case of a string
        if (is_string($src)) {
            // If its a file we check for null byte
            // If it's not a valid path, file_exists() will return a falsey value, and the @ will keep it from complaining about the bad string.
            return !(@file_exists($src) && strpos($src, chr(0)) !== false);
        } elseif (is_resource($src)) {
            return true;
        }

        return false;
    }

    /**
     * Check filename is writeable
     * If filename not exist check dirname writeable
     *
     * @param string $filename
     * @throws Varien_Io_Exception
     * @return bool
     */
    protected function _isFilenameWriteable($filename)
    {
        $error = false;
        @chdir($this->_cwd);
        if (file_exists($filename)) {
            if (!is_writeable($filename)) {
                $error = "File '{$this->getFilteredPath($filename)}' isn't writeable";
            }
        } else {
            $folder = dirname($filename);
            if (!is_writable($folder)) {
                $error = "Folder '{$this->getFilteredPath($folder)}' isn't writeable";
            }
        }
        @chdir($this->_iwd);

        if ($error) {
            throw new Varien_Io_Exception($error);
        }
        return true;
    }

    /**
     * Check source is file
     *
     * @param string $src
     * @return bool
     */
    protected function _checkSrcIsFile($src)
    {
        $result = false;
        if (is_string($src) && @is_readable($src) && is_file($src)) {
            $result = true;
        }

        return $result;
    }

    /**
     * File put content wrapper
     *
     * @param string $filename
     * @param srting|resource $src
     *
     * @return int
     */
    public function filePutContent($filename, $src)
    {
        @chdir($this->_cwd);
        $result = @file_put_contents($filename, $src);
        chdir($this->_iwd);

        return $result;
    }

    public function fileExists($file, $onlyFile = true)
    {
        @chdir($this->_cwd);
        $result = file_exists($file);
        if ($result && $onlyFile) {
            $result = is_file($file);
        }
        @chdir($this->_iwd);
        return $result;
    }

    public function isWriteable($path)
    {
        @chdir($this->_cwd);
        $result = is_writeable($path);
        @chdir($this->_iwd);
        return $result;
    }

    public function getDestinationFolder($filepath)
    {
        preg_match('/^(.*[!\/])/', $filepath, $mathces);
        if (isset($mathces[0])) {
            return $mathces[0];
        }
        return false;
    }

    /**
     * Create destination folder
     *
     * @param string $path
     * @return Varien_Io_File
     */
    public function createDestinationDir($path)
    {
        if (!$this->_allowCreateFolders) {
            return false;
        }
        return $this->checkAndCreateFolder($this->getCleanPath($path));
    }

    /**
     * Check and create if not exists folder
     *
     * @param string $folder
     * @param int $mode
     * @throws Exception
     * @return bool
     */
    public function checkAndCreateFolder($folder, $mode = 0777)
    {
        if (is_dir($folder)) {
            return true;
        }
        if (!is_dir(dirname($folder))) {
            $this->checkAndCreateFolder(dirname($folder), $mode);
        }
        if (!is_dir($folder) && !$this->mkdir($folder, $mode)) {
            throw new Exception("Unable to create directory '{$this->getFilteredPath($folder)}'. Access forbidden.");
        }
        return true;
    }

    /**
     * Delete a file
     *
     * @param string $filename
     * @return boolean
     */
    public function rm($filename)
    {
        @chdir($this->_cwd);
        $result = @unlink($filename);
        @chdir($this->_iwd);
        return $result;
    }

    /**
     * Rename or move a directory or a file
     *
     * @param string $src
     * @param string $dest
     * @return boolean
     */
    public function mv($src, $dest)
    {
        chdir($this->_cwd);
        $result = @rename($src, $dest);
        chdir($this->_iwd);
        return $result;
    }

    /**
     * Copy a file
     *
     * @param string $src
     * @param string $dest
     * @return boolean
     */
    public function cp($src, $dest)
    {
        @chdir($this->_cwd);
        $result = @copy($src, $dest);
        @chdir($this->_iwd);
        return $result;
    }

    /**
     * Change mode of a directory or a file
     *
     * @param string $filename
     * @param int $mode
     * @return boolean
     */
    public function chmod($filename, $mode)
    {
        if ($this->_cwd) {
            chdir($this->_cwd);
        }
        $result = @chmod($filename, $mode);
        if ($this->_iwd) {
            chdir($this->_iwd);
        }
        return $result;
    }

    /**
     * Get list of cwd subdirectories and files
     *
     * Suggestions (from moshe):
     * - Use filemtime instead of filectime for performance
     * - Change $grep to $flags and use binary flags
     *   - LS_DIRS  = 1
     *   - LS_FILES = 2
     *   - LS_ALL   = 3
     *
     * @param Varien_Io_File const
     * @access public
     * @return array
     */
    public function ls($grep=null)
    {
        $ignoredDirectories = Array('.', '..');

        if( is_dir($this->_cwd) ) {
            $dir = $this->_cwd;
        } elseif( is_dir($this->_iwd) ) {
            $dir = $this->_iwd;
        } else {
            throw new Exception('Unable to list current working directory.');
        }

        $list = Array();

        if ($dh = opendir($dir)) {
            while (($entry = readdir($dh)) !== false) {
                $list_item = Array();

                $fullpath = $dir . DIRECTORY_SEPARATOR . $entry;

                if( ($grep == self::GREP_DIRS) && (!is_dir($fullpath)) ) {
                    continue;
                } elseif( ($grep == self::GREP_FILES) && (!is_file($fullpath)) ) {
                    continue;
                } elseif( in_array($entry, $ignoredDirectories) ) {
                    continue;
                }

                $list_item['text'] = $entry;
                $list_item['mod_date'] = date ('Y-m-d H:i:s', filectime($fullpath));
                $list_item['permissions'] = $this->_parsePermissions(fileperms($fullpath));
                $list_item['owner'] = $this->_getFileOwner($fullpath);

                if( is_file($fullpath) ) {
                    $pathinfo = pathinfo($fullpath);
                    $list_item['size'] = filesize($fullpath);
                    $list_item['leaf'] = true;
                    if( isset($pathinfo['extension'])
                        && in_array(strtolower($pathinfo['extension']), Array('jpg', 'jpeg', 'gif', 'bmp', 'png'))
                        && $list_item['size'] > 0
                    ) {
                        $list_item['is_image'] = true;
                        $list_item['filetype'] = $pathinfo['extension'];
                    } elseif( $list_item['size'] == 0 ) {
                        $list_item['is_image'] = false;
                        $list_item['filetype'] = 'unknown';
                    } elseif( isset($pathinfo['extension']) ) {
                        $list_item['is_image'] = false;
                        $list_item['filetype'] = $pathinfo['extension'];
                    } else {
                        $list_item['is_image'] = false;
                        $list_item['filetype'] = 'unknown';
                    }
                } else {
                    $list_item['leaf'] = false;
                    $list_item['id'] = $fullpath;
                }

                $list[] = $list_item;
            }
            closedir($dh);
        } else {
            throw new Exception('Unable to list current working directory. Access forbidden.');
        }

        return $list;
    }

    /**
     * Convert integer permissions format into human readable
     *
     * @param integer $mode
     * @access protected
     * @return string
     */
    protected function _parsePermissions($mode)
    {
        if( $mode & 0x1000 )
            $type='p'; /* FIFO pipe */
        else if( $mode & 0x2000 )
            $type='c'; /* Character special */
        else if( $mode & 0x4000 )
            $type='d'; /* Directory */
        else if( $mode & 0x6000 )
            $type='b'; /* Block special */
        else if( $mode & 0x8000 )
            $type='-'; /* Regular */
        else if( $mode & 0xA000 )
            $type='l'; /* Symbolic Link */
        else if( $mode & 0xC000 )
            $type='s'; /* Socket */
        else
            $type='u'; /* UNKNOWN */

        /* Determine permissions */
        $owner['read'] = ($mode & 00400) ? 'r' : '-';
        $owner['write'] = ($mode & 00200) ? 'w' : '-';
        $owner['execute'] = ($mode & 00100) ? 'x' : '-';
        $group['read'] = ($mode & 00040) ? 'r' : '-';
        $group['write'] = ($mode & 00020) ? 'w' : '-';
        $group['execute'] = ($mode & 00010) ? 'x' : '-';
        $world['read'] = ($mode & 00004) ? 'r' : '-';
        $world['write'] = ($mode & 00002) ? 'w' : '-';
        $world['execute'] = ($mode & 00001) ? 'x' : '-';

        /* Adjust for SUID, SGID and sticky bit */
        if( $mode & 0x800 )
            $owner["execute"] = ($owner['execute']=='x') ? 's' : 'S';
        if( $mode & 0x400 )
            $group["execute"] = ($group['execute']=='x') ? 's' : 'S';
        if( $mode & 0x200 )
            $world["execute"] = ($world['execute']=='x') ? 't' : 'T';

        $s=sprintf('%1s', $type);
        $s.=sprintf('%1s%1s%1s', $owner['read'], $owner['write'], $owner['execute']);
        $s.=sprintf('%1s%1s%1s', $group['read'], $group['write'], $group['execute']);
        $s.=sprintf('%1s%1s%1s', $world['read'], $world['write'], $world['execute']);
        return trim($s);
    }

    /**
     * Get file owner
     *
     * @param string $filename
     * @access protected
     * @return string
     */
    protected function _getFileOwner($filename)
    {
        if( !function_exists('posix_getpwuid') ) {
            return 'n/a';
        }

        $owner     = posix_getpwuid(fileowner($filename));
        $groupinfo = posix_getgrnam(filegroup($filename));

        return $owner['name'] . ' / ' . $groupinfo;
    }

    public function dirsep()
    {
        return DIRECTORY_SEPARATOR;
    }

    public function dirname($file)
    {
        return $this->getCleanPath(dirname($file));
    }

    public function getStreamHandler()
    {
        return $this->_streamHandler;
    }
}
