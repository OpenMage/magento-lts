<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api2
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth2 Authentication adapter
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Auth_Adapter_Oauth2 extends Mage_Api2_Model_Auth_Adapter_Abstract
{
    /**
     * Process request and figure out an API user type and its identifier
     *
     * Returns stdClass object with two properties: type and id
     *
     * @return stdClass
     */
    public function getUserParams(Mage_Api2_Model_Request $request)
    {
        $userParamsObj = (object) ['type' => null, 'id' => null];

        try {
            $token = $this->_validateToken($request);
            $userType = $token->getUserType();

            if ($userType === 'admin') {
                $userParamsObj->id = $token->getAdminId();
            } else {
                $userParamsObj->id = $token->getCustomerId();
            }
            $userParamsObj->type = $userType;
        } catch (Exception $e) {
            throw new Mage_Api2_Exception($e->getMessage(), Mage_Api2_Model_Server::HTTP_UNAUTHORIZED);
        }

        return $userParamsObj;
    }

    /**
     * Validate the OAuth2 token
     *
     * @return Mage_Oauth2_Model_AccessToken
     * @throws Exception
     */
    protected function _validateToken(Mage_Api2_Model_Request $request)
    {
        $authorizationHeader = $request->getHeader('Authorization');
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new Exception('Missing or invalid Authorization header');
        }

        $accessToken = substr($authorizationHeader, 7);
        $token = Mage::getModel('oauth2/accessToken')->load($accessToken, 'access_token');
        if (!$token->getId() || $token->getExpiresIn() < time() || $token->getRevoked()) {
            throw new Exception('Invalid or expired access token');
        }

        return $token;
    }

    /**
     * Check if request contains authentication info for adapter
     *
     * @return bool
     */
    public function isApplicableToRequest(Mage_Api2_Model_Request $request)
    {
        $headerValue = $request->getHeader('Authorization');
        return $headerValue && strtolower(substr($headerValue, 0, 7)) === 'bearer ';
    }
}
