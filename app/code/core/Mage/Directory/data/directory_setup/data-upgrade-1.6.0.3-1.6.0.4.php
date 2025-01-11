<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
