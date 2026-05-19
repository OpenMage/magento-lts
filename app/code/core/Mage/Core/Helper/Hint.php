<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Core hint helper
 *
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
     * @param  string      $code
     * @return null|string
     */
    public function getHintByCode($code)
    {
        $hint = null;
        $hints = $this->getAvailableHints();
        if (array_key_exists($code, $hints)) {
            return $hints[$code];
        }

        return $hint;
    }
}
