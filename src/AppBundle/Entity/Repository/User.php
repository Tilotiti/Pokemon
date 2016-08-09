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
    public function ranking($page = 1, $max = 10, $order = 'xp', $way = 'DESC') {
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

        $dql->leftJoin('user.pokedex', 'pokedex');

        $dql->addSelect($order.' as orderParam');

        $dql->andWhere('user.cheater = FALSE');

        $dql->andHaving('user.username IS NOT NULL');
        $dql->andHaving($order.' IS NOT NULL');

        $dql->groupBy('user.id');
        $dql->orderBy('orderParam', $way);

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

    public function rankingCluster($page = 1, $max = 10, $order = 'xp', $way = 'DESC', $cluster) {
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

        $dql->join('user.clusters', 'cluster');

        $dql->andWhere('user.username IS NOT NULL');
        $dql->andWhere('user.cheater = FALSE');
        $dql->andWhere('cluster = :cluster');
        $dql->setParameter('cluster', $cluster);

        $dql->groupBy('user');
        $dql->orderBy("user.".$order, $way);

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

    public function statsTeam($param) {
        $dql = $this->createQueryBuilder('user');
        $dql->select('SUM(user.'.$param.') AS points');
        $dql->addSelect('user.team');
        $dql->andWhere('user.cheater = FALSE');
        $dql->groupBy('user.team');

        $query = $dql->getQuery();

        return $query->getResult();
    }
}
