<?php
/**
 * PEAR_FTP
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
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2006 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id: FTP.php,v 1.11 2006/11/19 21:32:14 cellog Exp $
 * @link       http://pear.php.net/package/PEAR
 * @since      File available since Release 1.4.0a1
 */

/**
 * FTP class used for PEAR's remote installation feature
 * @category   pear
 * @package    PEAR
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  1997-2006 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 0.3.1
 * @link       http://pear.php.net/package/PEAR
 * @since      Class available since Release 1.4.0a1
 */
class PEAR_FTP extends PEAR
{
    /**
     * @var array
     * @access private
     */
    protected $_parsed;

    /**
     * URI to prepend to all paths
     * @var string
     */
    protected $_uri;

    /**
     * Value to prepend to relative paths
     * @var string|null
     */
    protected $_pathprepend = null;
    /**
     * Resource needed for ssh2 action
     * @var resource
     */
    protected $_ssh2connection;

    /**
     * @param string full url to remote config file
     * @return true|PEAR_Error
     */
    public function init($url = null)
    {
        if ($url !== null) {
            $this->_parsed = @parse_url($url);
        } else {
            return;
        }
        if (!isset($this->_parsed['host'])) {
            return PEAR::raiseError('No FTP Host specified');
        }
        if (!isset($this->_parsed['scheme'])) {
            return PEAR::raiseError('No FTP Scheme (ftp/ftps/ssh2.sftp) specified');
        }
        if (!in_array($this->_parsed['scheme'], array('ftp', 'ftps', 'ssh2.sftp'), true)) {
            return PEAR::raiseError('Only ftp/ftps/ssh2.sftp is supported for remote config');
        }
        if (!in_array($this->_parsed['scheme'], stream_get_wrappers(), true)) {
            if ($this->_parsed['scheme'] == 'ftps' && !extension_loaded('openssl')) {
                if (OS_WINDOWS) {
                    return PEAR::raiseError('In order to use ftps, you must ' .
                        'put "extension=php_openssl.dll" into php.ini and ' .
                        'copy libeay32.dll and ssleay32.dll to \windows\system32');
                } else {
                    return PEAR::raiseError('In order to use ftps, you must ' .
                        'enable the "openssl" extension in php.ini');
                }
            }
            if ($this->_parsed['scheme'] == 'ssh2.sftp') {
                if (OS_WINDOWS) {
                    return PEAR::raiseError('In order to use ssh2.sftp, you must ' .
                        'enable the "php_ssh2.dll" extension in php.ini');
                } else {
                    return PEAR::raiseError('In order to use ssh2.sftp, you must ' .
                        'install ssh2 via "pecl install ssh2" and enable the "ssh2" ' .
                        'extension in php.ini');
                }
            }
            return PEAR::raiseError('Your PHP does not support this wrapper: ' .
                $this->_parsed['scheme']);
        }
        if (!isset($this->_parsed['path'])) {
            return PEAR::raiseError('No FTP file path to remote config specified');
        }
        $host = $this->_parsed['host'];
        $pass = isset($this->_parsed['pass']) ? ':' . $this->_parsed['pass'] : '';
        $user = isset($this->_parsed['user']) ? $this->_parsed['user'] . "$pass@" : '';
        $port = isset($this->_parsed['port']) ? ':' . $this->_parsed['port'] : '';
        $path = dirname($this->_parsed['path']);
        if ($path[strlen($path) - 1] == '/') {
            $path = substr($path, 0, strlen($path) - 1);
        }
        if ($this->_parsed['scheme'] == 'ssh2.sftp') {
            $connection = @ssh2_connect($host, isset($this->_parsed['port']) ?
                $this->_parsed['port'] : 22);
            if (!$connection) {
                return PEAR::raiseError('Unable to connect to remote host: ' .
                    $php_errormsg);
            }
            $a = @ssh2_auth_password($connection, urldecode($this->_parsed['user']),
                urldecode($this->_parsed['pass']));
            if (!$a) {
                return PEAR::raiseError('Unable to authenticate to remote host: ' .
                    $php_errormsg);
            }
            
            $sftp = @ssh2_sftp($connection);
            if (!$sftp) {
                return PEAR::raiseError('Unable to initiate sftp session: ' .
                    $php_errormsg);
            }
            $this->_uri = $this->_parsed['scheme'] . '://' . $sftp;
            $this->_pathprepend = $path;
            $this->_ssh2connection = $sftp;
        } else {
            $this->_uri = $this->_parsed['scheme'] . '://' . $user . $host;
            $this->_pathprepend = $path;
        }
        return true;
    }

