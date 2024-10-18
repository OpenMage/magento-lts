<?php

class Mage_Oauth2_Controller_BaseController extends Mage_Core_Controller_Front_Action
{
    /**
     * Send JSON response
     *
     * @param int $code HTTP status code
     * @param string $message Response message
     * @param mixed|null $data Additional data (optional)
     * @return void
     */
    protected function _sendResponse($code, $message, $data = null)
    {
        $response = [
            'code' => $code,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        $this->getResponse()
            ->setHttpResponseCode($code)
            ->setHeader('Content-Type', 'application/json', true)
            ->setBody(json_encode($response));
    }
}
