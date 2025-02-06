<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Customer
 */

if ($attrId = $this->getAttribute('customer', 'birthdate', 'attribute_id')) {
    $this->getConnection()->delete($this->getTable('eav_attribute'), 'attribute_id=' . $attrId);
}

$this->installEntities();
