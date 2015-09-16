<?php
require_once 'Viadutoo/transport/TransportInterface.php';

abstract class BaseTransport implements TransportInterface {
    /** @var string */
    private $_endpointUrl;
    /** @var float|null */
    private $_timeoutSeconds;

    /** @return string */
    public function getEndpointUrl() {
        return $this->_endpointUrl;
    }

    /**
     * @param string $endpointUrl
     * @return $this
     */
    public function setEndpointUrl($endpointUrl) {
        $this->_endpointUrl = strval($endpointUrl);
        return $this;
    }

    /** @return float|null */
    public function getTimeoutSeconds() {
        return $this->_timeoutSeconds;
    }

    /**
     * The number of seconds to wait for the data to be sent.
     *
     * Fractions of a second, down to the millisecond, may be specified.  Setting this to
     * null would use the default timeout value specified by the transport.
     *
     * @param float|null $timeoutSeconds
     * @return $this
     */
    public function setTimeoutSeconds($timeoutSeconds) {
        if ($timeoutSeconds != null) {
            $this->_timeoutSeconds = floatval($timeoutSeconds);
        } else {
            $this->_timeoutSeconds = null;
        }
        return $this;
    }
}