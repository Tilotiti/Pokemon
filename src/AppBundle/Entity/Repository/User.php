<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 01/08/2016
 * Time: 17:18
 */

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class User extends EntityRepository
{
    public function ranking($page = 1, $max = 10) {
        if(!is_numeric($page)) {
            throw new \InvalidArgumentException(
                '$page must be an integer ('.gettype($page).' : '.$page.')'
            );
        }

        if(!is_numeric($page)) {
            throw new \InvalidArgumentException(
                '$max must be an integer ('.gettype($max).' : '.$max.')'
            );
        }

        $dql = $this->createQueryBuilder('user');

        $dql->andWhere('user.username IS NOT NULL');
        $dql->orderBy("user.xp", 'DESC');

        $firstResult = ($page - 1) * $max;

        $query = $dql->getQuery();
        $query->setFirstResult($firstResult);
        $query->setMaxResults($max);

        $paginator = new Paginator($query);

        if(($paginator->count() <=  $firstResult) && $page != 1) {
            throw new NotFoundHttpException('Page not found');
        }
        return $paginator;
    }

    public function teamXpStats() {
        $dql = $this->createQueryBuilder('user');
        $dql->select('SUM(user.xp) AS points');
        $dql->addSelect('user.team');
        $dql->groupBy('user.team');

        $query = $dql->getQuery();

        return $query->getResult();
    }

    public function teamKmStats() {
        $dql = $this->createQueryBuilder('user');
        $dql->select('SUM(user.km) AS kilometre');
        $dql->addSelect('user.team');
        $dql->groupBy('user.team');

        $query = $dql->getQuery();

        return $query->getResult();
    }
}
