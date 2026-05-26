<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

/**
 * OAuth My Application grid block
 *
 * @package    Mage_Oauth
 */
class Mage_Oauth_Block_Adminhtml_Oauth_Admin_Token_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected string $_eventPrefix = 'oauth_adminhtml_oauth_admin_token_grid';

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
     * @inheritDoc
     */
    #[Override]
    protected function _prepareCollection()
    {
        /** @var Mage_Admin_Model_User $user */
        $user = Mage::getSingleton('admin/session')->getDataByKey('user');

        /** @var Mage_Oauth_Model_Resource_Token_Collection $collection */
        $collection = Mage::getModel('oauth/token')->getCollection();
        $collection->joinConsumerAsApplication()
                ->addFilterByType(Mage_Oauth_Model_Token::TYPE_ACCESS)
                ->addFilterByAdminId($user->getId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    #[Override]
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header'    => Mage::helper('oauth')->__('ID'),
            'index'     => 'entity_id',
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
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @inheritDoc
     */
    #[Override]
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

        return parent::_prepareMassaction();
    }

    /**
     * Get grid URL
     *
     * @return string
     */
    #[Override]
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
