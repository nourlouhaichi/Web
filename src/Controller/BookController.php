<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;
use App\Entity\Book;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BookType;


class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

                    /******************TEST*******************/
    ///READ
    #[Route('/listBook', name:'books_list')]
    public function listBook(BookRepository $reprository)
    {
        $books=$reprository->findAll();
        return $this->render("book/listBooks.html.twig",array("tabBooks"=>$books));
    }

    //CREATE
    #[Route('/addBook', name:'books_add')]
    public function addBook(ManagerRegistry $ManagerRegistry)
    {
        $date=new DateTime('2023-10-14');

        $book = new Book();
        $book->setRef("004");
        $book->setTitle("Too Late");
        $book->setPublished(true);
        $book->setPublicationDate($date);
        #$em = $this->getDoctrine()->getManager();
        $em= $ManagerRegistry->getManager();
        $em->persist($book);
        $em->flush();
        return $this->redirectToRoute("books_list");   
    }

    ///READ findBooksByAuthor
    #[Route('/listBook/{id}', name:'books_Author')]
    public function listBookAuthor($id,BookRepository $reprository)
    {
        $books=$reprository->findBooksByAuthor($id);
        return $this->render("book/listBooks.html.twig",array("tabBooks"=>$books));
    }

                /***************************Form**************************/
    //CREATE
    #[Route('/addFormBook', name: 'add_form_book')]
    public function addForm(Request $request,ManagerRegistry $managerRegistry)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class,$book);
        $form ->handleRequest($request);
        $book->setPublished(true);
        if($form->isSubmitted())
        {
            $em= $managerRegistry->getManager();
            $nbBooks=$book->getAuthor()->getNbrBooks();
            $book->getAuthor()->setNbrBooks($nbBooks+1);
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('books_list');
        }
        return $this->renderForm("book/FormAddBook.html.twig",array("formulaireBook"=>$form));
    }

    //DELETE
    #[Route('/deleteBook/{id}', name: 'book_delete')]
    public function deleteBook($id,BookRepository $repository,ManagerRegistry $managerRegistry)
    {
        $book= $repository->find($id);
        $em= $managerRegistry->getManager();
        $em->remove($book);
        $em->flush();
        return $this->redirectToRoute("books_list");
    }  

    //UPDATE
    #[Route('/updateFormBook/{id}', name: 'update_form_book')]
    public function updateForm($id,BookRepository $repository,Request $request,ManagerRegistry $manager)
    {
        $book= $repository->find($id);
        $form=$this->createForm(BookType::class,$book);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $em=$manager->getManager();
            //$em->persist($book);
            $em->flush();

            return $this->redirectToRoute('books_list');
        }
        return $this->renderForm("book/FormAddBook.html.twig",array("formulaireBook"=>$form));
    }

}
