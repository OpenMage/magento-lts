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
 * @category    Unserialize
 * @package     Unserialize_Reader
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Unserialize_Reader_ArrKey
 */
class Unserialize_Reader_ArrKey
{
    /**
     * @var int
     */
    protected $_status;

    /**
     * @object
     */
    protected $_reader;

    const NOT_STARTED = 1;
    const READING_KEY = 2;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->_status = self::NOT_STARTED;
    }

    /**
     * @param string $char
     * @param string $prevChar
     * @return mixed|null
     * @throws Exception
     */
    public function read($char, $prevChar)
    {
        if ($this->_status == self::NOT_STARTED) {
            switch ($char) {
                case Unserialize_Parser::TYPE_STRING:
                    $this->_reader = new Unserialize_Reader_Str();
                    $this->_status = self::READING_KEY;
                    break;
                case Unserialize_Parser::TYPE_INT:
                    $this->_reader = new Unserialize_Reader_Int();
                    $this->_status = self::READING_KEY;
                    break;
                default:
                    throw new Exception('Unsupported data type ' . $char);
            }
        }

        if ($this->_status == self::READING_KEY) {
            $key = $this->_reader->read($char, $prevChar);
            if (!is_null($key)) {
                return $key;
            }
        }
        return null;
    }
}
