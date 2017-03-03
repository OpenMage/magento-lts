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
 * @category    Varien
 * @package     Varien_Filter
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Varien_Filter_Template_Simple
 */
class Varien_Filter_Template_Simple extends Varien_Object implements Zend_Filter_Interface
{
    /**
     * Start tag for variable in template
     *
     * @var string
     */
    protected $_startTag = '{{';

    /**
     * End tag for variable in template
     *
     * @var string
     */
    protected $_endTag = '}}';

    /**
     * Define start tag and end tag
     *
     * @param string $start
     * @param string $end
     * @return Varien_Filter_Template_Simple
     */
    public function setTags($start, $end)
    {
        $this->_startTag = $start;
        $this->_endTag = $end;
        return $this;
    }

    /**
     * Return result of getData method for matched variables
     *
     * @param array $matches
     * @return mixed
     */
    protected function _filterDataItem($matches)
    {
        return $this->getData($matches[1]);
    }

    /**
     * Insert data to template
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        return preg_replace_callback(
            '#' . $this->_startTag . '(.*?)' . $this->_endTag . '#',
            array($this, '_filterDataItem'),
            $value
        );
    }
}

