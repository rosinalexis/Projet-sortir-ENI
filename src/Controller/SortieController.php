<?php

namespace App\Controller;

use App\Entity\Inscriptions;
use App\Entity\Participant;
use App\Entity\Sorties;
use App\Form\EditSortieType;
use App\Form\SortiesType;
use App\Repository\EtatsRepository;
use App\Repository\InscriptionsRepository;
use App\Repository\LieuxRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SitesRepository;
use App\Repository\SortiesRepository;
use App\Repository\VillesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/addSortie", name="addSortie")
     */
    public function addSortie(Request $req, VillesRepository $vr, LieuxRepository $lr, EtatsRepository $er, SitesRepository $sr, EntityManagerInterface $em): Response
    {
        $sortie = new Sorties();
        $villes = $vr->findAll();

        $form = $this->createForm(SortiesType::class,$sortie);
        $form->handleRequest($req);

        if ($form->isSubmitted()) {
            $sortie->setNom($req->get("nom"));
            $sortie->setDateDebut(new \DateTime($req->get("date_debut")));
            $sortie->setDateCloture(new \DateTime($req->get("date_cloture")));
            $sortie->setNbInscriptionsMax(intval($req->get("nbInscriptionsMax")));
            $sortie->setDuree(intval($req->get("duree")));
            $sortie->setDescription($req->get("description"));
            $sortie->setOrganisateur($this->getUser());
            $sortie->setSite($sr->findOneBy(array("id" => $this->getUser()->getSites()->getId())));
            if ($req->get("lieu") != null) $sortie->setLieux($lr->findOneBy(array("id" => str_replace("lieu","",$req->get("lieu")))));
            if ($req->get("ville") != null) $sortie->getLieux()->setVilles($vr->findOneBy(array("id" => str_replace("ville","",$req->get("ville")))));

            if ($req->get("etat") == "enregistrer") $sortie->setEtats($er->findOneBy(array("libelle" => "Créée")));
            else $sortie->setEtats($er->findOneBy(array("libelle" => "Ouverte")));

            $em->persist($sortie);
            $em->flush();
            return $this->redirectToRoute("home");
        } else {
            return $this->render('sortie/addSortie.html.twig',[
                "formSortie" => $form->createView(),
                "villes" => $villes
            ]);
        }
    }

    /**
     * @Route("/showSortie/{id}", name="showSortie")
     */
    public function showSortie(Sorties $s, InscriptionsRepository $ir, ParticipantRepository $pr): Response
    {
        $inscriptions = $ir->findBy(array("sorties" => $s->getId()));

        return $this->render('sortie/showSortie.html.twig',[
            "sortie" => $s,
            "participants" => $inscriptions
        ]);
    }

    /**
     * @Route("/editSortie/{id}", name="editSortie")
     */
    public function editSortie(Sorties $s, Request $req, VillesRepository $vr, EtatsRepository $er, LieuxRepository $lr, SitesRepository $sr, EntityManagerInterface $em): Response
    {
        $villes = $vr->findAll();

        $form = $this->createForm(EditSortieType::class,$s);
        $form->handleRequest($req);

        if ($form->isSubmitted()) {
            $s->setDateDebut(new \DateTime($req->get("date_debut")));
            $s->setDateCloture(new \DateTime($req->get("date_cloture")));
            $s->setNbInscriptionsMax(intval($req->get("nbInscriptionsMax")));
            $s->setDuree(intval($req->get("duree")));
            $s->setOrganisateur($this->getUser());
            $s->setSite($sr->findOneBy(array("id" => $this->getUser()->getSites()->getId())));
            
            if ($req->get("lieu") != null) $s->setLieux($lr->findOneBy(array("id" => str_replace("lieu","",$req->get("lieu")))));
            if ($req->get("ville") != null) $s->getLieux()->setVilles($vr->findOneBy(array("id" => str_replace("ville","",$req->get("ville")))));
            $s->setEtats($er->findOneBy(["libelle"=>$req->get("etat")]));
            if ($req->get("etat") == "enregistrer") $s->setEtats($er->findOneBy(array("libelle" => $req->get("etat_initial"))));
            else $s->setEtats($er->findOneBy(array("libelle" => "Ouverte")));

            $em->persist($s);
            $em->flush();
            return $this->redirectToRoute("home");
        } else {
            return $this->render('sortie/editSortie.html.twig',[
                "form" => $form->createView(),
                "villes" => $villes,
                "sortie" => $s
            ]);
        }
    }

    /**
     * @Route("/delSortie/{id}", name="delSortie")
     */
    public function delSortie(Sorties $s, EntityManagerInterface $em, InscriptionsRepository $ir): Response
    {
        $inscriptions = $ir->findBy(["sorties"=>$s->getId()]);
        $em->remove($s);

        foreach ($inscriptions as $inscription) {
            $em->remove($inscription);
        }
        
        $em->flush();
        return $this->redirectToRoute("home");
    }

    /**
     * @Route("/annuleSortie/{id}", name="annuleSortie")
     */
    public function annuleSortie(Sorties $s, Request $req, EntityManagerInterface $em, EtatsRepository $er): Response
    {
        $etat_annule = $er->findOneBy(array("libelle" => "Annulé"));

        $motif = "";
        if ($req->get("motif") != null) $motif = $req->get("motif");

        if ($motif == "") {
            return $this->render('sortie/annuleSortie.html.twig', [
                "sortie" => $s
            ]);
        } else {
            $s->setDescription("[ANNULEE motif : ".$motif."] ".$s->getDescription());
            $s->setEtats($etat_annule);
            $em->persist($s);
            $em->flush();
            return $this->redirectToRoute("home");
        }
    }

    /**
     * @Route("/desistSortie/{sortie}/{user}", name="desistSortie")
     */
    public function desistSortie(Sorties $sortie, Participant $user, EntityManagerInterface $em, InscriptionsRepository $ir): Response
    {
        $inscription = $ir->findOneBy(["sorties" => $sortie->getId(),"participants" => $user->getId()]);
        $em->remove($inscription);
        $em->flush();
        return $this->redirectToRoute("home");
    }

    /**
     * @Route("/inscription/{sortie}/{user}", name="inscriptionSortie")
     */
    public function inscriptionSortie(Sorties $sortie, Participant $user, EntityManagerInterface $em, InscriptionsRepository $ir): Response
    {
        $test = $ir->findBy(["sorties"=> $sortie->getId(), "participants"=> $user->getId()]);
        if ($test == null) {
            $inscription = new Inscriptions();
            $inscription->setDateInscription(new \DateTime(date("Y-m-d H:i:s")));
            $inscription->setSorties($sortie);
            $inscription->setParticipants($user);

            $em->persist($inscription);
            $em->flush();
        }

        return $this->redirectToRoute("home");
    }
}
