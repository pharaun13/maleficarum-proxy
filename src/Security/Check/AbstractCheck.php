<?php
/**
 * This class is the basis for all security check classes.
 */

namespace Maleficarum\Proxy\Security\Check;

abstract class AbstractCheck
{
    /**
     * Execute specific check logic.
     *
     * @param array $data
     *
     * @return bool
     */
    abstract public function execute(array $data = []) : bool;
}
