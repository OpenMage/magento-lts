<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

/**
 * Block to render select attribute
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Block_Widget_Select extends Mage_Eav_Block_Widget_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('eav/widget/select.phtml');
    }

    public function getOptions(): array
    {
        return $this->getAttribute()->getSource()->getAllOptions();
    }
}
