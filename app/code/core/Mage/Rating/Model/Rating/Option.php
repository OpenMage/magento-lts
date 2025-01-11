<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Rating
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating option model
 *
 * @category   Mage
 * @package    Mage_Rating
 *
 * @method Mage_Rating_Model_Resource_Rating_Option_Collection getResourceCollection()
 * @method Mage_Rating_Model_Resource_Rating_Option _getResource()
 * @method Mage_Rating_Model_Resource_Rating_Option getResource()
 * @method string getCode()
 * @method $this setCode(string $value)
 * @method int getDoUpdate()
 * @method $this setDoUpdate(int $value)
 * @method string getEntityPkValue()
 * @method $this setEntityPkValue(string $value)
 * @method $this setOptionId(int $value)
 * @method int getPosition()
 * @method $this setPosition(int $value)
 * @method int getRatingId()
 * @method $this setRatingId(int $value)
 * @method int getReviewId()
 * @method $this setReviewId(int $value)
 * @method int getValue()
 * @method $this setValue(int $value)
 * @method int getVoteId()
 * @method $this setVoteId(int $value)
 */
class Mage_Rating_Model_Rating_Option extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('rating/rating_option');
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function addVote()
    {
        $this->getResource()->addVote($this);
        return $this;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->setOptionId($id);
        return $this;
    }
}
