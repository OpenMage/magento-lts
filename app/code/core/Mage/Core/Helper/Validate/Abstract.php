<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Validation
 */

/**
 * @package    Mage_Validation
 */
abstract class Mage_Core_Helper_Validate_Abstract implements Mage_Core_Helper_Validate_Interface
{
    /**
     * The value to be validated
     *
     * @var mixed
     */
    protected $_value;

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageVariables = [];

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [];

    /**
     * Array of validation failure messages
     *
     * @var array
     */
    protected $_messages = [];

    /**
     * Returns array of validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Returns an array of the names of variables that are used in constructing validation failure messages
     */
    public function getMessageVariables(): array
    {
        return array_keys($this->_messageVariables);
    }

    /**
     * Returns the message templates from the validator
     */
    public function getMessageTemplates(): array
    {
        return $this->_messageTemplates;
    }

    /**
     * Sets the validation failure message template for a particular key
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setMessage(string $messageString, ?string $messageKey = null)
    {
        if ($messageKey === null) {
            $keys = array_keys($this->_messageTemplates);
            foreach ($keys as $key) {
                $this->setMessage($messageString, $key);
            }

            return $this;
        }

        if (!isset($this->_messageTemplates[$messageKey])) {
            throw new Mage_Core_Exception("No message template exists for key '$messageKey'");
        }

        $this->_messageTemplates[$messageKey] = $messageString;
        return $this;
    }

    /**
     * Sets validation failure message templates given as an array, where the array keys are the message keys,
     * and the array values are the message template strings.
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setMessages(array $messages)
    {
        foreach ($messages as $key => $message) {
            $this->setMessage($message, $key);
        }

        return $this;
    }

    /**
     * Magic function returns the value of the requested property, if and only if it is the value or a
     * message variable.
     *
     * @return mixed
     * @throws Mage_Core_Exception
     */
    public function __get(string $property)
    {
        if ($property == 'value') {
            return $this->_value;
        }

        if (array_key_exists($property, $this->_messageVariables)) {
            return $this->{$this->_messageVariables[$property]};
        }

        throw new Mage_Core_Exception("No property exists by the name '$property'");
    }

    /**
     * Constructs and returns a validation failure message with the given message key and value.
     *
     * Returns null if and only if $messageKey does not correspond to an existing template.
     */
    final protected function _createMessage(string $messageKey, array|object|string $value): ?string
    {
        if (!isset($this->_messageTemplates[$messageKey])) {
            return null;
        }

        $message = $this->_messageTemplates[$messageKey];

        if (is_object($value)) {
            if (!in_array('__toString', get_class_methods($value))) {
                $value = $value::class . ' object';
            } else {
                $value = $value->__toString();
            }
        } elseif (is_array($value)) {
            $value = $this->_implodeRecursive($value);
        } else {
            $value = implode('', (array) $value);
        }

        $message = str_replace('%value%', $value, $message);
        foreach ($this->_messageVariables as $ident => $property) {
            $message = str_replace(
                "%$ident%",
                implode(' ', (array) $this->$property),
                $message,
            );
        }

        return $message;
    }

    public function createMessageFromTemplate(string $template): string
    {
        $message = str_replace('%value%', $this->_value, $template);
        foreach ($this->_messageVariables as $ident => $property) {
            $message = str_replace(
                "%$ident%",
                implode(' ', (array) $this->$property),
                $message,
            );
        }

        return $message;
    }

    /**
     * Joins elements of a multidimensional array
     */
    final protected function _implodeRecursive(array $pieces): string
    {
        $values = [];
        foreach ($pieces as $item) {
            if (is_array($item)) {
                $values[] = $this->_implodeRecursive($item);
            } else {
                $values[] = $item;
            }
        }

        return implode(', ', $values);
    }

    final protected function _error(?string $messageKey, ?string $value = null): void
    {
        if ($messageKey === null) {
            $keys = array_keys($this->_messageTemplates);
            $messageKey = current($keys);
        }

        if ($value === null) {
            $value = $this->_value;
        }

        $this->_messages[$messageKey] = $this->_createMessage($messageKey, $value);
    }

    /**
     * Sets the value to be validated and clears the messages and errors arrays
     */
    final protected function _setValue(mixed $value): void
    {
        $this->_value    = $value;
        $this->_messages = [];
    }
}
