<?php

namespace App\Controller;

use App\Entity\Maison;
use App\Form\MaisonType;
use App\Repository\MaisonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MaisonController extends AbstractController
{
    /**
     * @Route("/admin/maison", name="admin_maison")
     */
    public function index(MaisonRepository $maisonRepository): Response
    {

        $maisons = $maisonRepository->findAll();

        return $this->render('admin/maison.html.twig', [
            'maisons' => $maisons,
        ]);
    }

    /**
     * @Route("/admin/maison/create", name="admin_maison_create")
     */
    public function createMaison(Request $request)
    {
        // creation d'une maison à l'aide d'un formulaire
        $maison = new Maison(); // creation d'une nouvelle maison
        $form = $this->createForm(MaisonType::class, $maison); // creation du formulaire avec en parametre la nouvelle maison
        $form->handleRequest($request); // gestionnaire des requetes http

        // traitement du formulaire
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $infoImg1 = $form['img1']->getData(); // recupere les infos de l'image
                $extensionImg1 = $infoImg1->guessExtension(); // recupere l'extension de l'image
                $nomImg1 = time() . '-1.' . $extensionImg1; // cree un nom unique pour l'image
                $infoImg1->move($this->getParameter('dossier_photos_maisons'), $nomImg1); // deplacer l'image dans le dossier adequat
                $maison->setImg1($nomImg1); // definit le nom de l'image a mettre en bdd
                
                $infoImg2 = $form['img2']->getData(); // recupere les infos de l'image
                if ($infoImg2 !== null) {
                    $extensionImg2 = $infoImg2->guessExtension(); // recupere l'extension de l'image
                    $nomImg2 = time() . '-2.' . $extensionImg2; // cree un nom unique pour l'image
                    $infoImg2->move($this->getParameter('dossier_photos_maisons'), $nomImg2); // deplacer l'image dans le dossier adequat
                    $maison->setImg2($nomImg2); // definit le nom de l'image a mettre en bdd
                } else {

                    $maison->setImg2(null); // on definit le nom de l'image a mettre en bdd si pas d'image 2
                }

                //
                $manager = $this->getDoctrine()->getManager(); // recupere le manager de doctrine
                $manager->persist($maison); // dit a doctrine qu'on va vouloir sauvegarder en bdd
                $manager->flush(); // execute la requete
                $this->addFlash('success','La maison a bien été ajoutée');
                
            } else {
                $this->addFlash('danger', 'Une erreur est survenue lors de l\'ajout de la maison');
            }
        }

        return $this->render('admin/maisonForm.html.twig', [
            'maisonForm' => $form->createView(),


        ]);
    }

    /**
     * @Route("/admin/maison/update-{id}", name="admin_maison_update")
     */
    public function updateMaison(MaisonRepository $maisonRepository, $id, Request $request)
    {
        $maison = $maisonRepository->find($id);
        $form = $this->createForm(MaisonType::class, $maison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { // verifie si le formulaire a été soumis

            $infoImg1 = $form['img1']->getData();
            $nomOldImg1 = $maison->getImg1();
            if ($infoImg1 !== null) {
                
                $cheminImg1 = $this->getParameter('dossier_photos_maisons') . '/' . $nomOldImg1;
                
                if (file_exists($cheminImg1)) {
                    unlink($cheminImg1);
                }
                $extensionImg1 = $infoImg1->guessExtension(); // recupere l'extension de l'image
                $nomImg1 = time() . '-1.' . $extensionImg1; // cree un nom unique pour l'image
                $infoImg1->move($this->getParameter('dossier_photos_maisons'), $nomImg1); // deplacer l'image dans le dossier adequat
                $maison->setImg1($nomImg1); // definit le nom de l'image a mettre en bdd
            } else {
                $maison->setImg1($nomOldImg1);
                

            }

            $infoImg2 = $form['img2']->getData();
            $nomOldImg2 = $maison->getImg2();
            if ($infoImg2 !== null) {
                if ($nomOldImg2 !== null) {
                    $cheminOldimg2 = $this->getParameter('dossier_photos_maisons') . '/' . $nomOldImg2;
                   if (file_exists($cheminOldimg2)) {
                       unlink($cheminOldimg2);
                   }
                }
                $extensionImg2 = $infoImg2->guessExtension(); // recupere l'extension de l'image
                $nomImg2 = time() . '-2.' . $extensionImg2; // cree un nom unique pour l'image
                $infoImg2->move($this->getParameter('dossier_photos_maisons'), $nomImg2); // deplacer l'image dans le dossier adequat
                $maison->setImg2($nomImg2); // definit le nom de l'image a mettre en bdd

                
            } else {
                $maison->setImg2($nomOldImg2);
            }

            

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($maison);
            $manager->flush();
            $this->addFlash('success','La maison a bien été modifiée');
            return $this->redirectToRoute('admin_maison');
        
        
        
        
        

        }

        return $this->render('admin/maisonForm.html.twig', [
            'maisonForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/maison/delete-{id}", name="admin_maison_delete")
     */

     public function deleteMaison(MaisonRepository $maisonRepository, $id)
     {
        $maison = $maisonRepository->find($id); // recupere une maison grace a l'id

        $nomImg1 = $maison->getImg1();
        if ($nomImg1 !== null) {
            $cheminImg1 = $this->getParameter('dossier_photos_maisons') . '/' . $nomImg1;
            if (file_exists($cheminImg1)) {
            unlink($cheminImg1);
            }
        }
        
        
        // supression des images
        // suppression de la maison en bdd

        $maison = $maisonRepository->find($id); // recupere une maison grace a l'id
        $nomImg2 = $maison->getImg2();
        if ($nomImg2 !== null) {
            $cheminImg2 = $this->getParameter('dossier_photos_maisons') . '/' . $nomImg2;
            if (file_exists($cheminImg2)) {
            unlink($cheminImg2);
            }
        }
        

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($maison);
        $manager->flush();
        $this->addFlash('success','La maison a bien été supprimée');

        return $this->redirectToRoute('admin_maison');
     }
}
