<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * Initialize resource
     */
    public function __construct()
    {
        $resources = Mage::getSingleton('core/resource');
        parent::__construct($resources->getConnection('eav_setup'));
    }
}
