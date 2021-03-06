<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 03/08/2016
 * Time: 16:47
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Cluster;
use AppBundle\Entity\Notification;
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
        switch($request->query->get('order', 'xp')) {
            case "name":
                $orderBy = 'cluster.name';
                break;
            case "members":
                $orderBy = 'COUNT(user.id)';
                break;
            case "level":
                $orderBy = 'AVG(user.level)';
                break;
            case "points":
                $orderBy = 'SUM(user.xp)';
                break;
            case "xp":
                $orderBy = 'AVG(user.xp)';
                break;
            case "km":
                $orderBy = 'AVG(user.km)';
                break;
            case "discovered":
                $orderBy = 'AVG(user.discovered)';
                break;
            case "catched":
                $orderBy = 'AVG(user.catched)';
                break;
            case "evolved":
                $orderBy = 'AVG(user.evolved)';
                break;
            default:
                return $this->redirectToRoute('cluster');
                break;
        }

        $listCluster = $this->getDoctrine()
            ->getRepository('AppBundle:Cluster')
            ->ranking(
                $request->query->getInt('page', 1),
                20,
                $orderBy,
                $request->query->get('way', 'DESC')
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
            'label' => "create",
            'attr' => array(
                'class' => 'btn btn-default'
            )
        ));

        $form->handleRequest($request);

        if($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($cluster);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $this->get('translator')->trans("success.cluster.create", [], "flash"));

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

        switch($request->query->get('order', 'xp')) {
            case "sign":
                $orderBy = 'user.sign';
                break;
            case "pokedex":
                $orderBy = 'COUNT(pokedex)';
                break;
            case "maxcp":
                $orderBy = 'MAX(pokedex.cp)';
                break;
            case "totalcp":
                $orderBy = 'SUM(pokedex.cp)';
                break;
            default:
                $orderBy = 'user.'.$request->query->get('order', 'xp');
                break;
        }

        $listUser = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->rankingCluster(
            $request->query->getInt('page', 1),
            20,
            $orderBy,
            $request->query->get('way', 'DESC'),
            $cluster
        );

        return $this->render('cluster/view.html.twig', array(
            'cluster' => $cluster,
            'listRequest' => $listRequest,
            'listUser' => $listUser
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
            $this->addFlash('danger', $this->get('translator')->trans("error.user.rank", [], "flash"));

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        }

        $form = $this->createForm(ClusterType::class, $cluster);

        $form->add('submit', SubmitType::class, array(
            'label' => "edit",
            'attr' => array(
                'class' => 'btn btn-default'
            )
        ));

        $form->handleRequest($request);

        if($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($cluster);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $this->get('translator')->trans("success.cluster.edit", [], "flash"));

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
                $this->addFlash('danger', $this->get('translator')->trans("error.group.alreadyMember", [], "flash"));

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

            $this->addFlash('success', $this->get('translator')->trans("success.cluster.join", [], "flash"));

            foreach ($cluster->getUsers() as $user) {
                // Notification
                $notification = new Notification();
                $notification->setUser($user);
                $notification->setIcon('list');
                $notification->setCategory('cluster');
                $notification->setCode('cluster.join');
                $notification->setParams(array(
                    'username' => $this->getUser()->getUsername(),
                    'cluster' => $cluster->getName()
                ));
                $notification->setRoute('cluster_view');
                $notification->setRouteParams(array(
                    'cluster' => $cluster->getId()
                ));

                $this->getDoctrine()
                    ->getManager()
                    ->persist($notification);
            }

            $this->getDoctrine()
                ->getManager()
                ->flush();

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        } else {
            if($cluster->hasRequestFrom($this->getUser())) {
                $this->addFlash('danger', "Votre demande est déjà en attente.");

                return $this->redirectToRoute("cluster_view", array(
                    'cluster' => $cluster->getId()
                ));
            }

            // Make a request
            $this->addFlash('warning', $this->get('translator')->trans("success.cluster.request.wait", [], "flash"));

            $request = new \AppBundle\Entity\Request();
            $request->setUser($this->getUser());
            $request->setCluster($cluster);

            $this->getDoctrine()
                ->getManager()
                ->persist($request);

            // Notification
            $notification = new Notification();
            $notification->setUser($cluster->getAdmin());
            $notification->setIcon('list');
            $notification->setCategory('cluster');
            $notification->setCode('cluster.request.add');
            $notification->setParams(array(
                'username' => $this->getUser()->getUsername(),
                'cluster' => $cluster->getName()
            ));
            $notification->setRoute('cluster_view');
            $notification->setRouteParams(array(
                'cluster' => $cluster->getId()
            ));

            $this->getDoctrine()
                ->getManager()
                ->persist($notification);

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
            $this->addFlash('danger', $this->get('translator')->trans("error.cluster.request.unknown", [], "flash"));
        } else {
            $this->addFlash('success', $this->get('translator')->trans("success.cluster.request.canceled", [], "flash"));

            $this->getDoctrine()
                ->getManager()
                ->remove($request);

            // Notification
            $notification = new Notification();
            $notification->setUser($cluster->getAdmin());
            $notification->setIcon('list');
            $notification->setCategory('cluster');
            $notification->setCode('cluster.request.cancel');
            $notification->setParams(array(
                'username' => $this->getUser()->getUsername(),
                'cluster' => $cluster->getName()
            ));
            $notification->setRoute('cluster_view');
            $notification->setRouteParams(array(
                'cluster' => $cluster->getId()
            ));

            $this->getDoctrine()
                ->getManager()
                ->persist($notification);

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
            $this->addFlash('danger', $this->get('translator')->trans("error.user.rank", [], "flash"));

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $request->getCluster()->getId()
            ));
        }

        foreach($request->getCluster()->getUsers() as $user) {

            // Notification aux membres du groupe
            $notification = new Notification();
            $notification->setUser($user);
            $notification->setIcon('list');
            $notification->setCategory('cluster');
            $notification->setCode('cluster.join');
            $notification->setParams(array(
                'cluster' => $request->getCluster()->getName(),
                'user' => $request->getUser()->getUsername()
            ));
            $notification->setRoute('cluster_view');
            $notification->setRouteParams(array(
                'cluster' => $request->getCluster()->getId()
            ));

            $this->getDoctrine()
                ->getManager()
                ->persist($notification);
        }


        $request->getCluster()->addUser($request->getUser());

        $this->getDoctrine()
            ->getManager()
            ->persist($request->getCluster());

        // Notification au membre concerné
        $notification = new Notification();
        $notification->setUser($request->getUser());
        $notification->setIcon('list');
        $notification->setCategory('cluster');
        $notification->setCode('cluster.request.accept');
        $notification->setParams(array(
            'cluster' => $request->getCluster()->getName()
        ));
        $notification->setRoute('cluster_view');
        $notification->setRouteParams(array(
            'cluster' => $request->getCluster()->getId()
        ));

        $this->getDoctrine()
            ->getManager()
            ->persist($notification);

        // Supprimer la request
        $this->getDoctrine()
            ->getManager()
            ->remove($request);

        $this->getDoctrine()
            ->getManager()
            ->flush();

        $this->addFlash('success', $this->get('translator')->trans("success.cluster.request.ok", [], "flash"));

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
            $this->addFlash('danger', $this->get('translator')->trans("error.user.rank", [], "flash"));

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $request->getCluster()->getId()
            ));
        }

        $this->getDoctrine()
            ->getManager()
            ->remove($request);

        // Notification
        $notification = new Notification();
        $notification->setUser($request->getUser());
        $notification->setIcon('list');
        $notification->setCategory('cluster');
        $notification->setCode('cluster.request.reject');
        $notification->setParams(array(
            'cluster' => $request->getCluster()->getName()
        ));
        $notification->setRoute('cluster_view');
        $notification->setRouteParams(array(
            'cluster' => $request->getCluster()->getId()
        ));

        $this->getDoctrine()
            ->getManager()
            ->persist($notification);

        $this->getDoctrine()
            ->getManager()
            ->flush();

        $this->addFlash('success', $this->get('translator')->trans("success.cluster.request.rejected", [], "flash"));

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
            $this->addFlash('danger', $this->get('translator')->trans("error.user.rank", [], "flash"));

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        }

        if(!$cluster->hasUser($user)) {
            $this->addFlash('danger', $this->get('translator')->trans("error.cluster.user", [], "flash"));

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

        // Notification
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setIcon('list');
        $notification->setCategory('cluster');
        $notification->setCode('cluster.abandon');
        $notification->setParams(array(
            'cluster' => $cluster->getName()
        ));
        $notification->setRoute('cluster_view');
        $notification->setRouteParams(array(
            'cluster' => $cluster->getId()
        ));

        $this->getDoctrine()
            ->getManager()
            ->persist($notification);

        $this->getDoctrine()
            ->getManager()
            ->flush();

        $this->addFlash('success', $this->get('translator')->trans("success.cluster.reject", [], "flash"));

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
            $this->addFlash('danger', $this->get('translator')->trans("error.cluster.quit", [], "flash"));

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        }

        if(!$cluster->hasUser($this->getUser())) {
            $this->addFlash('danger', $this->get('translator')->trans("error.user.rank", [], "flash"));

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

        // Notification
        $notification = new Notification();
        $notification->setUser($cluster->getAdmin());
        $notification->setIcon('list');
        $notification->setCategory('cluster');
        $notification->setCode('cluster.quit');
        $notification->setParams(array(
            'username' => $this->getUser()->getUsername(),
            'cluster' => $cluster->getName()
        ));
        $notification->setRoute('cluster_view');
        $notification->setRouteParams(array(
            'cluster' => $cluster->getId()
        ));

        $this->getDoctrine()
            ->getManager()
            ->persist($notification);

        $this->getDoctrine()
            ->getManager()
            ->flush();

        $this->addFlash('success', $this->get('translator')->trans("success.cluser.quit", [], "flash"));

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
            $this->addFlash('danger', $this->get('translator')->trans("error.user.rank", [], "flash"));

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        }

        foreach($cluster->getUsers() as $user) {
            // Notification
            $notification = new Notification();
            $notification->setUser($user);
            $notification->setIcon('list');
            $notification->setCategory('cluster');
            $notification->setCode('cluster.remove');
            $notification->setParams(array(
                'username' => $this->getUser()->getUsername(),
                'cluster' => $cluster->getName()
            ));
            $notification->setRoute('cluster');

            $this->getDoctrine()
                ->getManager()
                ->persist($notification);
        }

        $this->getDoctrine()
            ->getManager()
            ->remove($cluster);

        $this->getDoctrine()
            ->getManager()
            ->flush();

        $this->addFlash('success', $this->get('translator')->trans("success.cluster.deleted", [], "flash"));

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
            $this->addFlash('danger', $this->get('translator')->trans("error.user.rank", [], "flash"));

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        }

        if(!$cluster->hasUser($user)) {
            $this->addFlash('danger', $this->get('translator')->trans("error.cluster.user", [], "flash"));

            return $this->redirectToRoute("cluster_view", array(
                'cluster' => $cluster->getId()
            ));
        }

        $cluster->setAdmin($user);

        // Notification
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setIcon('list');
        $notification->setCategory('cluster');
        $notification->setCode('cluster.transfert');
        $notification->setParams(array(
            'username' => $this->getUser()->getUsername(),
            'cluster' => $cluster->getName()
        ));
        $notification->setRoute('cluster_view');
        $notification->setRouteParams(array(
            'cluster' => $cluster->getId()
        ));

        $this->getDoctrine()
            ->getManager()
            ->persist($notification);

        $this->getDoctrine()
            ->getManager()
            ->persist($cluster);

        $this->getDoctrine()
            ->getManager()
            ->flush();

        $this->addFlash('success', $this->get('translator')->trans("success.cluster.transfert", [], "flash"));

        return $this->redirectToRoute("cluster_view", array(
            'cluster' => $cluster->getId()
        ));
    }
}
