<?php

namespace Dsyph3r\JobeetBundle\Repository;

use Doctrine\ORM\EntityRepository;

class JobRepository extends EntityRepository
{
    public function getJobs()
    {
    	$qb = $this->createQueryBuilder('j')
                   ->select('j');  
    	
    	return $qb->getQuery()->getResult();    	
    }
}