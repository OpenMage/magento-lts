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
 * @package    Mage_Oauth
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth Consumer grid block
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Block_Adminhtml_Oauth_Consumer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Allow edit status
     *
     * @var bool
     */
    protected $_editAllow = false;

    /**
     * Construct grid block
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('consumerGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('entity_id')
            ->setDefaultDir(Varien_Db_Select::SQL_DESC);

        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');
        $this->_editAllow = $session->isAllowed('system/oauth/consumer/edit');
    }

    /**
     * Prepare collection
     *
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('oauth/consumer')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header' => Mage::helper('oauth')->__('ID'), 'index' => 'entity_id', 'align' => 'right', 'width' => '50px'
        ]);

        $this->addColumn('name', [
            'header' => Mage::helper('oauth')->__('Consumer Name'), 'index' => 'name', 'escape' => true
        ]);

        $this->addColumn('created_at', [
            'header' => Mage::helper('oauth')->__('Created At'), 'index' => 'created_at'
        ]);

        return parent::_prepareColumns();
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

    /**
     * Get row URL
     *
     * @param Mage_Oauth_Model_Consumer $row
     * @return string|null
     */
    public function getRowUrl($row)
    {
        if ($this->_editAllow) {
            return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
        }
        return null;
    }
}
