<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request) {
        $listUser = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->ranking(
            $request->query->getInt('page', 1),
            20,
            $request->query->get('order', 'xp')
        );

        $statsTeam = array();

        // Pokedex
        foreach(array('xp', 'km', 'discovered', 'catched') as $stats) {
            $listStats = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->statsTeam($stats);

            $statsTeam[$stats] = array(array(
                'name' => "Sans équipe",
                'points' => 0
            ), array(
                'name' => "Team bleu",
                'points' => 0
            ), array(
                'name' => "Team rouge",
                'points' => 0
            ), array(
                'name' => "Team Jaune",
                'points' => 0
            ));

            foreach ($listStats as $points) {
                $statsTeam[$stats][$points['team']]['points'] = $points["points"];
            }
        }

        return $this->render('default/index.html.twig', array(
            'listUser' => $listUser,
            'statsTeam' => $statsTeam,
        ));
    }


    /**
     * @Route("/player/{username}", name="player", defaults={"username":"me"})
     */
    public function playerAction(Request $request, $username) {

        // Utilisateur
        if($username == "me") {
            $user = $this->getUser();
        } else {
            $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findOneBy(array(
                'username' => $username
            ));
        }

        if(!$user) {
            $this->addFlash('danger', $this->get('translator')->trans("error.user.any", [], "flash"));

            if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                return $this->redirectToRoute("player", array(
                    'username' => $this->getUser()->getUsername()
                ));
            } else {
                return $this->redirectToRoute('login');
            }
        }

        // Pokédex
        $listPokemon = $this->getDoctrine()->getManager()->getRepository('AppBundle:Pokedex')->findBy(array(
            'user' => $user
        ), array(
            $request->query->get('order', 'cp') => $request->query->get('order', 'cp') == "pokemon" ? 'ASC' : 'DESC'
        ));

        return $this->render('default/player.html.twig', array(
            'user' => $user,
            'listPokemon' => $listPokemon
        ));
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request) {
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute("index");
        }

        if($request->isMethod('POST')) {
            // Inscription ou Connexion
            $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findOneBy(array(
                'email' => $request->get('login')
            ));

            if(!$user) {
                $user = new User();
                $user->setRoles(array('ROLE_USER'));
                $user->setCheater(false);
            }

            $user->setEmail($request->get('login'));

            $user = $this->get('player')->refresh($user, $request->get('password'));

            if(!$user) {
                // Erreur de connexion
                $this->addFlash('danger', $this->get('translator')->trans("error.login", [], "flash"));

                return $this->redirectToRoute("login");
            }

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            // Connexion manuelle
            $token = new UsernamePasswordToken($user, null, 'app', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_app', serialize($token));
            $this->get('session')->set('password', $request->get('password'));

            $this->addFlash('success', $this->get('translator')->trans("success.login", [], "flash"));

            return $this->redirectToRoute("player");
        }

        return $this->render('default/login.html.twig');
    }

    /**
     * @Route("/refresh", name="refresh")
     */
    public function refreshAction() {
        if($this->get('player')->refresh($this->getUser(), $this->get('session')->get('password'))) {
            return new Response('ok');
        } else {
            return new Response('Err');
        }
    }

    /**
     * @Route("/account", name="account")
     */
    public function accountAction(Request $request) {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);

        $form->add('submit', SubmitType::class, array(
            'label' => "edit",
            'attr' => array(
                'class' => 'btn btn-default'
            )
        ));

        $form->handleRequest($request);

        if($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $this->get('translator')->trans("success.refresh", [], "flash"));

            return $this->redirectToRoute('player', array(
                'username' => $user->getUsername()
            ));
        }

        return $this->render('default/account.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function editAction(Request $request, User $user) {
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            throw new NotFoundHttpException("Page not found");
        }

        $form = $this->createForm(UserType::class, $user);

        $form->add('cheater', ChoiceType::class, array(
            'label' => "player.cheater",
            'choices' => array(
                'Tricheur' => true,
                'Honnête' => false
            )
        ));

        $form->add('submit', SubmitType::class, array(
            'label' => "edit",
            'attr' => array(
                'class' => 'btn btn-default'
            )
        ));

        $form->handleRequest($request);

        if($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $this->get('translator')->trans("success.user.edit", [], "flash"));

            return $this->redirectToRoute('player', array(
                'username' => $user->getUsername()
            ));
        }

        return $this->render('default/edit.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    /**
     * Change the User Locale
     * @var Request
     * @var string $local User locale
     * @Route("/lang/{locale}", name="change_locale")
     * @return mixed
     */
    public function changeLocaleAction(Request $request, $locale) {
        $this->get('session')->set('_locale', $locale);

        if($this->getUser()) {
            $this->getUser()->setLocale($locale);

            $this->getDoctrine()
                ->getManager()
                ->persist($this->getUser());

            $this->getDoctrine()
                ->getManager()
                ->flush();
        }

        if($request->query->get('back')) {
            return $this->redirect($request->query->get('back'));
        } else {
            return $this->redirectToRoute('index');
        }
    }

    /**
     * @var Request $request
     * @Route("/notification", name="notification")
     * @return mixed
     */
    public function allNotificationAction(Request $request) {

        $listNotification = $this->getDoctrine()
            ->getRepository('AppBundle:Notification')
            ->listNotification(
                $request->query->getInt('page', 1),
                20,
                $this->getUser()
            );

        return $this->render('default/notification.html.twig', array(
            'listNotification' => $listNotification
        ));
    }

    /**
     * @Route("/notification/readAll", name="notification_read_all")
     */
    public function readAllNotificationAction() {

        if(!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $listNotification = $this->getDoctrine()
            ->getRepository('AppBundle:Notification')
            ->findBy(array(
                'user' => $this->getUser(),
                'isRead' => false
            ));

        foreach($listNotification as $notification) {
            $notification->setRead(true);

            $this->getDoctrine()
                ->getManager()
                ->persist($notification);
        }

        $this->getDoctrine()
            ->getManager()
            ->flush();

        return $this->redirectToRoute('notification');
    }

    /**
     * @Route("/login-check", name="login_check")
     */
    public function loginCheckAction() {
        return true;
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction() {
        return true;
    }
}
