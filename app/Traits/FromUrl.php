<?php

namespace App\Traits;

trait UrlFrom
{
    /**
	 * Set the URL of the previous request.
	 *
	 * @param  string  $url
	 * @return $this
	 */
	public function from(string $url)
	{
		$this->app['session']->setPreviousUrl($url);

		return $this;
	}
}
