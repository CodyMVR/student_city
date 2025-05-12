<?php
// src/Controller/PlaceController.php
namespace App\Controller;

use App\Entity\Place;
use App\Entity\Comment;
use App\Form\PlaceTypeForm;
use App\Form\CommentTypeForm;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaceController extends AbstractController
{
    #[Route('/place/new', name: 'place_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $place = new Place();
        $form  = $this->createForm(PlaceTypeForm::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $place->setAuthor($this->getUser());
            $em->persist($place);
            $em->flush();

            $this->addFlash('success', 'Lieu enregistré, en attente d’approbation.');
            return $this->redirectToRoute('place_list');
        }

        return $this->render('place/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/place', name: 'place_list')]
    public function list(Request $request, PlaceRepository $repo): Response
    {
        $q = trim((string)$request->query->get('q', ''));
        if ($q !== '') {
            $places = $repo->findActiveByName($q);
        } else {
            $places = $repo->findBy(['isActive' => true], ['name' => 'ASC']);
        }
    
        return $this->render('place/list.html.twig', [
            'places' => $places,
            'q'      => $q,
        ]);
    }

    #[Route('/place/{id}', name: 'place_show')]
    public function show(
        Place $place,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        // ne montre que les lieux approuvés aux non-admins
        if (!$place->isActive()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $comment = new Comment();
        $cForm   = $this->createForm(CommentTypeForm::class, $comment);
        $cForm->handleRequest($request);

        if ($cForm->isSubmitted() && $cForm->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setPlace($place);
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('place_show', ['id' => $place->getId()]);
        }

        return $this->render('place/show.html.twig', [
            'place'      => $place,
            'commentForm'=> $cForm->createView(),
        ]);
    }
}
