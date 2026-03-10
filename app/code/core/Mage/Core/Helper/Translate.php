<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Core data helper
 *
 * @package    Mage_Core
 */
class Mage_Core_Helper_Translate extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Core';

    /**
     * Save translation data to database for specific area
     *
     * @param  array  $translate
     * @param  string $area
     * @param  string $returnType
     * @return string
     */
    public function apply($translate, $area, $returnType = 'json')
    {
        try {
            if ($area) {
                Mage::getDesign()->setArea($area);
            }

            Mage::getModel('core/translate_inline')->processAjaxPost($translate);
            return $returnType == 'json' ? '{success:true}' : true;
        } catch (Exception $exception) {
            return $returnType == 'json' ? "{error:true,message:'" . $exception->getMessage() . "'}" : false;
        }
    }
}
