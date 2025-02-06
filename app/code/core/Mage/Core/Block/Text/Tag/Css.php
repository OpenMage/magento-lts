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
class Mage_Core_Block_Text_Tag_Css extends Mage_Core_Block_Text_Tag
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTagName('link');
        $this->setTagParams(['rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'all']);
    }

    /**
     * @param string $href
     * @param string|null $type
     * @return Mage_Core_Block_Text_Tag_Css
     */
    public function setHref($href, $type = null)
    {
        $type = (string) $type;
        if (empty($type)) {
            $type = 'skin';
        }
        $url = Mage::getBaseUrl($type) . $href;

        return $this->setTagParam('href', $url);
    }
}
