<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * @SuppressWarnings(PHPMD.DevelopmentCodeFragment)
     */
    public function setValue($value)
    {
        return $this->setContents(print_r($value, true));
    }
}
