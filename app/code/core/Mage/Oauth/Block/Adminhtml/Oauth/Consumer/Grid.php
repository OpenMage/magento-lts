<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

/**
 * OAuth Consumer grid block
 *
 * @package    Mage_Oauth
 */
class Mage_Oauth_Block_Adminhtml_Oauth_Consumer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected string $_eventPrefix = 'oauth_adminhtml_oauth_consumer_grid';

    public function __construct()
    {
        parent::__construct();
        $this->setId('consumerGrid');
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
        $collection = Mage::getModel('oauth/consumer')->getCollection();
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
            'header' => Mage::helper('oauth')->__('ID'),
            'index' => 'entity_id',
        ]);

        $this->addColumn('name', [
            'header' => Mage::helper('oauth')->__('Consumer Name'), 'index' => 'name', 'escape' => true,
        ]);

        $this->addColumn('created_at', [
            'header' => Mage::helper('oauth')->__('Created At'), 'index' => 'created_at',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * @inheritDoc
     * @param  Mage_Oauth_Model_Consumer $row
     * @throws Mage_Core_Exception
     */
    #[Override]
    public function getRowUrl($row)
    {
        if ($this->isAllowed('system/oauth/consumer/edit')) {
            return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
        }

        return '';
    }
}
