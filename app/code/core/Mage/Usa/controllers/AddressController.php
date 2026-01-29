<?php
/**
 * USPS Address Verification Controller
 *
 * Handles AJAX requests for address verification during checkout.
 *
 * @category    Mage
 * @package     Mage_Usa
 */
class Mage_Usa_AddressController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service
     */
    protected $_addressService;

    /**
     * Get address service instance
     *
     * @return Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service
     */
    protected function _getAddressService()
    {
        if ($this->_addressService === null) {
            $this->_addressService = Mage::getModel('usa/shipping_carrier_usps_address_service');
        }
        return $this->_addressService;
    }

    /**
     * Verify an address via USPS API
     *
     * POST parameters:
     * - street1: Primary street address
     * - street2: Secondary address (apt, suite, etc.)
     * - city: City name
     * - region: State/Province code
     * - postcode: ZIP code
     *
     * @return void
     */
    public function verifyAction()
    {
        $result = array(
            'status' => 'error',
            'message' => '',
            'original' => array(),
            'corrected' => null,
            'corrections' => array(),
            'warnings' => array()
        );

        try {
            // Validate form key
            if (!$this->_validateFormKey()) {
                $result['message'] = $this->__('Invalid form key. Please refresh the page.');
                $this->_sendJsonResponse($result);
                return;
            }

            // Check if verification is enabled
            $addressService = $this->_getAddressService();
            if (!$addressService->isEnabled()) {
                // Silently pass through if disabled
                $result['status'] = 'exact';
                $this->_sendJsonResponse($result);
                return;
            }

            // Build address data from POST
            $addressData = array(
                'street1' => trim($this->getRequest()->getPost('street1', '')),
                'street2' => trim($this->getRequest()->getPost('street2', '')),
                'city' => trim($this->getRequest()->getPost('city', '')),
                'region' => trim($this->getRequest()->getPost('region', '')),
                'postcode' => trim($this->getRequest()->getPost('postcode', ''))
            );

            // Store original for comparison
            $result['original'] = $addressData;

            // Validate required fields
            if ($addressData['street1'] === '' || $addressData['street1'] === null) {
                $result['message'] = $this->__('Street address is required.');
                $this->_sendJsonResponse($result);
                return;
            }

            if (($addressData['city'] === '' || $addressData['city'] === null) && ($addressData['postcode'] === '' || $addressData['postcode'] === null)) {
                $result['message'] = $this->__('City or ZIP code is required.');
                $this->_sendJsonResponse($result);
                return;
            }

            // Verify address using array-based method
            $verificationResult = $addressService->verifyFromArray($addressData);

            if ($verificationResult['success']) {
                $status = $verificationResult['status'];
                $result['status'] = $status;
                $result['warnings'] = isset($verificationResult['warnings']) ? $verificationResult['warnings'] : array();

                if ($status === Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service::MATCH_CORRECTED) {
                    // Address was corrected
                    $result['corrected'] = $verificationResult['corrected'];
                    $result['corrections'] = $this->_buildCorrections($addressData, $verificationResult['corrected']);
                    $result['message'] = $this->__('USPS suggests corrections to your address.');
                } elseif ($status === Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service::MATCH_EXACT) {
                    $result['message'] = $this->__('Address verified successfully.');
                } elseif ($status === Mage_Usa_Model_Shipping_Carrier_Usps_Address_Service::MATCH_MULTIPLE) {
                    $result['message'] = $this->__('Multiple addresses match. Please be more specific.');
                } else {
                    $result['status'] = 'invalid';
                    $result['message'] = $this->__('Address could not be verified.');
                }
            } else {
                $result['status'] = 'error';
                $result['message'] = isset($verificationResult['error'])
                    ? $verificationResult['error']
                    : $this->__('Address verification failed.');
            }

        } catch (Exception $e) {
            Mage::logException($e);
            $result['status'] = 'error';
            $result['message'] = $this->__('An error occurred during address verification.');
        }

        $this->_sendJsonResponse($result);
    }

    /**
     * Apply address correction to session/quote
     *
     * POST parameters: Same as verifyAction
     *
     * @return void
     */
    public function applyAction()
    {
        $result = array(
            'success' => false,
            'message' => ''
        );

        try {
            // Validate form key
            if (!$this->_validateFormKey()) {
                $result['message'] = $this->__('Invalid form key. Please refresh the page.');
                $this->_sendJsonResponse($result);
                return;
            }

            // Build corrected address from POST
            $correctedAddress = array(
                'street1' => trim($this->getRequest()->getPost('street1', '')),
                'street2' => trim($this->getRequest()->getPost('street2', '')),
                'city' => trim($this->getRequest()->getPost('city', '')),
                'region' => trim($this->getRequest()->getPost('region', '')),
                'postcode' => trim($this->getRequest()->getPost('postcode', ''))
            );

            // Apply correction to quote via service
            $addressService = $this->_getAddressService();
            $applyResult = $addressService->applyCorrectionToQuote($correctedAddress);

            $result['success'] = $applyResult['success'];
            $result['message'] = isset($applyResult['message'])
                ? $applyResult['message']
                : ($applyResult['success'] ? $this->__('Address updated.') : $this->__('Failed to update address.'));

            // Include updated form values for JS to populate
            if ($result['success']) {
                $result['address'] = $correctedAddress;
            }

        } catch (Exception $e) {
            Mage::logException($e);
            $result['message'] = $this->__('An error occurred while applying the correction.');
        }

        $this->_sendJsonResponse($result);
    }

    /**
     * Build corrections array showing what changed
     *
     * @param array $original Original address
     * @param array $corrected Corrected address
     * @return array
     */
    protected function _buildCorrections($original, $corrected)
    {
        $corrections = array();
        $fields = array('street1', 'street2', 'city', 'region', 'postcode');

        foreach ($fields as $field) {
            $origValue = isset($original[$field]) ? strtoupper(trim($original[$field])) : '';
            $corrValue = isset($corrected[$field]) ? strtoupper(trim($corrected[$field])) : '';

            if ($origValue !== $corrValue) {
                $corrections[$field] = array(
                    'original' => isset($original[$field]) ? $original[$field] : '',
                    'corrected' => isset($corrected[$field]) ? $corrected[$field] : ''
                );
            }
        }

        return $corrections;
    }

    /**
     * Send JSON response and exit
     *
     * @param array $data Response data
     * @return void
     */
    protected function _sendJsonResponse($data)
    {
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json', true)
            ->setBody(Mage::helper('core')->jsonEncode($data));
    }
}
