<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Io
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * FTP client
 *
 * @category   Varien
 * @package    Varien_Io
 */
class Varien_Io_Ftp extends Varien_Io_Abstract
{
    public const ERROR_EMPTY_HOST = 1;
    public const ERROR_INVALID_CONNECTION = 2;
    public const ERROR_INVALID_LOGIN = 3;
    public const ERROR_INVALID_PATH = 4;
    public const ERROR_INVALID_MODE = 5;
    public const ERROR_INVALID_DESTINATION = 6;
    public const ERROR_INVALID_SOURCE = 7;

    /**
     * Connection config
     *
     * @var array
     */
    protected $_config;

    /**
     * An FTP connection
     *
     * @var resource
     */
    protected $_conn;

    /**
     * Error code
     *
     * @var int
     */
    protected $_error;

    protected $_tmpFilename;

    /**
     * Open a connection
     *
     * Possible argument keys:
     * - host        required
     * - port        default 21
     * - timeout     default 90
     * - user        default anonymous
     * - password    default empty
     * - ssl         default false
     * - passive     default false
     * - path        default empty
     * - file_mode   default FTP_BINARY
     *
     * @return boolean
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    public function open(array $args = [])
    {
        if (empty($args['host'])) {
            $this->_error = self::ERROR_EMPTY_HOST;
            throw new Varien_Io_Exception('Empty host specified');
        }

        if (empty($args['port'])) {
            $args['port'] = 21;
        }

        if (empty($args['user'])) {
            $args['user'] = 'anonymous';
            $args['password'] = 'anonymous@noserver.com';
        }

        if (empty($args['password'])) {
            $args['password'] = '';
        }

        if (empty($args['timeout'])) {
            $args['timeout'] = 90;
        }

        if (empty($args['file_mode'])) {
            $args['file_mode'] = FTP_BINARY;
        }

        $this->_config = $args;

        if (empty($this->_config['ssl'])) {
            // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,Ecg.Security.ForbiddenFunction.Found
            $this->_conn = @ftp_connect($this->_config['host'], $this->_config['port'], $this->_config['timeout']);
        } else {
            // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,Ecg.Security.ForbiddenFunction.Found
            $this->_conn = @ftp_ssl_connect($this->_config['host'], $this->_config['port'], $this->_config['timeout']);
        }
        if (!$this->_conn) {
            $this->_error = self::ERROR_INVALID_CONNECTION;
            throw new Varien_Io_Exception('Could not establish FTP connection, invalid host or port');
        }

        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,Ecg.Security.ForbiddenFunction.Found
        if (!@ftp_login($this->_conn, $this->_config['user'], $this->_config['password'])) {
            $this->_error = self::ERROR_INVALID_LOGIN;
            $this->close();
            throw new Varien_Io_Exception('Invalid user name or password');
        }

        if (!empty($this->_config['path'])) {
            // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,Ecg.Security.ForbiddenFunction.Found
            if (!@ftp_chdir($this->_conn, $this->_config['path'])) {
                $this->_error = self::ERROR_INVALID_PATH;
                $this->close();
                throw new Varien_Io_Exception('Invalid path');
            }
        }

        if (!empty($this->_config['passive'])) {
            // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,Ecg.Security.ForbiddenFunction.Found
            if (!@ftp_pasv($this->_conn, true)) {
                $this->_error = self::ERROR_INVALID_MODE;
                $this->close();
                throw new Varien_Io_Exception('Invalid file transfer mode');
            }
        }

        return true;
    }

    /**
     * Close a connection
     *
     * @return boolean
     *
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    public function close()
    {
        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,Ecg.Security.ForbiddenFunction.Found
        return @ftp_close($this->_conn);
    }

    /**
     * Create a directory
     *
     * @todo implement $mode and $recursive
     * @param string $dir
     * @param int $mode
     * @param boolean $recursive
     * @return boolean
     *
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed
    public function mkdir($dir, $mode = 0777, $recursive = true)
    {
        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,Ecg.Security.ForbiddenFunction.Found
        return @ftp_mkdir($this->_conn, $dir);
    }

    /**
     * Delete a directory
     *
     * @param string $dir
     * @return boolean
     *
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed
    public function rmdir($dir, $recursive = false)
    {
        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,Ecg.Security.ForbiddenFunction.Found
        return @ftp_rmdir($this->_conn, $dir);
    }

    /**
     * Get current working directory
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    public function pwd()
    {
        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,Ecg.Security.ForbiddenFunction.Found
        return @ftp_pwd($this->_conn);
    }

    /**
     * Change current working directory
     *
     * @param string $dir
     * @return boolean
     *
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    public function cd($dir)
    {
        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,Ecg.Security.ForbiddenFunction.Found
        return @ftp_chdir($this->_conn, $dir);
    }

    /**
     * Read a file to result, file or stream
     *
     * @param string $filename
     * @param string|resource|null $dest destination file name, stream, or if null will return file contents
     * @return bool|string
     */
    public function read($filename, $dest = null)
    {
        if (is_string($dest)) {
            // phpcs:ignore Ecg.Security.ForbiddenFunction.Found
            $result = ftp_get($this->_conn, $dest, $filename, $this->_config['file_mode']);
        } else {
            if (is_resource($dest)) {
                $stream = $dest;
            } elseif (is_null($dest)) {
                $stream = tmpfile();
            } else {
                $this->_error = self::ERROR_INVALID_DESTINATION;
                return false;
            }

            // phpcs:ignore Ecg.Security.ForbiddenFunction.Found
            $result = ftp_fget($this->_conn, $stream, $filename, $this->_config['file_mode']);

            if (is_null($dest)) {
                fseek($stream, 0);
                $result = '';
                // phpcs:ignore Generic.CodeAnalysis.ForLoopWithTestFunctionCall.NotAllowed,Ecg.Security.ForbiddenFunction.Found
                for ($result = ''; $s = fread($stream, 4096); $result .= $s);
                fclose($stream);
            }
        }
        return $result;
    }

