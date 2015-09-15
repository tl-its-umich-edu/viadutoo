<?php


class CurlTransport implements TransportInterface{
    /**
     * @param Proxy $proxy
     * @return mixed HTTP response code
     */
    public function send($proxy) {
        $client = curl_init($proxy->getEndpointUrl());

        $headerStrings = [];
        foreach ($proxy->getHeaders() as $headerKey => $headerValue) {
            if ($headerKey == 'Host') {
                continue;
            }

            $headerStrings[] = $headerKey . ': ' . $headerValue;
        }

        $curlOptions = [
            CURLOPT_POST => true,
            CURLOPT_NOSIGNAL => true, // required for timeouts to work properly
            CURLOPT_HTTPHEADER => $headerStrings,
            CURLOPT_HEADER => true, // required to return response text
            CURLOPT_RETURNTRANSFER => true, // required to return response text
            CURLOPT_POSTFIELDS => $proxy->getBody(),
        ];

        $timeoutSeconds = $proxy->getTimeoutSeconds();
        if ($timeoutSeconds != null) {
            $curlOptions[CURLOPT_TIMEOUT_MS] = intval($timeoutSeconds * 1000);
        }

        curl_setopt_array($client, $curlOptions);

        $responseText = curl_exec($client);
        $responseInfo = curl_getinfo($client);
        curl_close($client);

        if ($responseText) {
            $responseCode = $responseInfo['http_code'];
        } else {
            $responseCode = null;
        }
    }

}