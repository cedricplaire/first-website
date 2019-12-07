<?php

namespace App\Controller;

use App\Entity\UserMessage;
use App\Form\UserMessageType;
use App\Repository\UserMessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @Route("/blog/messages")
 * 
 */
class UserMessageController extends AbstractController
{
    /**
     * @Route("/", name="user_message_index", methods={"GET"})
     */
    public function index(UserMessageRepository $userMessageRepository): Response
    {
        return $this->render('user_message/index.html.twig', [
            'user_messages' => $userMessageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_message_new", methods={"GET","POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function new(Request $request): Response
    {
        $userMessage = new UserMessage();
        $userMessage->setUser($this->getUser());
        $form = $this->createForm(UserMessageType::class, $userMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userMessage);
            $entityManager->flush();

            $this->addFlash('success', 'Votre message a bien été enregistré !');
            return $this->redirectToRoute('user_message_index');
        }

        return $this->render('user_message/new.html.twig', [
            'user_message' => $userMessage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="user_message_show", methods={"GET"})
     */
    public function show(UserMessage $userMessage, UserMessageRepository $repo): Response
    {
        return $this->render('user_message/show.html.twig', [
            'user_message' => $userMessage,
        ]);   
    }

    /**
     * @Route("/{id<\d+>}/edit", name="user_message_edit", methods={"GET","POST"})
     * @IsGranted("edit", subject="UserMessage", message="UserMessage can only be edited by their authors.")
     */
    public function edit(Request $request, UserMessage $userMessage): Response
    {
        $form = $this->createForm(UserMessageType::class, $userMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_message_index');
            $this->addFlash('success', 'Votre message a bien été modifié !');
        }

        return $this->render('user_message/edit.html.twig', [
            'user_message' => $userMessage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id<\d+>}/delete", name="user_message_delete", methods={"DELETE"})
     * @IsGranted("delete", subject="post")
     */
    public function delete(Request $request, UserMessage $userMessage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userMessage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($userMessage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_message_index');
    }
}
