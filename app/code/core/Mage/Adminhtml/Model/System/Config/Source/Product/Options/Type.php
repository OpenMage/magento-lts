<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Product option types mode source
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Product_Options_Type
{
    public const PRODUCT_OPTIONS_GROUPS_PATH = 'global/catalog/product/options/custom/groups';

    public function toOptionArray()
    {
        $groups = [
            ['value' => '', 'label' => Mage::helper('adminhtml')->__('-- Please select --')],
        ];

        $helper = Mage::helper('catalog');

        foreach (Mage::getConfig()->getNode(self::PRODUCT_OPTIONS_GROUPS_PATH)->children() as $group) {
            $types = [];
            $typesPath = self::PRODUCT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/types';
            foreach (Mage::getConfig()->getNode($typesPath)->children() as $type) {
                $labelPath = self::PRODUCT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/types/' . $type->getName()
                    . '/label';
                $types[] = [
                    'label' => $helper->__((string) Mage::getConfig()->getNode($labelPath)),
                    'value' => $type->getName(),
                ];
            }

            $labelPath = self::PRODUCT_OPTIONS_GROUPS_PATH . '/' . $group->getName() . '/label';

            $groups[] = [
                'label' => $helper->__((string) Mage::getConfig()->getNode($labelPath)),
                'value' => $types,
            ];
        }

        return $groups;
    }
}
