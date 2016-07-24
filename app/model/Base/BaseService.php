<?php

namespace App\Model\Services;

use App\Model\Entity\BaseEntity;
use Kdyby;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Nette;
use Nette\Object;


abstract class BaseService extends Object
{
	/** @var EntityManager */
	protected $em;

	/** @var EntityRepository */
	protected $repository;

	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em, $repository = null)
	{
		$this->em = $em;
		$this->repository = $repository;
	}


	///////////////////// EM based

	public function save($entity)
	{
		$this->em->persist($entity);
		$this->em->flush();
	}

	public function saveDelayed(BaseEntity $entity)
	{
		$this->em->persist($entity);
	}

	public function delete($entity)
	{
		$this->em->remove($entity);
		$this->em->flush();
	}
	public function deleteDelayed(BaseEntity $entity){
		$this->em->remove($entity);
	}

	public function flushAll()
	{
		$this->em->flush();
	}

	///////////////////// Repository based

	public function find($id)
	{
		return $this->repository->find($id);
	}

	public function findAll()
	{
		return $this->repository->findAll();
	}

	public function findPairs($criteria, $value = NULL, $orderBy = array(), $key = NULL)
	{
		return $this->repository->findPairs($criteria, $value, $orderBy, $key);
	}

	public function findBy(array $criteria, array $order = null, $limit = null, $offset = null)
	{
		return $this->repository->findBy($criteria, $order, $limit, $offset);
	}

	public function findOneBy(array $criteria, array $orderBy = null)
	{
		return $this->repository->findOneBy($criteria, $orderBy);
	}


}
