<?php
namespace App\Controller\Admin;

use App\Entity\Place;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PlaceTypeForm;

#[Route('/admin/places')]
class PlaceAdminController extends AbstractController
{
    public function __construct(
        private PlaceRepository $repo,
        private EntityManagerInterface $em
    ) {
    }

    #[Route('', name: 'admin_place_list')]
    public function list(): Response
    {
        $places = $this->repo->findAll();
        return $this->render('admin/places.html.twig', ['places' => $places]);
    }

    #[Route('/{id}/approve', name: 'admin_place_approve', methods: ['POST'])]
    public function approve(Place $place): RedirectResponse
    {
        $place->setIsActive(true);
        $this->em->flush();
        $this->addFlash('success', 'Lieu approuvé.');
        return $this->redirectToRoute('admin_place_list');
    }

    #[Route('/{id}/reject', name: 'admin_place_reject', methods: ['POST'])]
    public function reject(Place $place): RedirectResponse
    {
        $this->em->remove($place);
        $this->em->flush();
        $this->addFlash('success', 'Lieu refusé et supprimé.');
        return $this->redirectToRoute('admin_place_list');
    }

    #[Route('/{id}/delete', name: 'admin_place_delete', methods: ['POST'])]
    public function delete(Place $place): RedirectResponse
    {
        $this->em->remove($place);
        $this->em->flush();
        $this->addFlash('success', 'Lieu supprimé.');
        return $this->redirectToRoute('admin_place_list');
    }

    #[Route('/{id}/edit', name: 'admin_place_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Place $place): Response
    {
        $form = $this->createForm(PlaceTypeForm::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Nom du lieu mis à jour.');
            return $this->redirectToRoute('place_show', ['id' => $place->getId()]);
        }

        return $this->render('admin/place_edit.html.twig', [
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }
}
