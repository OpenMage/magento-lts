<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * Request content interpreter XML adapter
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Request_Interpreter_Xml implements Mage_Api2_Model_Request_Interpreter_Interface
{
    /**
     * Default name for item of non-associative array
     */
    public const ARRAY_NON_ASSOC_ITEM_NAME = 'data_item';

    /**
     * Load error string.
     *
     * Is null if there was no error while loading
     *
     * @var string
     */
    protected $_loadErrorStr = null;

    /**
     * Parse Request body into array of params
     *
     * @param string $body  Posted content from request
     * @return array
     * @throws Exception|Mage_Api2_Exception
     */
    public function interpret($body)
    {
        if (!is_string($body)) {
            throw new Exception(sprintf('Invalid data type "%s". String expected.', gettype($body)));
        }
        $body = str_contains($body, '<?xml') ? $body : '<?xml version="1.0"?>' . PHP_EOL . $body;

        set_error_handler([$this, '_loadErrorHandler']); // Warnings and errors are suppressed
        $config = simplexml_load_string($body);
        restore_error_handler();

        // Check if there was a error while loading file
        if ($this->_loadErrorStr !== null) {
            throw new Mage_Api2_Exception('Decoding error.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        return $this->_toArray($config);
    }

    /**
     * Returns an associativearray from a SimpleXMLElement.
     *
     * @param  SimpleXMLElement $xmlObject Convert a SimpleXMLElement into an array
     * @return array
     */
    protected function _toArray(SimpleXMLElement $xmlObject)
    {
        $config = [];
        // Search for parent node values
        if (count($xmlObject->attributes()) > 0) {
            foreach ($xmlObject->attributes() as $key => $value) {
                $value = (string) $value;
                if (array_key_exists($key, $config)) {
                    if (!is_array($config[$key])) {
                        $config[$key] = [$config[$key]];
                    }
                    $config[$key][] = $value;
                } else {
                    $config[$key] = $value;
                }
            }
        }

        // Search for children
        if (count($xmlObject->children()) > 0) {
            foreach ($xmlObject->children() as $key => $value) {
                if (count($value->children()) > 0) {
                    $value = $this->_toArray($value);
                } elseif (count($value->attributes()) > 0) {
                    $attributes = $value->attributes();
                    if (isset($attributes['value'])) {
                        $value = (string) $attributes['value'];
                    } else {
                        $value = $this->_toArray($value);
                    }
                } else {
                    $value = (string) $value;
                }
                if (array_key_exists($key, $config)) {
                    if (!is_array($config[$key]) || !array_key_exists(0, $config[$key])) {
                        $config[$key] = [$config[$key]];
                    }
                    $config[$key][] = $value;
                } elseif (self::ARRAY_NON_ASSOC_ITEM_NAME != $key) {
                    $config[$key] = $value;
                } else {
                    $config[] = $value;
                }
            }
        }

        return $config;
    }

    /**
     * Handle any errors from load xml
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     */
    protected function _loadErrorHandler($errno, $errstr, $errfile, $errline)
    {
        if ($this->_loadErrorStr === null) {
            $this->_loadErrorStr = $errstr;
        } else {
            $this->_loadErrorStr .= (PHP_EOL . $errstr);
        }
    }
}
