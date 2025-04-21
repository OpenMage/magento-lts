<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * @package    Mage_Core
 */
abstract class Mage_Core_Model_Resource_Type_Abstract
{
    /**
     * Name
     *
     * @var String
     */
    protected $_name = '';

    /**
     * Entity class
     *
     * @var String
     */
    protected $_entityClass = 'Mage_Core_Model_Resource_Entity_Abstract';

    /**
     * Retrieve entity type
     *
     * @return String
     */
    public function getEntityClass()
    {
        return $this->_entityClass;
    }

    /**
     * Set name
     *
     * @param String $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Retrieve name
     *
     * @return String
     */
    public function getName()
    {
        return $this->_name;
    }
}
