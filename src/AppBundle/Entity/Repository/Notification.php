<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 04/04/2016
 * Time: 18:29
 */
namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\Tools\Pagination\Paginator;

class Notification extends EntityRepository
{

    public function findLastForUser($user, $max) {
        $dql = $this->createQueryBuilder('notification');

        $dql->andWhere("notification.user = :user");
        $dql->setParameter("user", $user);
        $dql->orderBy('notification.datetime', 'DESC');
        $dql->setMaxResults($max);

        return $dql->getQuery()->getResult();
    }

    public function countUnreadForUser($user) {
        $dql = $this->createQueryBuilder('notification');

        $dql->select('COUNT(notification)');
        $dql->andWhere("notification.user = :user");
        $dql->setParameter("user", $user);
        $dql->andWhere('notification.isRead = false');

        return $dql->getQuery()->getSingleScalarResult();
    }

    public function listNotification($page = 1, $max = 10, $user) {
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

        $dql = $this->createQueryBuilder('notification');

        $dql->andWhere("notification.user = :user");
        $dql->setParameter("user", $user);
        $dql->orderBy('notification.datetime', 'DESC');

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
