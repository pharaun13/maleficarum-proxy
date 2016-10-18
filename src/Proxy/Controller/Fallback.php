<?php
/**
 * This is the default controller for the proxy app. It throws the 404 error on all actions.
 * 
 * @extends \Maleficarum\Proxy\Controller\Generic
 */

namespace Maleficarum\Proxy\Controller;

class Fallback extends \Maleficarum\Proxy\Controller\Generic {
	/**
	 * @see \Maleficarum\Proxy\Controller\Generic::__remap()
	 */
	public function _remap($method) {
		throw new \Maleficarum\Exception\NotFoundException('404 - not found.');
	}
}
