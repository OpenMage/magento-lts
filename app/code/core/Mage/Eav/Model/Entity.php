<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Eav
 */

/**
 * EAV entity model
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity extends Mage_Eav_Model_Entity_Abstract
{
    public const DEFAULT_ENTITY_MODEL      = 'eav/entity';
    public const DEFAULT_ATTRIBUTE_MODEL   = 'eav/entity_attribute';
    public const DEFAULT_BACKEND_MODEL     = 'eav/entity_attribute_backend_default';
    public const DEFAULT_FRONTEND_MODEL    = 'eav/entity_attribute_frontend_default';
    public const DEFAULT_SOURCE_MODEL      = 'eav/entity_attribute_source_config';

    public const DEFAULT_ENTITY_TABLE      = 'eav/entity';
    public const DEFAULT_ENTITY_ID_FIELD   = 'entity_id';
    public const DEFAULT_VALUE_TABLE_PREFIX = 'eav/entity_attribute';

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setConnection($resource->getConnection('eav_read'));
    }
}
