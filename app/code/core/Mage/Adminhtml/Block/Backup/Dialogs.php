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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml rollback dialogs block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Backup_Dialogs extends Mage_Adminhtml_Block_Template
{
    /**
     * Block's template
     *
     * @var string
     */
    protected $_template = 'backup/dialogs.phtml';

    /**
     * Include backup.js file in page before rendering
     *
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        /** @var Mage_Page_Block_Html_Head $block */
        $block = $this->getLayout()->getBlock('head');
        $block->addJs('mage/adminhtml/backup.js');
        return parent::_prepareLayout();
    }
}
