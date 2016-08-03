<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 03/08/2016
 * Time: 16:47
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Cluster;
use AppBundle\Entity\User;
use AppBundle\Form\ClusterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ClusterController
 * @package AppBundle\Controller\Cluster
 * @Route("/cluster")
 */
class ClusterController extends Controller
{
    /**
     * Cluster rank
     * Cluster finder
     * My clusters
     *
     * @var Request $request
     * @Route("", name="cluster")
     * @return mixed
     */
    public function indexAction(Request $request) {
        $listCluster = $this->getDoctrine()
            ->getRepository('AppBundle:Cluster')
            ->ranking(
                $request->query->getInt('page', 1),
                20,
                $request->query->get('order', 'xp')
            );

        return $this->render('cluster/index.html.twig', array(
            'listCluster' => $listCluster
        ));
    }

    /**
     * Add a cluster
     *
     * @var Request $request
     * @Route("/add", name="cluster_add")
     * @return mixed
     */
    public function addClusterAction(Request $request) {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }

        $cluster = new Cluster();
        $cluster->setAdmin($this->getUser());
        $cluster->addUser($this->getUser());

        $form = $this->createForm(ClusterType::class, $cluster);

        $form->add('submit', SubmitType::class, array(
            'label' => "Créer",
            'attr' => array(
                'class' => 'btn btn-default'
            )
        ));

        $form->handleRequest($request);

        if($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($cluster);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Votre groupe a été créé.");

            return $this->redirectToRoute('cluster_view', array(
                'cluster' => $cluster->getId()
            ));
        }

