<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Product Url helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Helper_Product_Url extends Mage_Core_Helper_Url
{
    /**
     * Symbol convert table
     *
     * @var array
     */
    protected $_convertTable = array('&amp;' => 'and', '@' => 'at', '©' => 'c', '®' => 'r', '™' => 'tm');

    /**
     * Check additional instruction for convertation table in configuration
     */
    public function __construct()
    {
        $convertNode = Mage::getConfig()->getNode('default/url/convert');
        if ($convertNode) {
            foreach ($convertNode->children() as $node) {
                $this->_convertTable[strval($node->from)] = strval($node->to);
            }
        }
    }

    /**
     * Get chars convertation table
     *
     * @return array
     */
    public function getConvertTable()
    {
        return $this->_convertTable;
    }

    /**
     * Process string based on convertation table
     *
     * @param   string $string
     * @param   null|string $locale
     * @return  string
     */
    public function format($string, $locale = null)
    {
        $string = strtr($string, $this->getConvertTable());

        if (!empty($locale)) {
            $opts = transliterator_list_ids();
            $code = str_replace('_', '-', strtolower($locale)).'_Latn/BGN';
            if (in_array($code, $opts)) {
                return transliterator_transliterate($code.'; Any-Latin; Latin-ASCII; [^\u001F-\u007f] remove; Lower()', $string);
            }
        }

        return transliterator_transliterate('Any-Latin; Latin-ASCII; [^\u001F-\u007f] remove; Lower()', $string);
    }
}
