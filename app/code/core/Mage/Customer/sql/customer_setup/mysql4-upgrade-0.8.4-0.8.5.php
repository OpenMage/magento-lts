<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

if ($attrId = $this->getAttribute('customer', 'birthdate', 'attribute_id')) {
    $this->getConnection()->delete($this->getTable('eav_attribute'), 'attribute_id=' . $attrId);
}

$this->installEntities();
