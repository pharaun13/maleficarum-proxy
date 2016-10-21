<?php
/**
 * This trait provides functionality common to all classes dependant on the \Maleficarum\Proxy\Logger namespace
 */

namespace Maleficarum\Proxy\Logger;

trait Dependant
{
    /**
     * Internal storage for the logger object.
     *
     * @var \Psr\Log\LoggerInterface|null
     */
    protected $logger = null;

    /* ------------------------------------ Dependant methods START ------------------------------------ */
    /**
     * Inject a new logger object.
     *
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(\Psr\Log\LoggerInterface $logger) {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Fetch the currently assigned logger object.
     *
     * @return \Psr\Log\LoggerInterface|null
     */
    public function getLogger() {
        return $this->logger;
    }

    /**
     * Detach the currently assigned logger object.
     *
     * @return $this
     */
    public function detachLogger() {
        $this->logger = null;

        return $this;
    }
    /* ------------------------------------ Dependant methods END -------------------------------------- */
}
