<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Base html block
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Block_Text_Tag_Debug extends Mage_Core_Block_Text_Tag
{
    protected function _construct()
    {
        parent::_construct();
        $this->setAttribute([
            'tagName' => 'xmp',
        ]);
    }

    /**
     * @param mixed $value
     * @return $this
     * @SuppressWarnings("PHPMD.DevelopmentCodeFragment")
     */
    public function setValue($value)
    {
        return $this->setContents(print_r($value, true));
    }
}
