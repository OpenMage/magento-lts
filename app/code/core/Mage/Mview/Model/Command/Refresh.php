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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Mview
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Mview_Model_Command_Refresh
 *
 * @category    Mage
 * @package     Mage_Mview
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Mview_Model_Command_Refresh extends Mage_Mview_Model_Command_Abstract
{
    /**
     * @var string
     */
    protected $_tableName = null;

    /**
     * @var string
     */
    protected $_viewName = null;

    /**
     * Constructor
     *
     * @param $arguments array
     */
    public function __construct($arguments)
    {
        $this->_tableName   = $arguments['table_name'];
        $this->_viewName    = $arguments['view_name'];
        parent::__construct($arguments);
    }

    /**
     * Refresh materialized view
     *
     * @return Mage_Mview_Model_Command_Refresh|mixed
     */
    public function execute()
    {
        $insert = $this->_connection->insertFromSelect(
            $this->_connection->select()->from($this->_viewName),
            $this->_tableName);
        $this->_connection->delete($this->_tableName);
        $this->_connection->query($insert);
        return $this;
    }
}
