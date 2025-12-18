<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

/**
 * Customer My Applications list block
 *
 * @package    Mage_Oauth
 */
class Mage_Oauth_Block_Customer_Token_List extends Mage_Customer_Block_Account_Dashboard
{
    /**
     * Collection model
     *
     * @var Mage_Oauth_Model_Resource_Token_Collection
     */
    protected $_collection;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton('customer/session');

        /** @var Mage_Oauth_Model_Resource_Token_Collection $collection */
        $collection = Mage::getModel('oauth/token')->getCollection();
        $collection->joinConsumerAsApplication()
                ->addFilterByType(Mage_Oauth_Model_Token::TYPE_ACCESS)
                ->addFilterByCustomerId($session->getCustomerId());
        $this->_collection = $collection;
    }

    /**
     * Get count of total records
     *
     * @return int
     */
    public function count()
    {
        return $this->_collection->getSize();
    }

    /**
     * Get toolbar html
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        /** @var Mage_Page_Block_Html_Pager $toolbar */
        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'customer_token.toolbar');
        $toolbar->setCollection($this->_collection);
        $this->setChild('toolbar', $toolbar);
        parent::_prepareLayout();
        return $this;
    }

    /**
     * Get collection
     *
     * @return Mage_Oauth_Model_Resource_Token_Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Get link for update revoke status
     *
     * @return string
     */
    public function getUpdateRevokeLink(Mage_Oauth_Model_Token $model)
    {
        return Mage::getUrl(
            'oauth/customer_token/revoke/',
            ['id' => $model->getId(), 'status' => (int) !$model->getRevoked()],
        );
    }

    /**
     * Get delete link
     *
     * @return string
     */
    public function getDeleteLink(Mage_Oauth_Model_Token $model)
    {
        return Mage::getUrl('oauth/customer_token/delete/', ['id' => $model->getId()]);
    }

    /**
     * Retrieve a token status label
     *
     * @param  int    $revokedStatus Token status of revoking
     * @return string
     */
    public function getStatusLabel($revokedStatus)
    {
        $labels = [
            $this->__('Enabled'),
            $this->__('Disabled'),
        ];
        return $labels[$revokedStatus];
    }

    /**
     * Retrieve a label of link to change a token status
     *
     * @param  int    $revokedStatus Token status of revoking
     * @return string
     */
    public function getChangeStatusLabel($revokedStatus)
    {
        $labels = [
            $this->__('Disable'),
            $this->__('Enable'),
        ];
        return $labels[$revokedStatus];
    }

    /**
     * Retrieve a message to confirm an action to change a token status
     *
     * @param  int    $revokedStatus Token status of revoking
     * @return string
     */
    public function getChangeStatusConfirmMessage($revokedStatus)
    {
        $messages = [
            $this->__('Are you sure you want to disable this application?'),
            $this->__('Are you sure you want to enable this application?'),
        ];
        return $messages[$revokedStatus];
    }
}
