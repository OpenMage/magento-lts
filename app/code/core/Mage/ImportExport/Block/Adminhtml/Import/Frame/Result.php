<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Import frame result block.
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Block_Adminhtml_Import_Frame_Result extends Mage_Adminhtml_Block_Template
{
    /**
     * JavaScript actions for response.
     *
     * @var array
     */
    protected $_actions = [
        'clear'           => [], // remove element from DOM
        'innerHTML'       => [], // set innerHTML property (use: elementID => new content)
        'value'           => [], // set value for form element (use: elementID => new value)
        'show'            => [], // show specified element
        'hide'            => [], // hide specified element
        'removeClassName' => [], // remove specified class name from element
        'addClassName'    => [],  // add specified class name to element
    ];

    /**
     * Validation messages.
     *
     * @var array
     */
    protected $_messages = [
        'error'   => [],
        'success' => [],
        'notice'  => [],
    ];

    /**
     * Add action for response.
     *
     * @param string $actionName
     * @param array|string $elementId
     * @param mixed $value OPTIONAL
     * @return $this
     */
    public function addAction($actionName, $elementId, $value = null)
    {
        if (isset($this->_actions[$actionName])) {
            if ($value === null) {
                if (is_array($elementId)) {
                    foreach ($elementId as $oneId) {
                        $this->_actions[$actionName][] = $oneId;
                    }
                } else {
                    $this->_actions[$actionName][] = $elementId;
                }
            } else {
                $this->_actions[$actionName][$elementId] = $value;
            }
        }

        return $this;
    }

    /**
     * Add error message.
     *
     * @param string $message Error message
     * @return $this
     */
    public function addError($message)
    {
        if (is_array($message)) {
            foreach ($message as $row) {
                $this->addError($row);
            }
        } else {
            $this->_messages['error'][] = $message;
        }

        return $this;
    }

    /**
     * Add notice message.
     *
     * @param mixed $message Message text
     * @param bool $appendImportButton OPTIONAL Append import button to message?
     * @return $this
     */
    public function addNotice($message, $appendImportButton = false)
    {
        if (is_array($message)) {
            foreach ($message as $row) {
                $this->addNotice($row);
            }
        } else {
            $this->_messages['notice'][] = $message . ($appendImportButton ? $this->getImportButtonHtml() : '');
        }

        return $this;
    }

    /**
     * Add success message.
     *
     * @param mixed $message Message text
     * @param bool $appendImportButton OPTIONAL Append import button to message?
     * @return $this
     */
    public function addSuccess($message, $appendImportButton = false)
    {
        if (is_array($message)) {
            foreach ($message as $row) {
                $this->addSuccess($row);
            }
        } else {
            $this->_messages['success'][] = $message . ($appendImportButton ? $this->getImportButtonHtml() : '');
        }

        return $this;
    }

    /**
     * Import button HTML for append to message.
     *
     * @return string
     */
    public function getImportButtonHtml()
    {
        return '&nbsp;&nbsp;<button onclick="editForm.startImport(\'' . $this->getImportStartUrl()
            . "', '" . Mage_ImportExport_Model_Import::FIELD_NAME_SOURCE_FILE . '\');" class="scalable save"'
            . ' type="button"><span><span><span>' . $this->__('Import') . '</span></span></span></button>';
    }

    /**
     * Import start action URL.
     *
     * @return string
     */
    public function getImportStartUrl()
    {
        return $this->getUrl('*/*/start');
    }

    /**
     * Messages getter.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Messages rendered HTML getter.
     *
     * @return string
     */
    public function getMessagesHtml()
    {
        /** @var Mage_Core_Block_Messages $messagesBlock */
        $messagesBlock = $this->_layout->createBlock('core/messages');

        foreach ($this->_messages as $priority => $messages) {
            $method = "add{$priority}";

            foreach ($messages as $message) {
                $messagesBlock->$method($message);
            }
        }

        return $messagesBlock->toHtml();
    }

    /**
     * Return response as JSON.
     *
     * @return string
     */
    public function getResponseJson()
    {
        // add messages HTML if it is not already specified
        if (!isset($this->_actions['import_validation_messages'])) {
            $this->addAction('innerHTML', 'import_validation_messages', $this->getMessagesHtml());
        }

        return Mage::helper('core')->jsonEncode($this->_actions);
    }
}
