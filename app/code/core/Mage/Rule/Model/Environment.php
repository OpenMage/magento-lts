<?php
/**
 * Class Mage_Rule_Model_Environment
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Rule
 * @method $this setNow(int $value)
 */
class Mage_Rule_Model_Environment extends Varien_Object
{
    /**
     * Collect application environment for rules filtering
     *
     * @todo make it not dependent on checkout module
     * @return $this
     */
    public function collect()
    {
        $this->setNow(time());

        Mage::dispatchEvent('rule_environment_collect', ['env' => $this]);

        return $this;
    }
}
