<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Adminhtml catalog product downloadable items tab links section
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples extends Mage_Uploader_Block_Single
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('downloadable/product/edit/downloadable/samples.phtml');
    }

    /**
     * Get model of the product that is being edited
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Check block is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getProduct()->getDownloadableReadonly();
    }

    /**
     * Retrieve Add Button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData([
                'label' => Mage::helper('downloadable')->__('Add New Row'),
                'id' => 'add_sample_item',
                'class' => 'add',
            ]);
        return $addButton->toHtml();
    }

    /**
     * Retrieve samples array
     *
     * @return array
     */
    public function getSampleData()
    {
        $samplesArr = [];
        /** @var Mage_Downloadable_Model_Product_Type $productType */
        $productType = $this->getProduct()->getTypeInstance(true);
        /** @var Mage_Downloadable_Model_Sample[] $samples */
        $samples = $productType->getSamples($this->getProduct());
        foreach ($samples as $item) {
            $tmpSampleItem = [
                'sample_id' => $item->getId(),
                'title' => $this->escapeHtml($item->getTitle()),
                'sample_url' => $item->getSampleUrl(),
                'sample_type' => $item->getSampleType(),
                'sort_order' => $item->getSortOrder(),
            ];
            $file = Mage::helper('downloadable/file')->getFilePath(
                Mage_Downloadable_Model_Sample::getBasePath(),
                $item->getSampleFile(),
            );
            if ($item->getSampleFile() && !is_file($file)) {
                Mage::helper('core/file_storage_database')->saveFileToFilesystem($file);
            }

            if ($item->getSampleFile() && is_file($file)) {
                $tmpSampleItem['file_save'] = [
                    [
                        'file' => $item->getSampleFile(),
                        'name' => Mage::helper('downloadable/file')->getFileFromPathFile($item->getSampleFile()),
                        'size' => filesize($file),
                        'status' => 'old',
                    ]];
            }

            if ($this->getProduct() && $item->getStoreTitle()) {
                $tmpSampleItem['store_title'] = $item->getStoreTitle();
            }

            $samplesArr[] = new Varien_Object($tmpSampleItem);
        }

        return $samplesArr;
    }

    /**
     * Check exists defined samples title
     *
     * @return bool
     */
    public function getUsedDefault()
    {
        return $this->getProduct()->getAttributeDefaultValue('samples_title') === false;
    }

    /**
     * Retrieve Default samples title
     *
     * @return string
     */
    public function getSamplesTitle()
    {
        return Mage::getStoreConfig(Mage_Downloadable_Model_Sample::XML_PATH_SAMPLES_TITLE);
    }

    /**
     * Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setChild(
            'upload_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData([
                    'id'      => '',
                    'label'   => Mage::helper('adminhtml')->__('Upload Files'),
                    'type'    => 'button',
                    'onclick' => "Downloadable.massUploadByType('samples')",
                ]),
        );

        $this->_addElementIdsMapping([
            'container' => $this->getHtmlId() . '-new',
            'delete'    => $this->getHtmlId() . '-delete',
        ]);
        return $this;
    }

    /**
     * Retrieve Upload button HTML
     *
     * @return string
     */
    public function getUploadButtonHtml()
    {
        return $this->getChild('upload_button')->toHtml();
    }

    /**
     * Retrieve config json
     *
     * @return string
     */
    public function getConfigJson()
    {
        $this->getUploaderConfig()
            ->setFileParameterName('samples')
            ->setTarget(
                Mage::getModel('adminhtml/url')
                    ->getUrl('*/downloadable_file/upload', ['type' => 'samples', '_secure' => true]),
            );
        $this->getMiscConfig()
            ->setReplaceBrowseWithRemove(true)
        ;
        return Mage::helper('core')->jsonEncode(parent::getJsonConfig());
    }

    /**
     * @return string
     */
    public function getBrowseButtonHtml()
    {
        return $this->getChild('browse_button')
            // Workaround for IE9
            ->setBeforeHtml('<div style="display:inline-block; " id="downloadable_sample_{{id}}_file-browse">')
            ->setAfterHtml('</div>')
            ->setId('downloadable_sample_{{id}}_file-browse_button')
            ->toHtml();
    }

    /**
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChild('delete_button')
            ->setLabel('')
            ->setId('downloadable_sample_{{id}}_file-delete')
            ->setStyle('display:none; width:31px;')
            ->toHtml();
    }

    /**
     * Retrieve config object
     *
     * @return $this
     * @deprecated
     */
    public function getConfig()
    {
        return $this;
    }
}
