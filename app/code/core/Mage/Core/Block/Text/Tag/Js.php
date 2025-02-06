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
 *
 * @method $this setTagName(string $value)
 * @method $this setTagParams(array $value)
 */
class Mage_Core_Block_Text_Tag_Js extends Mage_Core_Block_Text_Tag
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTagName('script');
        $this->setTagParams(['language' => 'javascript', 'type' => 'text/javascript']);
    }

    /**
     * @param string $src
     * @param string|null $type
     * @return $this
     */
    public function setSrc($src, $type = null)
    {
        $type = (string) $type;
        if (empty($type)) {
            $type = 'js';
        }
        $url = Mage::getBaseUrl($type) . $src;

        return $this->setTagParam('src', $url);
    }
}
