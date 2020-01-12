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
     * @Route("/messages", name="user_message_index", methods={"GET"})
     */
    public function index(UserMessageRepository $userMessageRepository): Response
    {
        return $this->render('user_message/index.html.twig', [
            'user_messages' => $userMessageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/messages/new", name="user_message_new", methods={"GET","POST"})
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

            $this->addFlash('success', 'msg.oknew');
            return $this->redirectToRoute('user_message_index');
        }

        return $this->render('user_message/new.html.twig', [
            'user_message' => $userMessage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/messages/{id<\d+>}/show", name="user_message_show", methods={"GET"})
     */
    public function show(UserMessage $userMessage, UserMessageRepository $repo): Response
    {
        return $this->render('user_message/show.html.twig', [
            'user_message' => $userMessage,
        ]);
    }

    /**
     * @Route("/messages/{id<\d+>}/edit", name="user_message_edit", methods={"GET","POST"})
     * @IsGranted("edit", subject="userMessage", message="UserMessage can only be edited by their authors.")
     */
    public function edit(Request $request, UserMessage $userMessage): Response
    {
        $form = $this->createForm(UserMessageType::class, $userMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_message_index');
            $this->addFlash('success', 'msg.okedit');
        }

        return $this->render('user_message/edit.html.twig', [
            'user_message' => $userMessage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/messages/{id<\d+>}/delete", name="user_message_delete", methods={"DELETE"})
     * @IsGranted("delete", subject="userMessage")
     */
    public function delete(Request $request, UserMessage $userMessage): Response
    {
        if ($this->isCsrfTokenValid('delete' . $userMessage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($userMessage);
            $entityManager->flush();
        }

        $this->addFlash('success', 'msg.okdelete');
        return $this->redirectToRoute('user_message_index');
    }

    /**
     * @route("/contact", name="blog_contact")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function contact(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $msg = new UserMessage();
        $msg->setUser($this->getUser());
        $form = $this->createForm(UserMessageType::class, $msg);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($msg);
            $em->flush();

            return $this->redirectToRoute('blog_index');
        }

        return $this->render('default/contact.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}
