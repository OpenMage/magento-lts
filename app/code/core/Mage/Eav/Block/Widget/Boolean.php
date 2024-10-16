<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block to render boolean attribute
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Block_Widget_Boolean extends Mage_Eav_Block_Widget_Abstract
{
    /**
     * Initialize block
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('eav/widget/boolean.phtml');
    }

    /**
     * Get select options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [
            ['value' => '',  'label' => Mage::helper('eav')->__('')],
            ['value' => '1', 'label' => Mage::helper('eav')->__('Yes')],
            ['value' => '0', 'label' => Mage::helper('eav')->__('No')]
        ];

        return $options;
    }
}