    /**
     * This works similar to the mkdir-command on your local machine. You can either give
     * it an absolute or relative path. The relative path will be completed with the actual
     * selected server-path. (see: pwd())
     *
     * @access  public
     * @param   string $dir       relative dir-path
     * @param   bool   $recursive (optional) Create all needed directories
     * @return  mixed             True on success, otherwise PEAR::Error
     * @see     NET_FTP_ERR_CREATEDIR_FAILED
     */
    public function mkdir($dir, $recursive = false)
    {
        if (method_exists($this, '_testftp_mkdir')) {
            $res = $this->_testftp_mkdir($this->_prepend($dir), 0755, $recursive);
        } else {
            $res = @mkdir($this->_prepend($dir), 0755, $recursive);
        }
        if (!$res) {
            return $this->raiseError("Creation of '$dir' failed");
        } else {
            return true;
        }
    }

    /**
     * @param string full path to local file
     * @param string full path to remote file
     */
    public function installFile($local, $remote)
    {
        $this->pushErrorHandling(PEAR_ERROR_RETURN);
        $this->mkdir(dirname($remote), true);
        $this->popErrorHandling();
        return $this->put($local, $remote, true);
    }

    /**
     * Retrieve a file from the remote server
     *
     * @param string $relfile relative path of the remote file
     * @param string $localfile full local path to save the file in
     */
    public function get($relfile, $localfile, $binary = true)
    {
        $local = @fopen($localfile, 'w' . ($binary ? 'b' : ''));
        $remote = @fopen($this->_prepend($relfile), 'r' . ($binary ? 'b' : ''));
        if (!$remote) {
            return PEAR::raiseError('Could not open remote file ' . 
                $this->_prepend($relfile) . ' for retrieval');
        }
        if (!$local) {
            return PEAR::raiseError('Could not open local file ' . 
                $this->_prepend($relfile) . ' for retrieving remote file ' .
                $this->_prepend($relfile));
        }
        $ret = @stream_copy_to_stream($remote, $local);
        if ($local) {
            @fclose($local);
        }
        if ($remote) {
            @fclose($remote);
        }
        return $ret ? $ret : PEAR::raiseError('FTP get of ' . $this->_prepend($relfile) .
            ' failed');
    }

    public function put($localfile, $remotefile, $overwrite = false, $binary = true)
    {
        // sometimes we get the wrong size for files, this might fix it
        clearstatcache();
        $local = @fopen($localfile, 'r' . ($binary ? 'b' : ''));
        if (!$local) {
            return PEAR::raiseError('Could not put local file ' . $localfile .
                'opening the file failed');
        }
        $opts = array('ftp' => array('overwrite' => $overwrite));
        $context = stream_context_create($opts);
        $remote = @fopen($this->_prepend($remotefile), 'w' . ($binary ? 'b' : ''), false,
            $context);
        if (!$remote) {
            return PEAR::raiseError('Could not open remote file ' .
                $this->_prepend($remotefile) . ' for saving a copy of local file ' . $localfile);
        }
        $ret = @stream_copy_to_stream($local, $remote);
        if ($local) {
            @fclose($local);
        }
        if ($remote) {
            @fclose($remote);
        }
        return $ret ? $ret : PEAR::raiseError('FTP put of ' . $this->_prepend($remotefile) .
            ' failed');
    }

    public function disconnect()
    {
        // does nothing here
    }

    public function rm($path, $recursive = false)
    {
        if (@unlink($this->_prepend($path))) {
            return true;
        }
        return PEAR::raiseError('rm of ' . $this->_prepend($path) . ' failed');
    }

    /**
     * Return a ftp URI for usage with filesystem functions directly
     *
     * @param string $path relative path to the on the FTP server
     * @return string full path to the ftp server including ftp[s]://...
     */
    private function _prepend($path)
    {
        if ($path[0] == '/') {
            return $this->_uri . $path;
        } else {
            return $this->_uri . $this->_pathprepend . '/' . $path;
        }
    }
}
?>