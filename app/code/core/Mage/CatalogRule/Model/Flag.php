<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogRule
 */

/**
 * Flag stores status about availability not applied catalog price rules
 *
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
