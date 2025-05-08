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
