<?php
/**
 * Librairie immodvisor
 * Afin de profiter pleinement des mises à jours de cette librarie, il est fortement recommandé de ne pas la modifier
 * @author Jeremy Humbert <jeremy@immodvisor.com>
 * @copyright 2019 immodvisor
 */
namespace Bakhshi\Immodvisor;


class ImmodvisorConfig {
	
	public const VERSION = '1.7.0';
    public const URL_API_PROD = 'https://api.immodvisor.com/';
	public const URL_API_DEV = 'http://api.immodvisor.digital/';
	
	private $env = 'prod';
	
	public function env($env): ImmodvisorConfig
    {
		if(is_string($env) && in_array($env, array('dev', 'prod'))) {
			$this->env = (string) $env;
		}
		return $this;
	}
	
	protected function getUrlApi(): string
    {
		if($this->env == 'dev') {
			return self::URL_API_DEV;
		}
		return self::URL_API_PROD;
	}
	
}
