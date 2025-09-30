<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogRule
 */

/**
 * @package    Mage_CatalogRule
 */
class Mage_CatalogRule_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_CatalogRule';

    /**
     * Algorithm for calculating price rule
     *
     * @param  string $actionOperator
     * @param  int $ruleAmount
     * @param  float $price
     * @return float|int
     */
    public function calcPriceRule($actionOperator, $ruleAmount, $price)
    {
        $priceRule = 0;
        return match ($actionOperator) {
            'to_fixed' => min($ruleAmount, $price),
            'to_percent' => $price * $ruleAmount / 100,
            'by_fixed' => max(0, $price - $ruleAmount),
            'by_percent' => $price * (1 - $ruleAmount / 100),
            default => $priceRule,
        };
    }
}
