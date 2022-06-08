<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_list")
     * @Method({"GET"})
    */
    public function index(ManagerRegistry $doctrine)
    {
        $articles = $doctrine->getRepository(Article::class)->findAll();

        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/article/new", name="new_article")
     * @Method({"GET", "POST"})
     */
    public function new(ManagerRegistry $doctrine, Request $request)
    {
        $article = new Article();

        $form = $this->createFormBuilder($article)
                    ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
                    ->add('body', TextareaType::class, array(
                        'attr' => array('class' => 'form-control'),
                        'required' => false
                    ))
                    ->add('save', SubmitType::class, array(
                        'label' => 'Create',
                        'attr' => array('class' => 'btn btn-primary mt-3')
                    ))
                    ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/create.html.twig', array('form' => $form->createView()));

    }

    /**
     * @Route("/article/edit/{id}", name="article_edit")
     * @Method({"GET", "POST"})
    */
    public function edit(ManagerRegistry $doctrine, Request $request, $id)
    {
        $article = new Article();
        $article = $doctrine->getRepository(Article::class)->find($id);

        $form = $this->createFormBuilder($article)
                        ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
                        ->add('body', TextareaType::class, array(
                            'attr' => array('class' => 'form-control'),
                            'required' => false
                        ))
                        ->add('save', SubmitType::class, array(
                            'label' => 'Update',
                            'attr' => array('class' => 'btn btn-primary mt-3')
                        ))
                        ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/edit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/article/{id}", name="article_show")
     * @Method({"GET"})
    */
    public function show(ManagerRegistry $doctrine, $id)
    {
        $article = $doctrine->getRepository(Article::class)->find($id);
        return $this->render('articles/show.html.twig', [
            'article' => $article,
        ]);
        
    }

    /**
     * @Route("/article/delete/{id}", name="article_delete")
     * @Method({"DELETE"})
    */
    public function delete(ManagerRegistry $doctrine, $id)
    {
        $article = $doctrine->getRepository(Article::class)->find($id);

        $entityManager = $doctrine->getManager();
        $entityManager->remove($article);
        $entityManager->flush();

        $response = new Response();
        $response->send();

    }

    // /**
    //  * @Route("/article/save")
    //  */
    // public function save(ManagerRegistry $doctrine) {
    //     $entityManager = $doctrine->getManager();

    //     $article = new Article();
    //     $article->setTitle('Article One');
    //     $article->setBody('Lorem ipsum for article one');

    //     $entityManager->persist($article);

    //     $entityManager->flush();

    //     return new Response('Saved an article witht the id of ' . $article->getId());
    // }
}