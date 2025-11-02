<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

/**
 * Validate OAuth keys
 *
 * @package    Mage_Oauth
 */
class Mage_Oauth_Model_Consumer_Validator_KeyLength extends Zend_Validate_StringLength
{
    /**
     * Key name
     *
     * @var string
     */
    protected $_name = 'Key';

    /**
     * Sets validator options
     *
     * @param  array|int|Zend_Config $options
     */
    public function __construct($options = [])
    {
        $args = func_get_args();
        if (!is_array($options)) {
            $options = $args;
            if (!isset($options[1])) {
                $options[1] = 'utf-8';
            }

            parent::__construct($options[0], $options[0], $options[1]);
            return;
        } else {
            if (isset($options['length'])) {
                $options['max']
                = $options['min'] = $options['length'];
            }

            if (isset($options['name'])) {
                $this->_name = $options['name'];
            }
        }

        parent::__construct($options);
    }

    /**
     * Init validation failure message template definitions
     *
     * @return $this
     */
    protected function _initMessageTemplates()
    {
        $_messageTemplates[self::TOO_LONG]
            = Mage::helper('oauth')->__("%name% '%value%' is too long. It must has length %min% symbols.");
        $_messageTemplates[self::TOO_SHORT]
            = Mage::helper('oauth')->__("%name% '%value%' is too short. It must has length %min% symbols.");

        return $this;
    }

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageVariables = [
        'min'  => '_min',
        'max'  => '_max',
        'name' => '_name',
    ];

    /**
     * Set length
     *
     * @param int $length
     * @return $this
     */
    public function setLength($length)
    {
        parent::setMax($length);
        parent::setMin($length);
        return $this;
    }

    /**
     * Set length
     *
     * @return int
     */
    public function getLength()
    {
        return parent::getMin();
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if the string length of $value is at least the min option and
     * no greater than the max option (when the max option is not null).
     *
     * @param  string $value
     * @return bool
     */
    public function isValid($value)
    {
        $result = parent::isValid($value);
        if (!$result && isset($this->_messages[self::INVALID])) {
            throw new Exception($this->_messages[self::INVALID]);
        }

        return $result;
    }

    /**
     * Set key name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Get key name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
}
