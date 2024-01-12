<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }



    #[Route('/category/update/{id}', name: 'category_update')]
    #[Route('/category/create', name: 'category_create')]
    public function category_create(Request $request, EntityManagerInterface $manager, CategoryRepository $repository, $id=null): Response
    {
        // cette méthode a pour but de créer une nouvelle catégorie.
        // préalablement à cette méthode nous avons configurés les contraintes dans l'entité et configuré les champs de formulaire dans le type (dans le dossier form de src)
        // il nous faudra donc un formulaire, une condition de soumission de formulaire et enfin une méthode pour insérer en table de BDD.

        if (!$id):
        // créer une catégorie pour la create
        $category = new Category();
        else:
        // récupérer une catégorie pour la update
        $category=$repository->find($id);
       endif;
        // création du formulaire
        // La méthode createForm() génère un objet de formulaire, elle attend plusieurs arguments:
        //1er: Le formulaire que l'on souhaite créé (automatiquement un des types existant dans le dossier Form)
        //2eme: l'instance de classe à remplir par ce formulaire
        //3eme (optionnel): Des options de formulaire à transmettre au type
        // cette méthode permet à symfony de vérifier la concordance des chams de formulaire (les add() dans le type) avec les propriétés existantes dans l'entité et de même de charger la vérification des erreurs à la soumission.
        $form = $this->createForm(CategoryType::class, $category);
        //A présent il nous faut renvoyer la vue de ce formulaire dans la méthode render()

        //récupération des données du formulaire (remplissage de l'objet)
        $form->handleRequest($request);

        // condition de soumission de formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // il nous reste à envoyer en BDD
            // Pour cela nous avons besoins de l'EntityManagerInterface de doctrine

            // on prépare la requete
            $manager->persist($category);

            // on execute la requete préparée
            $manager->flush();

            // on redirige sur la page de gestion
            return $this->redirectToRoute('category_list');

        }


        return $this->render('home/category_create.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/category/list', name: 'category_list')]
    public function category_list(CategoryRepository $repository): Response
    {
        // méthode ayant pour but d'afficher un tableau recapitulatif des catégories afin de pouvoir les modifier ou les supprimer
        // il nous faut donc récupérer toutes les catégories enregistrées en BDD

        // pour toutes requêtes de selection (récupération de données en BDD)
        // il faudra injecter en dé^pendance le Repository concerné

        $categories=$repository->findAll();
        dump($categories);
        //on renvoi à la vue le jeu de résultat



        return $this->render('home/category_list.html.twig', [
           'categories'=>$categories
        ]);
    }


}
