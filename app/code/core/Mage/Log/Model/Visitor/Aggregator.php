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
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Log visitor aggregator
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Log_Model_Visitor_Aggregator extends Varien_Object
{
    public function getResource()
    {
        return Mage::getResourceModel('log/visitor_aggregator');
    }

    public function update()
    {
        $this->getResource()->update();
        return $this;
    }
    
    public function updateOneShot()
    {
        $this->getResource()->updateOneShot();
        return $this;
    }
}