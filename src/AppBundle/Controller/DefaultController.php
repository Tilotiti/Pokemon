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

        $listStats = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->teamXpStats();

        $teamXpStats = array(array(
            'name' => "Sans équipe",
            'xp' => 0,
            'color' => '#F1F3F6'
        ), array(
            'name' => "Team bleu",
            'xp' => 0,
            'color' => '#94DBEE'
        ), array(
            'name' => "Team rouge",
            'xp' => 0,
            'color' => '#EC8484'
        ), array(
            'name' => "Team Jaune",
            'xp' => 0,
            'color' => '#FFFF99'
        ));

        foreach($listStats as $stats) {
            $teamXpStats[$stats['team']]['xp'] = $stats["points"];
        }

        $listStats = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->teamKmStats();

        $teamKmStats = array(array(
            'name' => "Sans équipe",
            'km' => 0,
            'color' => '#F1F3F6'
        ), array(
            'name' => "Team bleu",
            'km' => 0,
            'color' => '#94DBEE'
        ), array(
            'name' => "Team rouge",
            'km' => 0,
            'color' => '#EC8484'
        ), array(
            'name' => "Team Jaune",
            'km' => 0,
            'color' => '#FFFF99'
        ));

        foreach($listStats as $stats) {
            $teamKmStats[$stats['team']]['km'] = $stats["kilometre"];
        }

        return $this->render('default/index.html.twig', array(
            'listUser' => $listUser,
            'teamXpStats' => $teamXpStats,
            'teamKmStats' => $teamKmStats
        ));
    }


    /**
     * @Route("/player/{username}", name="player", defaults={"username":"me"})
     */
    public function playerAction(Request $request, $username) {

        // Classement général
        $listUser = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->ranking(
            $request->query->getInt('page', 1),
            10
        );

        // Utilisateur
        if($username == "me") {
            $user = $this->getUser();
        } else {
            $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findOneBy(array(
                'username' => $username
            ));
        }

        if(!$user) {
            $this->addFlash('danger', "Cet utilisateur n'existe pas.");

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
            'cp' => 'DESC'
        ));

        return $this->render('default/player.html.twig', array(
            'user' => $user,
            'listUser' => $listUser,
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

            }

            $user->setEmail($request->get('login'));
            $user->encryptPassword($request->get('password'));
            $user->setCheater(false);

            $user = $this->get('player')->refresh($user);

            if(!$user) {
                // Erreur de connexion
                $this->addFlash('danger', "Une erreur de connexion s'est produite. Vos identifiants Google ne sont pas valides ou les serveurs de Pokemon Go sont hors-ligne. Si ce n'est pas le cas, rendez-vous ici pour valider l'accès à votre compte Google : https://g.co/allowaccess.");

                return $this->redirectToRoute("login");
            }

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            // Connexion manuelle
            $token = new UsernamePasswordToken($user, null, 'app', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_app', serialize($token));

            $this->addFlash('success', "Vous êtes maintenant connecté et vos informations sont à jour.");

            return $this->redirectToRoute("player");
        }

        return $this->render('default/login.html.twig');
    }

    /**
     * @Route("/refresh", name="refresh")
     */
    public function refreshAction() {
        $this->get('player')->refresh($this->getUser());
        return new Response('ok');
    }

    /**
     * @Route("/account", name="account")
     */
    public function accountAction(Request $request) {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);

        $form->add('submit', SubmitType::class, array(
            'label' => "Modifier",
            'attr' => array(
                'class' => 'btn btn-default'
            )
        ));

        $form->handleRequest($request);

        if($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Votre profil a été mis à jour.");

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
            'label' => "Tricheur",
            'choices' => array(
                'Tricheur' => true,
                'Honnête' => false
            )
        ));

        $form->add('submit', SubmitType::class, array(
            'label' => "Modifier",
            'attr' => array(
                'class' => 'btn btn-default'
            )
        ));

        $form->handleRequest($request);

        if($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Le profil de ".$user->getUsername()." a été mis à jour.");

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
