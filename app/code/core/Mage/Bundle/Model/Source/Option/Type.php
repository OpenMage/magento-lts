<?php
/**
 * Bundle Option Type Source Model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
