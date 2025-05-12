<?php
// src/Controller/Admin/CommentAdminController.php
namespace App\Controller\Admin;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/comments')]
class CommentAdminController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/{id}/delete', name: 'admin_comment_delete', methods: ['POST'])]
    public function delete(Comment $comment): RedirectResponse
    {
        $place = $comment->getPlace();
        $this->em->remove($comment);
        $this->em->flush();

        $this->addFlash('success', 'Commentaire supprimé.');

        // Retour à la page de la place dont il provient
        return $this->redirectToRoute('place_show', [
            'id' => $place?->getId()
        ]);
    }
}
