<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Core hint helper
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Helper_Hint extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Core';

    /**
     * List of available hints
     *
     * @var null|array
     */
    protected $_availableHints;

    /**
     * Retrieve list of available hints as [hint code] => [hint url]
     *
     * @return array
     */
    public function getAvailableHints()
    {
        if ($this->_availableHints === null) {
            $hints = [];
            $config = Mage::getConfig()->getNode('default/hints');
            if ($config) {
                foreach ($config->children() as $type => $node) {
                    if ((string) $node->enabled) {
                        $hints[$type] = (string) $node->url;
                    }
                }
            }
            $this->_availableHints = $hints;
        }
        return $this->_availableHints;
    }

    /**
     * Get Hint Url by Its Code
     *
     * @param string $code
     * @return null|string
     */
    public function getHintByCode($code)
    {
        $hint = null;
        $hints = $this->getAvailableHints();
        if (array_key_exists($code, $hints)) {
            $hint = $hints[$code];
        }
        return $hint;
    }
}
