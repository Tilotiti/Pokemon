<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 15/08/2016
 * Time: 23:55
 */

namespace AppBundle\Controller;

use AppBundle\Entity\News;
use AppBundle\Form\NewsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Notification;

/**
 * Class NewsController
 * @package AppBundle\Controller
 * @Route("/news")
 */
class NewsController extends Controller
{
    /**
     * @Route("/", name="news")
     */
    public function indexAction(Request $request) {
        $listNews = $this->getDoctrine()->getRepository('AppBundle:News')->findPaginated(
            $request->getLocale(),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render("news/index.html.twig", array(
           'listNews' => $listNews
        ));
    }

    /**
     * @Route("/view/{news}", name="news_view")
     */
    public function viewAction(News $news) {
        return $this->render('news/view.html.twig', array(
            'news' => $news
        ));
    }

    /**
     * @Route("/add", name="news_add")
     */
    public function addAction(Request $request) {
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $this->addFlash('danger', $this->get('translator')->trans("error.user.rank", [], "flash"));
            return $this->redirectToRoute('news');
        }

        $news = new News();
        $news->setLocale($request->getLocale());

        $form = $this->createForm(NewsType::class, $news);

        $form->add('submit', SubmitType::class, array(
            'label' => "create",
            'attr' => array(
                'class' => 'btn btn-default'
            )
        ));

        $form->handleRequest($request);

        if($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($news);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $this->get('translator')->trans("success.news.add", [], "flash"));

            return $this->redirectToRoute('news_view', array(
                'news' => $news->getId()
            ));
        }

        return $this->render(':news:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/edit/{news}", name="news_edit")
     */
    public function editAction(Request $request, News $news) {
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $this->addFlash('error', $this->get('translator')->trans("error.user.rank", [], "flash"));
            return $this->redirectToRoute('news');
        }

        $form = $this->createForm(NewsType::class, $news);

        $form->add('submit', SubmitType::class, array(
            'label' => "edit",
            'attr' => array(
                'class' => 'btn btn-default'
            )
        ));

        $form->handleRequest($request);

        if($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($news);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $this->get('translator')->trans("success.news.edit", [], "flash"));

            return $this->redirectToRoute('news_view', array(
                'news' => $news->getId()
            ));
        }

        return $this->render(':news:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/delete/{news}", name="news_delete")
     */
    public function deleteAction(News $news) {
        if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $this->addFlash('error', $this->get('translator')->trans("error.user.rank", [], "flash"));
            return $this->redirectToRoute('news');
        }

        $this->getDoctrine()->getManager()->remove($news);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', $this->get('translator')->trans("success.news.delete", [], "flash"));

        return $this->redirectToRoute('news');
    }
}
