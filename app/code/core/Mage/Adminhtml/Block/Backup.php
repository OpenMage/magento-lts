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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml backup page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Backup extends Mage_Adminhtml_Block_Template
{
    /**
     * Block's template
     *
     * @var string
     */
    protected $_template = 'backup/list.phtml';

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setChild(
            'createButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label' => Mage::helper('backup')->__('Database Backup'),
                    'onclick' => "return backup.backup('" . Mage_Backup_Helper_Data::TYPE_DB . "')",
                    'class'  => 'task'
                ])
        );
        $this->setChild(
            'createSnapshotButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label' => Mage::helper('backup')->__('System Backup'),
                    'onclick' => "return backup.backup('" . Mage_Backup_Helper_Data::TYPE_SYSTEM_SNAPSHOT . "')",
                    'class'  => ''
                ])
        );
        $this->setChild(
            'createMediaBackupButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label' => Mage::helper('backup')->__('Database and Media Backup'),
                    'onclick' => "return backup.backup('" . Mage_Backup_Helper_Data::TYPE_MEDIA . "')",
                    'class'  => ''
                ])
        );
        $this->setChild(
            'backupsGrid',
            $this->getLayout()->createBlock('adminhtml/backup_grid')
        );

        $this->setChild('dialogs', $this->getLayout()->createBlock('adminhtml/backup_dialogs'));
        return $this;
    }

    public function getCreateButtonHtml()
    {
        return $this->getChildHtml('createButton');
    }

    /**
     * Generate html code for "Create System Snapshot" button
     *
     * @return string
     */
    public function getCreateSnapshotButtonHtml()
    {
        return $this->getChildHtml('createSnapshotButton');
    }

    /**
     * Generate html code for "Create Media Backup" button
     *
     * @return string
     */
    public function getCreateMediaBackupButtonHtml()
    {
        return $this->getChildHtml('createMediaBackupButton');
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('backupsGrid');
    }

    /**
     * Generate html code for pop-up messages that will appear when user click on "Rollback" link
     *
     * @return string
     */
    public function getDialogsHtml()
    {
        return $this->getChildHtml('dialogs');
    }
}
