<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml catalog product downloadable items tab links section
 *
 * @category   Mage
 * @package    Mage_Downloadable
 *
 * @method $this setCanEditPrice(bool $value)
 * @method bool getCanReadPrice()
 * @method $this setCanReadPrice(bool $value)
 */
class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links extends Mage_Uploader_Block_Single
{
    /**
     * Purchased Separately Attribute cache
     *
     * @var Mage_Catalog_Model_Resource_Eav_Attribute|null
     */
    protected $_purchasedSeparatelyAttribute = null;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('downloadable/product/edit/downloadable/links.phtml');
        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
    }

    /**
     * Get product that is being edited
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Retrieve Purchased Separately Attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getPurchasedSeparatelyAttribute()
    {
        if ($this->_purchasedSeparatelyAttribute === null) {
            $attributeCode = 'links_purchased_separately';

            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
            if (!($attribute instanceof Mage_Catalog_Model_Resource_Eav_Attribute)) {
                Mage::throwException('Attribute links_purchased_separately must be of type Mage_Catalog_Model_Resource_Eav_Attribute');
            }
            $this->_purchasedSeparatelyAttribute = $attribute;
        }

        return $this->_purchasedSeparatelyAttribute;
    }

    /**
     * Retrieve Purchased Separately HTML select
     *
     * @return string
     */
    public function getPurchasedSeparatelySelect()
    {
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setName('product[links_purchased_separately]')
            ->setId('downloadable_link_purchase_type')
            ->setOptions(Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray())
            ->setValue($this->getProduct()->getLinksPurchasedSeparately());

        return $select->getHtml();
    }

    /**
     * Retrieve Add button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData([
                'label' => Mage::helper('downloadable')->__('Add New Row'),
                'id'    => 'add_link_item',
                'class' => 'add'
            ]);
        return $addButton->toHtml();
    }

    /**
     * Retrieve default links title
     *
     * @return string
     */
    public function getLinksTitle()
    {
        return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }

    /**
     * Check exists defined links title
     *
     * @return bool
     */
    public function getUsedDefault()
    {
        return $this->getProduct()->getAttributeDefaultValue('links_title') === false;
    }

    /**
     * Return true if price in website scope
     *
     * @deprecated since 1.14.2.0
     * @return bool
     */
    public function getIsPriceWebsiteScope()
    {
        return Mage::helper('downloadable')->getIsPriceWebsiteScope();
    }

    /**
     * Return array of links
     *
     * @return array
     */
    public function getLinkData()
    {
        $linkArr = [];
        /** @var Mage_Downloadable_Model_Product_Type $productType */
        $productType = $this->getProduct()->getTypeInstance(true);
        /** @var Mage_Downloadable_Model_Link[] $links */
        $links = $productType->getLinks($this->getProduct());
        $priceWebsiteScope = Mage::helper('downloadable')->getIsPriceWebsiteScope();
        foreach ($links as $item) {
            $tmpLinkItem = [
                'link_id' => $item->getId(),
                'title' => $this->escapeHtml($item->getTitle()),
                'price' => $this->getCanReadPrice() ? $this->getPriceValue($item->getPrice()) : '',
                'number_of_downloads' => $item->getNumberOfDownloads(),
                'is_shareable' => $item->getIsShareable(),
                'link_url' => $item->getLinkUrl(),
                'link_type' => $item->getLinkType(),
                'sample_file' => $item->getSampleFile(),
                'sample_url' => $item->getSampleUrl(),
                'sample_type' => $item->getSampleType(),
                'sort_order' => $item->getSortOrder(),
            ];

            if ($item->getLinkFile()) {
                $file = Mage::helper('downloadable/file')->getFilePath(
                    Mage_Downloadable_Model_Link::getBasePath(),
                    $item->getLinkFile()
                );
                if (!is_file($file)) {
                    Mage::helper('core/file_storage_database')->saveFileToFilesystem($file);
                }
                if (is_file($file)) {
                    $name = '<a href="'
                        . $this->getUrl('*/downloadable_product_edit/link', [
                            'id' => $item->getId(),
                            '_secure' => true
                        ]) . '">' . Mage::helper('downloadable/file')->getFileFromPathFile($item->getLinkFile()) . '</a>';
                    $tmpLinkItem['file_save'] = [
                        [
                            'file' => $item->getLinkFile(),
                            'name' => $name,
                            'size' => filesize($file),
                            'status' => 'old'
                        ]
                    ];
                }
            }
            if ($item->getSampleFile()) {
                $sampleFile = Mage::helper('downloadable/file')->getFilePath(
                    Mage_Downloadable_Model_Link::getBaseSamplePath(),
                    $item->getSampleFile()
                );
                if (is_file($sampleFile)) {
                    $tmpLinkItem['sample_file_save'] = [
                        [
                            'file' => $item->getSampleFile(),
                            'name' => Mage::helper('downloadable/file')->getFileFromPathFile($item->getSampleFile()),
                            'size' => filesize($sampleFile),
                            'status' => 'old'
                        ]
                    ];
                }
            }
            if ($item->getNumberOfDownloads() == '0') {
                $tmpLinkItem['is_unlimited'] = ' checked="checked"';
            }
            if ($this->getProduct()->getStoreId() && $item->getStoreTitle()) {
                $tmpLinkItem['store_title'] = $item->getStoreTitle();
            }
            if ($this->getProduct()->getStoreId() && $priceWebsiteScope) {
                $tmpLinkItem['website_price'] = $item->getWebsitePrice();
            }
            $linkArr[] = new Varien_Object($tmpLinkItem);
        }
        return $linkArr;
    }

    /**
     * Return formatted price with two digits after decimal point
     *
     * @param float $value
     * @return string
     */
    public function getPriceValue($value)
    {
        return number_format($value ?? 0, 2, null, '');
    }

    /**
     * Retrieve max downloads value from config
     *
     * @return int
     */
    public function getConfigMaxDownloads()
    {
        return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_DEFAULT_DOWNLOADS_NUMBER);
    }

    /**
     * Prepare block Layout
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setChild(
            'upload_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->addData([
               'id'      => '',
               'label'   => Mage::helper('adminhtml')->__('Upload Files'),
               'type'    => 'button',
               'onclick' => 'Downloadable.massUploadByType(\'links\');Downloadable.massUploadByType(\'linkssample\')'
            ])
        );
        $this->_addElementIdsMapping([
           'container' => $this->getHtmlId() . '-new',
           'delete'    => $this->getHtmlId() . '-delete'
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
     * @param string $type
     * @return string
     */
    public function getConfigJson($type = 'links')
    {
        $this->getUploaderConfig()
            ->setFileParameterName($type)
            ->setTarget(
                Mage::getModel('adminhtml/url')
                    ->getUrl('*/downloadable_file/upload', ['type' => $type, '_secure' => true])
            );
        $this->getMiscConfig()
            ->setReplaceBrowseWithRemove(true)
        ;
        return Mage::helper('core')->jsonEncode(parent::getJsonConfig());
    }

    /**
     * @param string $type
     * @return string
     */
    public function getBrowseButtonHtml($type = '')
    {
        return $this->getChild('browse_button')
            // Workaround for IE9
            ->setBeforeHtml(
                '<div style="display:inline-block; " id="downloadable_link_{{id}}_' . $type . 'file-browse">'
            )
            ->setAfterHtml('</div>')
            ->setId('downloadable_link_{{id}}_' . $type . 'file-browse_button')
            ->toHtml();
    }

    /**
     * @param string $type
     * @return string
     */
    public function getDeleteButtonHtml($type = '')
    {
        return $this->getChild('delete_button')
            ->setLabel('')
            ->setId('downloadable_link_{{id}}_' . $type . 'file-delete')
            ->setStyle('display:none; width:31px;')
            ->toHtml();
    }

    /**
     * Retrieve config object
     *
     * @deprecated
     * @return $this
     */
    public function getConfig()
    {
        return $this;
    }
}
