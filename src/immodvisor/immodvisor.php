<?php
/**
 * Librairie immodvisor
 * Afin de profiter pleinement des mises à jours de cette librarie, il est fortement recommandé de ne pas la modifier
 * @author Jeremy Humbert <jeremy@immodvisor.com>
 * @copyright 2019 immodvisor
 */
namespace immodvisor;
include __DIR__.'/Api.php';
include __DIR__.'/Date.php';
include __DIR__.'/Utils.php';

class immodvisor {
	
	const VERSION = '1.7.0';
    const URL_API_PROD = 'https://api.immodvisor.com/';
	const URL_API_DEV = 'http://api.immodvisor.digital/';
	
	private $env = 'prod';
	
	public function env($env) {
		if(is_string($env) && in_array($env, array('dev', 'prod'))) {
			$this->env = (string) $env;
		}
		return $this;
	}
	
	protected function getUrlApi() {
		if($this->env == 'dev') {
			return self::URL_API_DEV;
		}
		return self::URL_API_PROD;
	}
	
}
