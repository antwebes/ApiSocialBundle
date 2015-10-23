<?php

namespace Ant\Bundle\ApiSocialBundle\Provider;

class Advertising
{	
	/**
	 * @var string
	 */
	protected $lotery;
	
	public function __construct()
	{
		$this->lotery = rand(1, 100);
	}
	
	public function getBeneficiary()
	{
		if ($this->lotery > 59){
			return 'awc';
		}else{
			return 'affiliate';
		}
	}
}