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

class Cluster extends EntityRepository
{
    public function ranking($page = 1, $max = 10, $order = 'xp') {
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

        $dql = $this->createQueryBuilder('cluster');

        $dql->addSelect("SUM(user.".$order.") AS points");

        $dql->join('cluster.users', 'user');

        $dql->where('user.cheater != TRUE');

        $dql->orderBy("points", 'DESC');
        $dql->groupBy('cluster.id');

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
}
