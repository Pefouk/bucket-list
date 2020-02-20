<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Liste;
use App\Form\ListeType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @return Response
     */
    public function list_idea(Request $request)
    {
        $ListRepo = $this->getDoctrine()->getRepository(Liste::class);
        $categories = $this->getDoctrine()->getRepository(Categories::class)->findAll();
        if ($request->request->get('categorie') == null || $request->request->get('categorie') == -1) {
            $liste = $ListRepo->findBy(['isPublished' => 'TRUE'], ['dateCreated' => 'DESC'], 30);
            $select = -1;
        } else {
            $select = $request->request->get('categorie');
            $liste = $ListRepo->findBy(['isPublished' => 'TRUE', 'categorie' => $select]);
        }
        return $this->render('idea/list.html.twig', ['liste' => $liste, 'categories' => $categories, 'select' => $select]);
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
            $this->addFlash('success', 'L\'idée a bien été ajoutée à la liste !');
            return $this->redirectToRoute('idea_detail', ['id' => $res->getId()]);
        }
        if ($this->getUser() == null || (strcmp($this->getUser()->getUsername(), $res->getAuthor()) !== 0)) {
            $this->addFlash('error', 'You can\'t edit someone\'s else idea !');
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
            $this->addFlash('success', 'L\'idée a bien été ajoutée à la liste !');
            return $this->redirectToRoute('idea_detail', ['id' => $liste->getId()]);
        } elseif ($formListe->isSubmitted() && !$formListe->isValid())
            $this->addFlash('error', 'L\'idée n\'as pas été ajouté à la liste car un ou plusieurs champs sont incorrect');

        return $this->render('idea/add.html.twig', [
            "listeForm" => $formListe->createView()
        ]);
    }
}