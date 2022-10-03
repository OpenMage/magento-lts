<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Base html block
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
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
        $this->setTagParams(['language'=>'javascript', 'type'=>'text/javascript']);
    }

    /**
     * @param string $src
     * @param string|null $type
     * @return $this
     */
    public function setSrc($src, $type = null)
    {
        $type = (string)$type;
        if (empty($type)) {
            $type = 'js';
        }
        $url = Mage::getBaseUrl($type).$src;

        return $this->setTagParam('src', $url);
    }
}