        return $this->render('cluster/add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Cluster details
     *
     * @param Request $request
     * @param Cluster $cluster
     * @Route("/view/{cluster}", name="cluster_view")
     * @return mixed
     */
    public function clusterViewAction(Request $request, Cluster $cluster) {
        $listRequest = $this->getDoctrine()
            ->getRepository('AppBundle:Request')
            ->findBy(array(
                'cluster' => $cluster
            ));

        $listPlayer = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->rankingCluster(
                $request->query->getInt('page', 1),
                20,
                $request->query->get('order', 'xp'),
                $cluster
            );

        return $this->render('cluster/view.html.twig', array(
            'cluster' => $cluster,
            'listRequest' => $listRequest,
            'listUser' => $listPlayer
        ));
    }

    /**
     * Edit Cluster
     *
     * @param Cluster $cluster
     * @Route("/edit/{cluster}", name="cluster_edit")
     * @return mixed
     */
    public function clusterEditAction(Request $request, Cluster $cluster) {
        if(!$this->getUser()->isRole('ROLE_ADMIN') && $cluster->getAdmin() != $this->getUser()) {
            $this->addFlash('error', "Vous n'avez pas les droits pour modifier ce groupe.");

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        }

        $form = $this->createForm(ClusterType::class, $cluster);

        $form->add('submit', SubmitType::class, array(
            'label' => "Modifier",
            'attr' => array(
                'class' => 'btn btn-default'
            )
        ));

        $form->handleRequest($request);

        if($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($cluster);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Votre groupe a été modifié.");

            return $this->redirectToRoute('cluster_view', array(
                'cluster' => $cluster->getId()
            ));
        }

        return $this->render('cluster/edit.html.twig', array(
            'cluster' => $cluster,
            'form' => $form->createView()
        ));
    }

    /**
     * Join the cluster
     *
     * @param Cluster $cluster
     * @Route("/join/{cluster}", name="cluster_join")
     * @return mixed
     */
    public function clusterJoinAction(Cluster $cluster) {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }

        // Check that the user is not a member already
        foreach($this->getUser()->getClusters() as $userCluster) {
            if($userCluster == $cluster) {
                $this->addFlash('error', "Vous êtes déjà membre de ce groupe.");

                return $this->redirectToRoute("cluster_view", array(
                    'cluster' => $cluster->getId()
                ));
            }
        }

        if($cluster->isOpened()) {
            // join the group
            $cluster->addUser($this->getUser());

            $this->getDoctrine()
                ->getManager()
                ->persist($cluster);

            $this->getDoctrine()
                ->getManager()
                ->flush();

            $this->addFlash('success', "Vous êtes maintenant membre de ce groupe.");

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        } else {
            if($cluster->hasRequestFrom($this->getUser())) {
                $this->addFlash('error', "Votre demande est déjà en attente.");

                return $this->redirectToRoute("cluster_view", array(
                    'cluster' => $cluster->getId()
                ));
            }

            // Make a request
            $this->addFlash('warning', "Une demande a été envoyée à l'administrateur du groupe.");

            $request = new \AppBundle\Entity\Request();
            $request->setUser($this->getUser());
            $request->setCluster($cluster);

            $this->getDoctrine()
                ->getManager()
                ->persist($request);

            $this->getDoctrine()
                ->getManager()
                ->flush();

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));

        }
    }

    /**
     * Cancel his own request for this Cluster
     *
     * @param Cluster $cluster
     * @Route("/cancelRequest/{cluster}", name="cluster_cancelRequest")
     * @return mixed
     */
    public function cancelRequestAction(Cluster $cluster) {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }

        $request = $this->getDoctrine()
            ->getRepository('AppBundle:Request')
            ->findOneBy(array(
               'user' => $this->getUser(),
                'cluster' => $cluster
            ));

        if(!$request) {
            $this->addFlash('error', "Vous n'avez pas demandé à faire parti de ce groupe.");
        } else {
            $this->addFlash('success', "Votre demande a été annulée");

            $this->getDoctrine()
                ->getManager()
                ->remove($request);

            $this->getDoctrine()
                ->getManager()
                ->flush();
        }

        return $this->redirectToRoute("cluster_view", array(
            'cluster' => $cluster->getId()
        ));
    }

    /**
     * Accept the request
     *
     * @param \AppBundle\Entity\Request $request
     * @Route("/accept/{request}", name="cluster_accept_request")
     *
     * @return mixed
     */
    public function clusterAcceptRequest(\AppBundle\Entity\Request $request) {
        if($request->getCluster()->getAdmin() != $this->getUser()) {
            $this->addFlash('error', "Vous n'avez pas les droits pour modifier ce groupe.");

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $request->getCluster()->getId()
            ));
        }

        $request->getCluster()->addUser($request->getUser());

        $this->getDoctrine()
            ->getManager()
            ->persist($request->getCluster());

        $this->getDoctrine()
            ->getManager()
            ->remove($request);

        $this->getDoctrine()
            ->getManager()
            ->flush();

        $this->addFlash('success', $request->getUser()->getUsername()." est maintenant membre de ce groupe.");

        return $this->redirectToRoute("cluster_view", array(
            'cluster' => $request->getCluster()->getId()
        ));
    }

    /**
     * Reject the request
     *
     * @param \AppBundle\Entity\Request $request
     * @Route("/reject/{request}", name="cluster_reject_request")
     *
     * @return mixed
     */
    public function clusterRejectRequest(\AppBundle\Entity\Request $request) {
        if($request->getCluster()->getAdmin() != $this->getUser()) {
            $this->addFlash('error', "Vous n'avez pas les droits pour modifier ce groupe.");

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $request->getCluster()->getId()
            ));
        }

        $this->getDoctrine()
            ->getManager()
            ->remove($request);

        $this->getDoctrine()
            ->getManager()
            ->flush();

        $this->addFlash('success', "La demande de ".$request->getUser()->getUsername()." a été supprimée.");

        return $this->redirectToRoute("cluster_view", array(
            'cluster' => $request->getCluster()->getId()
        ));
    }

    /**
     * Remove an User from the team
     *
     * @param Cluster $cluster
     * @param User $user
     * @Route("/remove/{cluster}/{user}", name="cluster_remove_user")
     * @return mixed
     */
    public function clusterRemoveUser(Cluster $cluster, User $user) {
        if($cluster->getAdmin() != $this->getUser()) {
            $this->addFlash('error', "Vous n'avez pas les droits pour modifier ce groupe.");

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        }

        if(!$cluster->hasUser($user)) {
            $this->addFlash('error', $user->getUsername()." ne fait pas parti de ce groupe.");

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        }

        $listUser = array();
        foreach($cluster->getUsers() as $clusterUser) {
            if($clusterUser != $user) {
                $listUser[] = $clusterUser;
            }
        }

        $cluster->setUsers($listUser);

        $this->getDoctrine()
            ->getManager()
            ->persist($cluster);

        $this->getDoctrine()
            ->getManager()
            ->flush();

        $this->addFlash('success', $user->getUsername()." a été rejeté de ce groupe.");

        return $this->redirectToRoute("cluster_view", array(
            'cluster' => $cluster->getId()
        ));
    }

    /**
     * Remove his own membership
     *
     * @param Cluster $cluster
     * @Route("/abandon/{cluster}", name="cluster_abandon")
     * @return mixed
     */
    public function clusterAbandon(Cluster $cluster) {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }

        if($cluster->getAdmin() == $this->getUser()) {
            $this->addFlash('error', "Vous n'avez pas le droit de quitter ce groupe. Vous devez soit, en transférer la propriété, soit le supprimer.");

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        }

        if(!$cluster->hasUser($this->getUser())) {
            $this->addFlash('error', "Vous ne faites pas parti de ce groupe.");

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        }

        $listUser = array();
        foreach($cluster->getUsers() as $clusterUser) {
            if($clusterUser != $this->getUser()) {
                $listUser[] = $clusterUser;
            }
        }

        $cluster->setUsers($listUser);

        $this->getDoctrine()
            ->getManager()
            ->persist($cluster);

        $this->getDoctrine()
            ->getManager()
            ->flush();

        $this->addFlash('success', "Vous avez quitté ce groupe.");

        return $this->redirectToRoute("cluster_view", array(
            'cluster' => $cluster->getId()
        ));
    }

    /**
     * Remove the cluster
     *
     * @param Cluster $cluster
     * @Route("/remove/{cluster}", name="cluster_remove")
     * @return mixed
     */
    public function clusterRemove(Cluster $cluster) {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }

        if($cluster->getAdmin() != $this->getUser()) {
            $this->addFlash('error', "Vous n'avez pas le droit de supprimer ce groupe.");

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        }

        $this->getDoctrine()
            ->getManager()
            ->remove($cluster);

        $this->getDoctrine()
            ->getManager()
            ->flush();

        $this->addFlash('success', "Le groupe a été supprimé.");

        return $this->redirectToRoute("cluster");
    }

    /**
     * Change the cluster ownership
     *
     * @param Cluster $cluster
     * @param User $user
     * @Route("/ownership/{cluster}/{user}", name="cluster_ownership")
     * @return mixed
     */
    public function clusterOwnership(Cluster $cluster, User $user) {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('login');
        }

        if($cluster->getAdmin() != $this->getUser()) {
            $this->addFlash('error', "Vous n'avez pas le droit de changer le propriétaire du groupe.");

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        }

        if(!$cluster->hasUser($user)) {
            $this->addFlash('error', "Le nouveau propriétaire doit faire parti du groupe.");

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        }

        $cluster->setAdmin($user);

        $this->getDoctrine()
            ->getManager()
            ->persist($cluster);

        $this->getDoctrine()
            ->getManager()
            ->flush();

        $this->addFlash('success', "La propriétaire du groupe a changé.");

        return $this->redirectToRoute("cluster_view", array(
            'cluster' => $cluster->getId()
        ));
    }
}
