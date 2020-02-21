<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Liste;
use App\Form\DeleteIdeaType;
use App\Form\ListeType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/idea", name="idea")
 */
class IdeaController extends AbstractController
{
    /**
     * @Route("/", name="_list")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function list_idea(Request $request, PaginatorInterface $paginator)
    {
        $ListRepo = $this->getDoctrine()->getRepository(Liste::class);
        $categories = $this->getDoctrine()->getRepository(Categories::class)->findAll();
        if ($request->request->get('categorie') == null || $request->request->get('categorie') == -1) {
            $liste = $ListRepo->findBy(['isPublished' => 'TRUE'], ['dateCreated' => 'DESC']);
            $select = -1;
        } else {
            $select = $request->request->get('categorie');
            $liste = $ListRepo->findBy(['isPublished' => 'TRUE', 'categorie' => $select], ['dateCreated' => 'DESC']);
        }
        $res = $paginator->paginate(
            $liste,
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('idea/list.html.twig', ['liste' => $res, 'categories' => $categories, 'select' => $select]);
    }

    /**
     * @Route("/delete/{id}", name="_delete")
     * @param $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     */
    public function delete_idea($id, Request $request, EntityManagerInterface $em)
    {
        $ListRepo = $this->getDoctrine()->getRepository(Liste::class);
        $res = $ListRepo->find(["id" => $id]);
        $formDelete = $this->createForm(DeleteIdeaType::class, $res);
        $formDelete->handleRequest($request);
        if ($formDelete->isSubmitted() && $formDelete->isValid()) {
            $em->remove($res);
            $em->flush();
            $this->addFlash('success', 'Idea suppressed successfully !');
            return $this->redirectToRoute('home');
        }
        if ($this->getUser() == null || (strcmp($this->getUser()->getUsername(), $res->getAuthor()) !== 0)) {
            $this->addFlash('danger', 'You can\'t delete someone\'s else idea !');
            return $this->redirectToRoute('idea_list');
        }
        return $this->render('idea/delete.html.twig', ["listeForm" => $formDelete->createView()]);
    }

    /**
     * @Route("/edit/{id}", name="_edit")
     */
    public function edit_idea($id, Request $request, EntityManagerInterface $em)
    {
        $ListRepo = $this->getDoctrine()->getRepository(Liste::class);
        $res = $ListRepo->find(["id" => $id]);
        $formListe = $this->createForm(ListeType::class, $res);
        $formListe->handleRequest($request);
        if ($formListe->isSubmitted() && $formListe->isValid()) {
            $em->persist($res);
            $em->flush();
            $this->addFlash('success', 'The idea was successfully edited !');
            return $this->redirectToRoute('idea_detail', ['id' => $res->getId()]);
        }
        if ($this->getUser() == null || (strcmp($this->getUser()->getUsername(), $res->getAuthor()) !== 0)) {
            $this->addFlash('danger', 'You can\'t edit someone\'s else idea !');
            return $this->redirectToRoute('idea_list');
        } else {
            return $this->render('idea/edit.html.twig', ["listeForm" => $formListe->createView()]);
        }
    }

    /**
     * @Route("/detail/{id}", name="_detail")
     */
    public function detail_idea($id)
    {
        $ListRepo = $this->getDoctrine()->getRepository(Liste::class);
        $res = $ListRepo->find(["id" => $id]);

        if ($this->getUser() != null && (strcmp($this->getUser()->getUsername(), $res->getAuthor()) === 0))
            $editable = true;
        else
            $editable = false;
        dump($editable);
        return $this->render("idea/detail.html.twig", ["res" => $res, "editable" => $editable]);
    }

    /**
     * @Route("/add", name="_add")
     */
    public
    function add_idea(EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        $liste = new Liste();
        $formListe = $this->createForm(ListeType::class, $liste);
        $liste->setIsPublished(true);
        $liste->setDateCreated(new DateTime());

        $formListe->handleRequest($request);
        if ($formListe->isSubmitted() && $formListe->isValid()) {
            $em->persist($liste);
            $em->flush();
            $this->addFlash('success', 'The idea was modified successfully !');
            return $this->redirectToRoute('idea_detail', ['id' => $liste->getId()]);
        } elseif ($formListe->isSubmitted() && !$formListe->isValid())
            $this->addFlash('danger', 'The idea wasn\'t modified because on or multiple champs are incorrect !');

        return $this->render('idea/add.html.twig', [
            "listeForm" => $formListe->createView()
        ]);
    }
}