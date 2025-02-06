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
 * @method string getTheme()
 */
class Mage_Core_Block_Text_Tag_Css_Admin extends Mage_Core_Block_Text_Tag_Css
{
    /**
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    protected function _construct()
    {
        parent::_construct();
        $theme = empty($_COOKIE['admtheme']) ? 'default' : $_COOKIE['admtheme'];
        $this->setAttribute('theme', $theme);
    }

    /**
     * @param string $href
     * @param string|null $type
     * @return $this
     */
    public function setHref($href, $type = null)
    {
        $type = (string) $type;
        if (empty($type)) {
            $type = 'skin';
        }
        $url = Mage::getBaseUrl($type) . $href . $this->getTheme() . '.css';
        return $this->setTagParam('href', $url);
    }
}
