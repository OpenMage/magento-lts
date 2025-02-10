<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 */
if ($attrId = $this->getAttribute('customer', 'birthdate', 'attribute_id')) {
    $this->getConnection()->delete($this->getTable('eav_attribute'), 'attribute_id=' . $attrId);
}

$this->installEntities();
