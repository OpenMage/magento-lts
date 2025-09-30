<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Directory
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

// The Netherlands Antilles (AN) was divided into Bonaire, Saint Eustatius and Saba (BQ, BES, 535),
// Curaçao (CW, CUW, 531) and Sint Maarten (Dutch part) (SX, SXM, 534).
//
// See: https://www.iso.org/obp/ui/#iso:code:3166:AN

$data = [
    ['BQ', 'BQ', 'BES'],
    ['CW', 'CW', 'CUW'],
    ['SS', 'SS', 'SSD'],
    ['SX', 'SX', 'SXM'],
];

$columns = ['country_id', 'iso2_code', 'iso3_code'];
$installer->getConnection()->insertArray($installer->getTable('directory/country'), $columns, $data);
