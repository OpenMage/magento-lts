<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/** @var Mage_Core_Model_Resource_Setup $this */

$days = Mage::app()->getLocale()->getTranslationList('days');
$days = array_keys($days['format']['wide']);
foreach ($days as $key => $value) {
    $days[$key] = ucfirst($value);
}

$select = $this->getConnection()
    ->select()
    ->from($this->getTable('core/config_data'), ['config_id', 'value'])
    ->where('path = ?', 'carriers/dhl/shipment_days')
    ->orWhere('path = ?', 'carriers/dhl/intl_shipment_days');

foreach ($this->getConnection()->fetchAll($select) as $configRow) {
    $row = ['value' => implode(',', array_intersect_key($days, array_flip(explode(',', $configRow['value']))))];
    $this->getConnection()->update(
        $this->getTable('core/config_data'),
        $row,
        [
            'config_id = ?' => $configRow['config_id'],
        ],
    );
}
