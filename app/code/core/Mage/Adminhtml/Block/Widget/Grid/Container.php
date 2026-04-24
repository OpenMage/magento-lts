<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */


/**
 * Adminhtml grid container block
 *
 * @package    Mage_Adminhtml
 *
 * @method string getBackUrl()
 * @method $this  setBackUrl(string $value)
 */
class Mage_Adminhtml_Block_Widget_Grid_Container extends Mage_Adminhtml_Block_Widget_Container
{
    protected $_addButtonLabel;

    protected $_backButtonLabel;

    protected $_blockGroup = 'adminhtml';

    /**
     * @var string
     */
    protected $_block;

    /**
     * Mage_Adminhtml_Block_Widget_Grid_Container constructor.
     */
    public function __construct()
    {
        if (is_null($this->_addButtonLabel)) {
            $this->_addButtonLabel = $this->__('Add New');
        }

        if (is_null($this->_backButtonLabel)) {
            $this->_backButtonLabel = $this->__('Back');
        }

        parent::__construct();

        $this->setTemplate('widget/grid/container.phtml');

        $this->_addPreparedButton(
            id: self::BUTTON_TYPE_ADD,
            label: $this->getAddButtonLabel(),
            onClickUrl: $this->getCreateUrl(),
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                $this->_blockGroup . '/' . $this->_controller . '_grid',
                $this->_controller . '.grid',
            )->setSaveParametersInSession(true),
        );
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

    /**
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * @return string
     */
    protected function getAddButtonLabel()
    {
        return $this->_addButtonLabel;
    }

    /**
     * @return string
     */
    protected function getBackButtonLabel()
    {
        return $this->_backButtonLabel;
    }

    protected function _addBackButton()
    {
        $this->_addPreparedButton(
            id: self::BUTTON_TYPE_BACK,
            onClickUrl: $this->getBackUrl(),
        );
    }

    /**
     * @return string
     */
    #[Override]
    public function getHeaderCssClass()
    {
        return 'icon-head ' . parent::getHeaderCssClass();
    }

    /**
     * @return string
     */
    public function getHeaderWidth()
    {
        return 'width:50%;';
    }
}
