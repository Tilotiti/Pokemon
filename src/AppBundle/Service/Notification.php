<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 04/08/2016
 * Time: 16:41
 */

namespace AppBundle\Service;


use AppBundle\Entity\Notification as NotificationEntity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Routing\Router;

class Notification extends \Twig_Extension
{
    private $db;
    private $token;
    private $router;

    public function __construct(EntityManager $db, TokenStorage $token, Router $router)
    {
        $this->db = $db;
        $this->token = $token;
        $this->router = $router;
    }

    public function getUser() {
        if(!$this->token->getToken()) {
            return null;
        }

        return $this->token->getToken()->getUser();
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('notificationLink', array($this, 'getLink'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('notificationList', array($this, 'getList'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('notificationCountUnread', array($this, 'countUnread'), array(
                'is_safe' => array('html')
            ))
        );
    }

    public function getName()
    {
        return 'app_notification';
    }

    public function getLink(NotificationEntity $notification)
    {
        $params = $notification->getRouteParams();
        $params['notification'] = $notification->getId();
        return $this->router->generate($notification->getRoute(), $params);
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        if ($event->getRequest()->query->get('notification')) {
            $notification = $this->db->getRepository('AppBundle:Notification')->find($event->getRequest()->query->get('notification'));

            if ($notification && !$notification->isRead()) {
                // Notification existante et non-lue

                if ($this->token->getToken()->getUser() == $notification->getUser()) {
                    // L'utilisateur a les droits sur la notification

                    // La notification est marquée comme lue
                    $notification->setRead(true);
                    $this->db->persist($notification);
                    $this->db->flush();
                }
            }
        }
    }

    /**
     * Récupère la liste des dernières notifications de l'utilisateur connecté
     */
    public function getList($max)
    {
        return $this->db->getRepository('AppBundle:Notification')->findLastForUser($this->getUser(), $max);
    }

    /**
     * Compte le nombre de notifications non-lues de l'utilisateur connecté
     */
    public function countUnread()
    {
        return $this->db->getRepository('AppBundle:Notification')->countUnreadForUser($this->getUser());
    }
}
