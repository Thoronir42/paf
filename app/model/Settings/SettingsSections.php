<?php

namespace App\Model\Settings;


use App\Model\Services\BaseService;
use Kdyby\Doctrine\EntityManager;

class SettingsSections extends BaseService
{
	public function __construct(EntityManager $em)
	{
		parent::__construct($em, $em->getRepository(SettingsSection::class));
	}

	public function findAllOrdered(){
		return $this->findBy([], ['domain' => 'ASC']);
	}

	public function findByDomain($domain)
	{
		return $this->findOneBy(['domain' => $domain]);
	}
}
