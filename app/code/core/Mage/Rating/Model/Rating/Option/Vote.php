<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rating
 */

/**
 * Rating vote model
 *
 * @package    Mage_Rating
 *
 * @method Mage_Rating_Model_Resource_Rating_Option_Vote            _getResource()
 * @method Mage_Rating_Model_Resource_Rating_Option_Vote_Collection getCollection()
 * @method Mage_Rating_Model_Resource_Rating_Option_Vote            getResource()
 * @method Mage_Rating_Model_Resource_Rating_Option_Vote_Collection getResourceCollection()
 * @method $this                                                    setRatingOptions(Mage_Rating_Model_Resource_Rating_Option_Collection $options)
 */
class Mage_Rating_Model_Rating_Option_Vote extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('rating/rating_option_vote');
    }

public function getEntityPkValue(): string
    {
        return (string) $this->_getData('entity_pk_value');
    }

    public function getRatingId(): int
    {
        return (int) $this->_getData('rating_id');
    }

    public function setEntityPkValue(string $value): static
    {
        return $this->setData('entity_pk_value', $value);
    }

    public function setRatingId(int $value): static
    {
        return $this->setData('rating_id', $value);
    }
}
