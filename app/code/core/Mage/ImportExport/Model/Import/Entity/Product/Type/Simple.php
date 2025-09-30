<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Import entity simple product type
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Import_Entity_Product_Type_Simple extends Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract
{
    /**
     * Attributes' codes which will be allowed anyway, independently from its visibility property.
     *
     * @var array
     */
    protected $_forcedAttributesCodes = [
        'related_tgtr_position_behavior', 'related_tgtr_position_limit',
        'upsell_tgtr_position_behavior', 'upsell_tgtr_position_limit',
    ];
}
