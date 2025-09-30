<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Template Filter Model
 *
 * @package    Mage_Catalog
 * @todo       Needs to be reimplemented to get rid of the copypasted methods
 */
class Mage_Catalog_Model_Template_Filter extends Varien_Filter_Template
{
    /**
     * Use absolute links flag
     *
     * @var bool
     */
    protected $_useAbsoluteLinks = false;

    /**
     * Whether to allow SID in store directive: NO
     *
     * @var bool
     */
    protected $_useSessionInUrl = false;

    /**
     * Set use absolute links flag
     *
     * @param bool $flag
     * @return Mage_Catalog_Model_Template_Filter
     */
    public function setUseAbsoluteLinks($flag)
    {
        $this->_useAbsoluteLinks = $flag;
        return $this;
    }

    /**
     * Setter whether SID is allowed in store directive
     * Doesn't set anything intentionally, since SID is not allowed in any kind of emails
     *
     * @param bool $flag
     * @return Mage_Catalog_Model_Template_Filter
     */
    public function setUseSessionInUrl($flag)
    {
        $this->_useSessionInUrl = $flag;
        return $this;
    }

    /**
     * Retrieve Skin URL directive
     *
     * @param array $construction
     * @return string
     * @see Mage_Core_Model_Email_Template_Filter::skinDirective() method has been copypasted
     */
    public function skinDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        $params['_absolute'] = $this->_useAbsoluteLinks;

        return Mage::getDesign()->getSkinUrl($params['url'], $params);
    }

    /**
     * Retrieve media file URL directive
     *
     * @param array $construction
     * @return string
     * @see Mage_Core_Model_Email_Template_Filter::mediaDirective() method has been copypasted
     */
    public function mediaDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        return Mage::getBaseUrl('media') . $params['url'];
    }

    /**
     * Retrieve store URL directive
     * Support url and direct_url properties
     *
     * @param array $construction
     * @return string
     * @see Mage_Core_Model_Email_Template_Filter::storeDirective() method has been copypasted
     */
    public function storeDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        if (!isset($params['_query'])) {
            $params['_query'] = [];
        }
        foreach ($params as $k => $v) {
            if (str_starts_with($k, '_query_')) {
                $params['_query'][substr($k, 7)] = $v;
                unset($params[$k]);
            }
        }
        $params['_absolute'] = $this->_useAbsoluteLinks;

        if ($this->_useSessionInUrl === false) {
            $params['_nosid'] = true;
        }

        if (isset($params['direct_url'])) {
            $path = '';
            $params['_direct'] = $params['direct_url'];
            unset($params['direct_url']);
        } else {
            $path = $params['url'] ?? '';
            unset($params['url']);
        }

        return Mage::app()->getStore(Mage::getDesign()->getStore())->getUrl($path, $params);
    }
}
