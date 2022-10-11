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
 * @package    Mage_Oauth
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth My Application grid block
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Block_Adminhtml_Oauth_Admin_Token_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Construct grid block
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('adminTokenGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('entity_id')
            ->setDefaultDir(Varien_Db_Select::SQL_DESC);
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Admin_Model_User $user */
        $user = Mage::getSingleton('admin/session')->getData('user');

        /** @var Mage_Oauth_Model_Resource_Token_Collection $collection */
        $collection = Mage::getModel('oauth/token')->getCollection();
        $collection->joinConsumerAsApplication()
                ->addFilterByType(Mage_Oauth_Model_Token::TYPE_ACCESS)
                ->addFilterByAdminId($user->getId());
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header'    => Mage::helper('oauth')->__('ID'),
            'index'     => 'entity_id',
            'align'     => 'right',
            'width'     => '50px',
        ]);

        $this->addColumn('name', [
            'header'    => $this->__('Application Name'),
            'index'     => 'name',
            'escape'    => true,
        ]);

        /** @var Mage_Adminhtml_Model_System_Config_Source_Yesno $sourceYesNo */
        $sourceYesNo = Mage::getSingleton('adminhtml/system_config_source_yesno');
        $this->addColumn('revoked', [
            'header'    => $this->__('Revoked'),
            'index'     => 'revoked',
            'width'     => '100px',
            'type'      => 'options',
            'options'   => $sourceYesNo->toArray(),
            'sortable'  => true,
        ]);

        parent::_prepareColumns();
        return $this;
    }

    /**
     * Add mass-actions to grid
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $block = $this->getMassactionBlock();

        $block->setFormFieldName('items');
        $block->addItem('enable', [
            'label' => Mage::helper('index')->__('Enable'),
            'url'   => $this->getUrl('*/*/revoke', ['status' => 0]),
        ]);
        $block->addItem('revoke', [
            'label' => Mage::helper('index')->__('Revoke'),
            'url'   => $this->getUrl('*/*/revoke', ['status' => 1]),
        ]);
        $block->addItem('delete', [
            'label' => Mage::helper('index')->__('Delete'),
            'url'   => $this->getUrl('*/*/delete'),
        ]);

        return $this;
    }

    /**
     * Get grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
