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
 * Rating vote model
 *
 * @category   Mage
 * @package    Mage_Rating
 *
 * @method Mage_Rating_Model_Resource_Rating_Option_Vote_Collection getResourceCollection()
 * @method string getEntityPkValue()
 * @method int getRatingId()
 * @method $this setRatingOptions(Mage_Rating_Model_Resource_Rating_Option_Collection $options)
 */
class Mage_Rating_Model_Rating_Option_Vote extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('rating/rating_option_vote');
    }
}
