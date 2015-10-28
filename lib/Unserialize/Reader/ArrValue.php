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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Unserialize
 * @copyright   Copyright (c) 2015 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Unserialize_Reader_ArrValue
 */
class Unserialize_Reader_ArrValue
{

    /**
     * @var
     */
    public $key;

    /**
     * @var int
     */
    protected $_status;

    /**
     * @object
     */
    protected $_reader;

    const NOT_STARTED = 1;
    const READING_VALUE = 2;

    public function __construct($key)
    {
        $this->_status = self::NOT_STARTED;
        $this->key = $key;
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
                    $this->_status = self::READING_VALUE;
                    break;
                case Unserialize_Parser::TYPE_ARRAY:
                    $this->_reader = new Unserialize_Reader_Arr();
                    $this->_status = self::READING_VALUE;
                    break;
                case Unserialize_Parser::TYPE_INT:
                    $this->_reader = new Unserialize_Reader_Int();
                    $this->_status = self::READING_VALUE;
                    break;
                case Unserialize_Parser::TYPE_BOOL:
                    $this->_reader = new Unserialize_Reader_Bool();
                    $this->_status = self::READING_VALUE;
                    break;
                case Unserialize_Parser::TYPE_DOUBLE:
                    $this->_reader = new Unserialize_Reader_Dbl();
                    $this->_status = self::READING_VALUE;
                    break;
                default:
                    throw new Exception('Unsupported data type ' . $char);
            }
        }

        if ($this->_status == self::READING_VALUE) {
            $value = $this->_reader->read($char, $prevChar);
            if (!is_null($value)) {
                return $value;
            }
        }
        return null;
    }
}
