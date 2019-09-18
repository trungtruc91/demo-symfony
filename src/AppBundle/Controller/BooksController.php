<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BooksController extends Controller
{
    /**
     * @Route("/books/author")
     */
    public function authorAction()
    {
        return $this->render('books/author.html.twig');
//        return new Response('Book store application!');
    }

    /**
     * @Route("/books/display", name="app_book_display")
     * */
    public function displayAction()
    {
        $bk = $this->getDoctrine()
            ->getRepository('AppBundle:Book')
            ->findAll();
        return $this->render('books/display.html.twig', ['data' => $bk]);
    }

    /**
     * @Route("/books/new",name="app_book_new")
     */
    public function newAction(Request $request)
    {
        $stud = new Book();
        $form = $this->createFormBuilder($stud)
            ->add('name', TextType::class)
            ->add('author', TextType::class)
            ->add('price', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Submit'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();

            $doct = $this->getDoctrine()->getManager();
            $doct->persist($book);
            $doct->flush();
            return $this->redirectToRoute('app_book_display');
        }

        return $this->render('books/new.html.twig', array('form' => $form->createView(),));
    }

    /**
     * @Route("/books/update/{id}", name = "app_book_update" )
     */
    public function updateAction($id, Request $request)
    {
        $doct = $this->getDoctrine()->getManager();
        $bk = $doct->getRepository('AppBundle:Book')->find($id);

        if (!$bk) {
            throw $this->createNotFoundException(
                'No book found for id ' . $id
            );
        }
        $form = $this->createFormBuilder($bk)
            ->add('name', TextType::class)
            ->add('author', TextType::class)
            ->add('price', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Submit'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();
            $doct = $this->getDoctrine()->getManager();

            // tells Doctrine you want to save the Product
            $doct->persist($book);

            //executes the queries (i.e. the INSERT query)
            $doct->flush();
            return $this->redirectToRoute('app_book_display');
        } else {
            return $this->render('books/new.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }

    /**
     * @Route("/books/delete/{id}", name="app_book_delete")
     */
    public function deleteAction($id)
    {
        $doct = $this->getDoctrine()->getManager();
        $bk = $doct->getRepository('AppBundle:Book')->find($id);

        if (!$bk) {
            throw $this->createNotFoundException('No book found for id ' . $id);
        }
        $doct->remove($bk);
        $doct->flush();
        return $this->redirectToRoute('app_book_display');
    }
}