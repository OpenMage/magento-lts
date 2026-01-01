<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * CMS page chooser for Wysiwyg CMS widget
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cms_Page_Widget_Chooser extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments Object data
     */
    public function __construct($arguments = [])
    {
        parent::__construct($arguments);
        //$this->setDefaultSort('name');
        $this->setUseAjax(true);
        $this->setDefaultFilter(['chooser_is_active' => '1']);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param  Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('*/cms_page_widget/chooser', ['uniq_id' => $uniqId]);

        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        if ($element->getValue()) {
            $page = Mage::getModel('cms/page')->load((int) $element->getValue());
            if ($page->getId()) {
                $chooser->setLabel($page->getTitle());
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        return '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var pageTitle = trElement.down("td").next().innerHTML;
                var pageId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                ' . $chooserJsObject . '.setElementValue(pageId);
                ' . $chooserJsObject . '.setElementLabel(pageTitle);
                ' . $chooserJsObject . '.close();
            }
        ';
    }

    /**
     * Prepare pages collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Cms_Model_Resource_Page_Collection $collection */
        $collection = Mage::getModel('cms/page')->getCollection();
        $collection->setFirstStoreFlag(true);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for pages grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('chooser_id', [
            'header'    => Mage::helper('cms')->__('ID'),
            'align'     => 'right',
            'index'     => 'page_id',
            'width'     => 50,
        ]);

        $this->addColumn('chooser_title', [
            'header'    => Mage::helper('cms')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ]);

        $this->addColumn('chooser_identifier', [
            'header'    => Mage::helper('cms')->__('URL Key'),
            'align'     => 'left',
            'index'     => 'identifier',
        ]);

        $this->addColumn('chooser_root_template', [
            'header'    => Mage::helper('cms')->__('Layout'),
            'index'     => 'root_template',
            'type'      => 'options',
            'options'   => Mage::getSingleton('page/source_layout')->getOptions(),
            'width'   => '100',
        ]);

        $this->addColumn('chooser_is_active', [
            'header'    => Mage::helper('cms')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => Mage::getModel('cms/page')->getAvailableStatuses(),
            'width'     => '100',
        ]);

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/cms_page_widget/chooser', ['_current' => true]);
    }
}
