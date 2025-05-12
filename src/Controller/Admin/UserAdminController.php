<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users')]
class UserAdminController extends AbstractController
{
    public function __construct(private UserRepository $repo, private EntityManagerInterface $em) {}

    #[Route('', name: 'admin_user_list')]
    public function list(): Response
    {
        $users = $this->repo->findAll();
        return $this->render('admin/index.html.twig', ['users' => $users]);
    }

    #[Route('/{id}/approve', name: 'admin_user_approve', methods: ['POST'])]
    public function approve(User $user): RedirectResponse
    {
        $user->setIsActive(true);
        $this->em->flush();
        $this->addFlash('success', 'Compte activé.');
        return $this->redirectToRoute('admin_user_list');
    }

    #[Route('/{id}/disapprove', name: 'admin_user_disapprove', methods: ['POST'])]
    public function disapprove(User $user): RedirectResponse
    {
        $this->em->remove($user);
        $this->em->flush();
        $this->addFlash('success', 'Compte refusé et supprimé.');
        return $this->redirectToRoute('admin_user_list');
    }

    #[Route('/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(User $user): RedirectResponse
    {
        $this->em->remove($user);
        $this->em->flush();
        $this->addFlash('success', 'Compte supprimé.');
        return $this->redirectToRoute('admin_user_list');
    }
}
