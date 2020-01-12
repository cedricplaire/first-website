<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\PostLike;
use App\Form\CommentType;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use App\Events\CommentCreatedEvent;
use App\Repository\PostLikeRepository;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * Controller used to manage blog contents in the public part of the site.
 *
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="blog_index")
     * @Route("/rss.xml", defaults={"page": "1", "_format"="xml"}, methods={"GET"}, name="blog_rss")
     * @Route("/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="blog_index_paginated")
     * @Cache(smaxage="10")
     */
    public function index(Request $request, int $page, string $_format, PostRepository $posts, TagRepository $tags): Response
    {
        $tag = null;
        if ($request->query->has('tag')) {
            $tag = $tags->findOneBy(['name' => $request->query->get('tag')]);
        }
        $latestPosts = $posts->findLatest($page, $tag);

        return $this->render('blog/index.' . $_format . '.twig', [
            'paginator' => $latestPosts,
        ]);
    }

    /**
     * @Route("/posts/{slug}", methods={"GET"}, name="blog_post")
     */
    public function postShow(Post $post): Response
    {
        return $this->render('blog/post_show.html.twig', ['post' => $post]);
    }

    /**
     * @Route("/comment/{postSlug}/new", methods={"POST"}, name="comment_new")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @ParamConverter("post", options={"mapping": {"postSlug": "slug"}})
     */
    public function commentNew(Request $request, Post $post, EventDispatcherInterface $eventDispatcher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $post->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $eventDispatcher->dispatch(new CommentCreatedEvent($comment));
            $this->addFlash('success', 'comment.oknew');
            return $this->redirectToRoute('blog_post', ['slug' => $post->getSlug()]);
        }

        return $this->render('blog/comment_form_error.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * The "id" of the Post is passed in and then turned into a Post object
     * automatically by the ParamConverter.
     */
    public function commentForm(Post $post): Response
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('blog/_comment_form.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search", methods={"GET"}, name="blog_search")
     */
    public function search(Request $request, PostRepository $posts): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->render('blog/search.html.twig');
        }

        $query = $request->query->get('q', '');
        $limit = $request->query->get('l', 10);
        $foundPosts = $posts->findBySearchQuery($query, $limit);

        $results = [];
        foreach ($foundPosts as $post) {
            $results[] = [
                'title' => htmlspecialchars($post->getTitle(), ENT_COMPAT | ENT_HTML5),
                'date' => $post->getPublishedAt()->format('M d, Y'),
                'author' => htmlspecialchars($post->getAuthor()->getFullName(), ENT_COMPAT | ENT_HTML5),
                'summary' => htmlspecialchars($post->getSummary(), ENT_COMPAT | ENT_HTML5),
                'url' => $this->generateUrl('blog_post', ['slug' => $post->getSlug()]),
            ];
        }

        return $this->json($results);
    }

    /**
     * permet de liker ou unliker un article
     * 
     * @Route("/post/{id}/like", methods={"POST"}, name="post_like")
     *
     * @param Post $post
     * @param ObjectManager $manager
     * @param PostLikeRepository $likeRepo
     * @return Response
     */
    public function like(Post $post, PostLikeRepository $likeRepo): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if (!$user) return $this->json([
            'code' => 403,
            'message' => 'unauthorized'
        ], 403);

        if ($post->isLikedByUser($user)) {
            $like = $likeRepo->findOneBy([
                'post' => $post,
                'user' => $user
            ]);

            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'J\'aime bien supprimé',
                'likes' => $likeRepo->count(['post' => $post])
            ], 200);
        }

        $like = new PostLike();
        $like->setPost($post)
            ->setUser($user)
            ->setCreatedAt(new DateTime('now'));

        $manager->persist($like);
        $manager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'J\'aime bien ajouté',
            'likes' => $likeRepo->count(['post' => $post])
        ], 200);
    }
}
