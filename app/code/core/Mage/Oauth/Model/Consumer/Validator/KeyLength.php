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
class Mage_Oauth_Model_Consumer_Validator_KeyLength extends Mage_Core_Helper_Validate_Abstract
{
    /**
     * Key name
     */
    protected string $name = 'Key';

    protected ?int $max = null;

    protected ?int $min = null;

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageVariables = [
        'min'  => 'min',
        'max'  => 'max',
        'name' => 'name',
    ];

    /**
     * Sets validator options
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (isset($options['length'])) {
            $this->min = $options['length'];
            $this->max = $options['length'];
        }

        if (isset($options['name'])) {
            $this->name = $options['name'];
        }
    }

    /**
     * Defined by Mage_Validation_Interface
     *
     * Returns true if and only if the string length of $value is at least the min option and
     * no greater than the max option (when the max option is not null).
     *
     * @param  string              $value
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        /** @var Mage_Core_Helper_Validate $validator */
        $validator = Mage::helper('core/validate');
        $violation = $validator->validateLength(
            value: $value,
            min: $this->min,
            max: $this->max,
            exactMessage: $this->createMessageFromTemplate(
                Mage::helper('oauth')->__("%name% '%value%' should have exactly %min% symbols."),
            ),
            minMessage: $this->createMessageFromTemplate(
                Mage::helper('oauth')->__("%name% '%value%' is too short. It must has length %min% symbols."),
            ),
            maxMessage: $this->createMessageFromTemplate(
                Mage::helper('oauth')->__("%name% '%value%' is too long. It must has length %max% symbols."),
            ),
        );

        if ($violation->count() > 0) {
            throw new Mage_Core_Exception($violation->get(0)->getMessage());
        }

        return true;
    }

    /**
     * Set key name
     *
     * @param  string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get key name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
