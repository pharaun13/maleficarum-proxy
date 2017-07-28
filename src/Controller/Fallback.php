<?php
/**
 * This is the default controller for the Proxy app. It throws the 404 error on all actions.
 *
 * @extends \Maleficarum\Proxy\Controller\Generic
 */

namespace Maleficarum\Proxy\Controller;

class Fallback extends \Maleficarum\Proxy\Controller\Generic
{
    /**
     * Throws not found exception
     * 
     * @see \Maleficarum\Proxy\Controller\Generic::__remap()
     * @param string $method
     *
     * @return void
     * @throws \Maleficarum\Exception\NotFoundException
     */
    public function __remap(string $method) {
        $this->respondToNotFound('404 - page not found.');
    }
}
