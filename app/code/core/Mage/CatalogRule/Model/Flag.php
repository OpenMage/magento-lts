<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Flag stores status about availability not applied catalog price rules
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 */
class Mage_CatalogRule_Model_Flag extends Mage_Core_Model_Flag
{
    /**
     * Flag code
     *
     * @var string
     */
    protected $_flagCode = 'catalog_rules_dirty';
}
