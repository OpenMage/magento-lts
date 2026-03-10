<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Base html block
 *
 * @package    Mage_Core
 */
class Mage_Core_Block_Text_Tag_Debug extends Mage_Core_Block_Text_Tag
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setAttribute([
            'tagName' => 'xmp',
        ]);
    }

    /**
     * @param  mixed $value
     * @return $this
     * @SuppressWarnings("PHPMD.DevelopmentCodeFragment")
     */
    public function setValue($value)
    {
        return $this->setContents(print_r($value, true));
    }
}
