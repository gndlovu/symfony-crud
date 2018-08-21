<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Post;
// use Doctrine\DBAL\Types\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PostController extends Controller
{
    /**
     * @Route("/", name="view_posts")
     */
    public function viewPostsAction()
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findAll();
        
        return $this->render("pages/post/list.html.twig", compact('posts'));
    }

    /**
     * @Route("/post/create",  name="create_post")
     */
    public function createPostAction(Request $request)
    {
        $post = new Post;
        $form = $this->createFormBuilder($post)
                ->add('title', TextType::Class, ['attr' => ['class' => 'form-control']])
                ->add('description', TextareaType::Class, ['attr' => ['class' => 'form-control']])
                ->add('category', TextType::Class, ['attr' => ['class' => 'form-control']])
                ->add('save', SubmitType::Class, ['label' => 'Create Post', 'attr' => ['class' => 'btn btn-primary mt-5']])
                ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $title = $form['title']->getData();
            $description = $form['description']->getData();
            $category = $form['category']->getData();

            $post->setTitle($title);
            $post->setDescription($description);
            $post->setCategory($category);

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Post successfully added!');

            return $this->redirectToRoute('view_posts');
        }

        return $this->render("pages/post/create.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("/post/update/{id}",  name="update_post")
     */
    public function updatePostAction(Request $request, $id)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);
        $post->setTitle($post->getTitle());
        $post->setDescription($post->getDescription());
        $post->setCategory($post->getCategory());

        $form = $this->createFormBuilder($post)
                ->add('title', TextType::Class, ['attr' => ['class' => 'form-control']])
                ->add('description', TextareaType::Class, ['attr' => ['class' => 'form-control']])
                ->add('category', TextType::Class, ['attr' => ['class' => 'form-control']])
                ->add('save', SubmitType::Class, ['label' => 'Update Post', 'attr' => ['class' => 'btn btn-primary mt-5']])
                ->getForm();

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $title = $form['title']->getData();
            $description = $form['description']->getData();
            $category = $form['category']->getData();

            $em = $this->getDoctrine()->getManager();
            $post = $em->getRepository('AppBundle:Post')->find($id);
            $post->setTitle($title);
            $post->setDescription($description);
            $post->setCategory($category);
            $em->flush();

            $this->addFlash('success', 'Post successfully updated!');

            return $this->redirectToRoute('view_posts');
        }

        return $this->render("pages/post/update.html.twig",  ['form' => $form->createView()]);
    }

    /**
     * @Route("/post/show/{id}",  name="show_post")
     */
    public function showPostAction($id)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);

        return $this->render("pages/post/show.html.twig", compact('post'));
    }

    /**
     * @Route("/post/delete/{id}",  name="delete_post")
     */
    public function deletePostAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AppBundle:Post')->find($id);
        $em->remove($post);
        $em->flush();
        
        $this->addFlash('success', 'Post successfully deleted!');

        return $this->redirectToRoute('view_posts');
    }
}
