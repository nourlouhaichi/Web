<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AuthorType;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    //****************************CRUD**********************************

    ///READ
    #[Route('/listAuthor', name:'authors_list')]
    public function listAuthor(AuthorRepository $reprository)
    {
        $authors1=$reprository->findAll();
        $authors2=$reprository->showAllAuthorsOrderByEmail();
        return $this->render("author/listAuthors.html.twig",array("tabAuthors"=>$authors1,"tabAuthors2"=>$authors2));
    }

    //CREATE
    #[Route('/addAuthor', name:'authors_add')]
    public function addAuthor(ManagerRegistry $ManagerRegistry)
    {
        $author = new Author();
        $author->setUsername("Nour");
        $author->setEmail("Nour@gmail.com");
        #$em = $this->getDoctrine()->getManager();
        $em= $ManagerRegistry->getManager();
        $em->persist($author);
        $em->flush();
        return $this->redirectToRoute("authors_list");   
    }

    //UPDATE
    #[Route('/updateAuthor/{id}', name: 'author_update')]
    public function updateAuthor(AuthorRepository $repository,$id,ManagerRegistry $managerRegistry)
    {
        $author = $repository->find($id);
        $author->setUsername("Louhaichi");
        $author->setEmail("Louhaichi@gmail.com");
        #$em = $this->getDoctrine()->getManager();
        $em= $managerRegistry->getManager();
        $em->flush();
        return $this->redirectToRoute("authors_list");
    }

    //DELETE
    #[Route('/deleteAuthor/{id}', name: 'author_delete')]
    public function deleteAuthor($id,AuthorRepository $repository,ManagerRegistry $managerRegistry)
    {
        $author= $repository->find($id);
        $em= $managerRegistry->getManager();
        $em->remove($author);
        $em->flush();
        return $this->redirectToRoute("authors_list");
    }  

    //****************************CRUD FORM**********************************

    //CREATE
    #[Route('/addForm', name: 'add_form')]
    public function addForm(Request $request,ManagerRegistry $managerRegistry)
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class,$author);
        $form ->handleRequest($request);
        if($form->isSubmitted())
        {
            $em= $managerRegistry->getManager();
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('authors_list');
        }
        //        1methode
        /*return $this->render("author/add.html.twig"
        ,array("formulaireAuthor"=>$form->createView()));*/
        return $this->renderForm("author/FormAddAuthor.html.twig",array("formulaireAuthor"=>$form));
    }

    //UPDATE
    #[Route('/updateForm/{id}', name: 'update_form')]
    public function updateForm($id,AuthorRepository $repository,Request $request,ManagerRegistry $manager)
    {
        $author= $repository->find($id);
        $form=$this->createForm(AuthorType::class,$author);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $em=$manager->getManager();
            //$em->persist($author);
            $em->flush();

            return $this->redirectToRoute('authors_list');
        }
        return $this->renderForm("author/FormAddAuthor.html.twig",array("formulaireAuthor"=>$form));
    }

    //****************************Bonus**********************************

    // #[Route('/showauthor/{name}', name: 'show_author')]
    // public function showAuthor($name)
    // {
    //     return $this->render("author/show.html.twig",array('nameAuthor'=>$name));
    // }

    //AFFICHAGE TABLEAU SANS BD STATIQUE
    #[Route('/list', name: 'list')]
    public function list()
    {
        $authors = array(
            array('id' => 1, 'username' => ' Victor Hugo','email'=> 'victor.hugo@gmail.com', 'nb_books'=> 100),
            array ('id' => 2, 'username' => 'William Shakespeare','email'=>
                'william.shakespeare@gmail.com','nb_books' => 200),
            array('id' => 3, 'username' => ' Taha Hussein','email'=> 'taha.hussein@gmail.com','nb_books' => 300),
        );
        return $this->render("author/list.html.twig",array("tabAuthors"=>$authors));
    }

    //FONCTION QUI VA PRENDRE LE ID ET L ENVOYER VERS UNE PAGE TWIG
    #[Route('/auhtorDetails/{id}', name:'detail_author')]
    public function auhtorDetails($id)
    {
        return $this->render("author/showAuthor.html.twig",array("id"=>$id));
    }
}
