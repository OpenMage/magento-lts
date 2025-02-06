<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Bundle
 */

/**
 * Bundle Option Type Source Model
 *
 * @category   Mage
 * @package    Mage_Bundle
 */
class Mage_Bundle_Model_Source_Option_Type
{
    public const BUNDLE_OPTIONS_TYPES_PATH = 'global/catalog/product/options/bundle/types';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $types = [];

        foreach (Mage::getConfig()->getNode(self::BUNDLE_OPTIONS_TYPES_PATH)->children() as $type) {
            $labelPath = self::BUNDLE_OPTIONS_TYPES_PATH . '/' . $type->getName() . '/label';
            $types[] = [
                'label' => (string) Mage::getConfig()->getNode($labelPath),
                'value' => $type->getName(),
            ];
        }

        return $types;
    }
}