    /**
     * Write a file from string, file or stream
     *
     * @param string $filename
     * @param string|resource $src filename, string data or source stream
     * @return int|boolean
     *
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed
    public function write($filename, $src, $mode = null)
    {
        // phpcs:ignore Ecg.Security.ForbiddenFunction.Found
        if (is_string($src) && is_readable($src)) {
            // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,Ecg.Security.ForbiddenFunction.Found
            return @ftp_put($this->_conn, $filename, $src, $this->_config['file_mode']);
        } else {
            if (is_string($src)) {
                $stream = tmpfile();
                fputs($stream, $src);
                fseek($stream, 0);
            } elseif (is_resource($src)) {
                $stream = $src;
            } else {
                $this->_error = self::ERROR_INVALID_SOURCE;
                return false;
            }

            // phpcs:ignore Ecg.Security.ForbiddenFunction.Found
            $result = ftp_fput($this->_conn, $filename, $stream, $this->_config['file_mode']);
            if (is_string($src)) {
                fclose($stream);
            }
            return $result;
        }
    }

    /**
     * Delete a file
     *
     * @param string $filename
     * @return boolean
     *
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    public function rm($filename)
    {
        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,Ecg.Security.ForbiddenFunction.Found
        return @ftp_delete($this->_conn, $filename);
    }

    /**
     * Rename or move a directory or a file
     *
     * @param string $src
     * @param string $dest
     * @return boolean
     *
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    public function mv($src, $dest)
    {
        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,Ecg.Security.ForbiddenFunction.Found
        return @ftp_rename($this->_conn, $src, $dest);
    }

    /**
     * Change mode of a directory or a file
     *
     * @param string $filename
     * @param int $mode
     * @return boolean
     *
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    public function chmod($filename, $mode)
    {
        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,Ecg.Security.ForbiddenFunction.Found
        return @ftp_chmod($this->_conn, $mode, $filename);
    }

    /**
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClass
    public function ls($grep = null)
    {
        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged,Ecg.Security.ForbiddenFunction.Found
        $ls = @ftp_nlist($this->_conn, '.');

        $list = [];
        foreach ($ls as $file) {
            $list[] = [
                'text' => $file,
                'id' => $this->pwd() . '/' . $file,
            ];
        }

        return $list;
    }

    protected function _tmpFilename($new = false)
    {
        if ($new || !$this->_tmpFilename) {
            // phpcs:ignore Ecg.Security.ForbiddenFunction.Found
            $this->_tmpFilename = tempnam(md5(uniqid(rand(), true)), '');
        }
        return $this->_tmpFilename;
    }
}
