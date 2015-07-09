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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Review_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'review';

        $this->_updateButton('save', 'label', Mage::helper('review')->__('Save Review'));
        $this->_updateButton('save', 'id', 'save_button');
        $this->_updateButton('delete', 'label', Mage::helper('review')->__('Delete Review'));

        if( $this->getRequest()->getParam('productId', false) ) {
            $this->_updateButton(
                'back',
                'onclick',
                'setLocation(\''
                    . $this->getUrl(
                        '*/catalog_product/edit',
                        array('id' => $this->getRequest()->getParam('productId', false))
                    )
                    .'\')'
            );
        }

        if( $this->getRequest()->getParam('customerId', false) ) {
            $this->_updateButton(
                'back',
                'onclick',
                'setLocation(\''
                    . $this->getUrl(
                        '*/customer/edit',
                        array('id' => $this->getRequest()->getParam('customerId', false))
                    )
                    .'\')'
            );
        }

        if( $this->getRequest()->getParam('ret', false) == 'pending' ) {
            $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/*/pending') .'\')' );
            $this->_updateButton(
                'delete',
                'onclick',
                'deleteConfirm('
                    . '\''
                    . Mage::helper('core')->jsQuoteEscape(
                        Mage::helper('review')->__('Are you sure you want to do this?')
                    )
                    . '\', '
                    . '\'' . $this->getUrl(
                        '*/*/delete',
                        array(
                            $this->_objectId => $this->getRequest()->getParam($this->_objectId),
                            'ret'           => 'pending',
                        )
                    ) . '\''
                    . ')'
            );
            Mage::register('ret', 'pending');
        }

        if( $this->getRequest()->getParam($this->_objectId) ) {
            $reviewData = Mage::getModel('review/review')
                ->load($this->getRequest()->getParam($this->_objectId));
            Mage::register('review_data', $reviewData);
        }

        $this->_formInitScripts[] = '
            var review = {
                updateRating: function() {
                        elements = [
                            $("select_stores"),
                            $("rating_detail").getElementsBySelector("input[type=\'radio\']")
                        ].flatten();
                        $(\'save_button\').disabled = true;
                        new Ajax.Updater(
                            "rating_detail",
                            "' . $this->getUrl('*/*/ratingItems', array('_current'=>true)).'",
                            {
                                parameters:Form.serializeElements(elements),
                                evalScripts:true,
                                onComplete:function(){ $(\'save_button\').disabled = false; }
                            }
                        );
                    }
           }
           Event.observe(window, \'load\', function(){
                 Event.observe($("select_stores"), \'change\', review.updateRating);
           });
        ';
    }

    public function getHeaderText()
    {
        if( Mage::registry('review_data') && Mage::registry('review_data')->getId() ) {
            return Mage::helper('review')->__("Edit Review '%s'", $this->escapeHtml(Mage::registry('review_data')->getTitle()));
        } else {
            return Mage::helper('review')->__('New Review');
        }
    }
}
