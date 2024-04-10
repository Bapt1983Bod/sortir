<?php

namespace App\Controller;

use App\Entity\Group;

use App\Form\GroupFormType;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;

class PrivateGroupController extends AbstractController
{
    #[Route('/group', name: 'app_group')]
    public function index(GroupRepository $groupRepository): Response
    {

        $user=$this->getUser();
        $groups = $groupRepository->findAll();
        return $this->render('private_group/index.html.twig', [
            'groups' => $groups,
            'user'=>$user,
        ]);
    }

#[Route('/group/create', name: 'app_group_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, Security $security): Response
{
    $group = new Group();
    $form = $this->createForm(GroupFormType::class, $group);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
//        faire en sorte que le créateur soit automatiquement inscrit dans le groupe
        $user=$security->getUser();
        $group->addMembre($user);

        $entityManager->persist($group);
        $entityManager->flush();

        // Redirigez l'utilisateur vers une autre page, par exemple la page d'accueil
        return $this->redirectToRoute('app_group');
    }

    return $this->render('private_group/create.html.twig', [
        'form' => $form->createView(),
    ]);
}#[Route('/group/delete/{id}', name: 'app_group_delete')]
    public function delete(Group $group, EntityManagerInterface $em): Response
    {
        $em->remove($group);
        $em->flush();
        return $this->redirectToRoute('app_group');
    }
}