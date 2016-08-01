<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->find(1);

        //$response = $this->get('player')->refresh($user);

        return $this->render('default/index.html.twig', array(
            'user' => $user
        ));
    }
}
