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
 * @package     Mage_Newsletter
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Templates collection
 * 
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Newsletter_Model_Mysql4_Template_Collection extends Varien_Data_Collection_Db
{
    /**
     * Template table name
     *
     * @var string
     */
    protected $_templateTable;
    
    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('newsletter_read'));
        $this->_templateTable = Mage::getSingleton('core/resource')->getTableName('newsletter/template');
        $this->_select->from($this->_templateTable, array('template_id','template_code',
                                                             'template_type',
                                                             'template_subject','template_sender_name',
                                                             'template_sender_email',
                                                             'added_at',
                                                             'modified_at'
                                                             ));
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('newsletter/template'));
    }
    
    /**
     * Load only actual template
     *
     * @return  Mage_Newsletter_Model_Mysql4_Template_Collection
     */
    public function useOnlyActual()
    {
        $this->_select->where('template_actual=?', 1);
        
        return $this;
    }
        
    public function toOptionArray()
    {
        return $this->_toOptionArray('template_id', 'template_code');
    }
    
}
