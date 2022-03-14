<?php

namespace App\Controller;

use App\Entity\Lieux;
use App\Entity\Villes;
use App\Form\LieuFormType;
use App\Repository\LieuxRepository;
use App\Repository\SortiesRepository;
use App\Repository\VillesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LieuController extends AbstractController
{

    /**
     * @Route("/lieux_ville/{ville}", name="lieux_ville", methods={"GET"})
     */
    public function lieux_ville(Villes $ville, VillesRepository $vr, LieuxRepository $lr, NormalizerInterface $ni): Response
    {

        $ville = $vr->findOneBy(["id" => $ville->getId()]);
        $lieux = $lr->findBy(["villes" => $ville]);
        $normalize = $ni->normalize($lieux, null, ["groups" => "lieu:read"]);
        return $this->json($normalize);
    }

    /**
     * @Route("/admin/quickAddLieu", name="quickAddLieu")
     */
    public function quickAddLieu(Request $req, EntityManagerInterface $em, VillesRepository $vr): Response
    {
        $ville = $vr->findOneBy(array("id" => $req->get("ville")));

        $lieu = new Lieux();
        $lieu->setVilles($ville);
        $lieu->setNomLieu($req->get("nom_lieu"));
        $lieu->setRue($req->get("rue"));

        $em->persist($lieu);
        $em->flush();
        return $this->redirectToRoute("addSortie");
    }

    /**
     * @Route("/showLieux", name="showLieux")
     */
    public function showLieux(LieuxRepository $lr): Response
    {
        return $this->render("lieux/showLieux.html.twig", [
            "lieux" => $lr->findAll()
        ]);
    }

    /**
     * @Route("/showLieu/{l}", name="showLieu")
     */
    public function showLieu(Lieux $l): Response
    {
        return $this->render("lieux/showLieu.html.twig", [
            "lieu" => $l
        ]);
    }

    /**
     * @Route("/admin/addLieu", name="addLieu")
     */
    public function addLieu(Request $req, EntityManagerInterface $em, VillesRepository $vr): Response
    {
        $lieu = new Lieux();
        $form = $this->createForm(LieuFormType::class, $lieu);
        $form->handleRequest($req);

        if ($form->isSubmitted()) {
            $lieu->setVilles($vr->findOneBy(["id" => intval($req->get("ville"))]));
            $lieu->setNomLieu($req->get("nom_lieu"));
            $lieu->setRue($req->get("rue"));
            if ($req->get("latitude") != null) $lieu->setLatitude(floatval($req->get("latitude")));
            if ($req->get("longitude") != null) $lieu->setLongitude(floatval($req->get("longitude")));

            $em->persist($lieu);
            $em->flush();
            return $this->redirectToRoute("showLieux");
        } else {
            return $this->render("lieux/addLieu.html.twig", [
                "form" => $form->createView(),
                "villes" => $vr->findAll()
            ]);
        }
    }

    /**
     * @Route("/admin/editLieu/{l}", name="editLieu")
     */
    public function editLieu(Lieux $l, Request $req, VillesRepository $vr, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LieuFormType::class, $l);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $l->setNomLieu($req->get("nom_lieu"));
            $l->setRue($req->get("rue"));
            $l->setLatitude($req->get("latitude"));
            $l->setLongitude($req->get("longitude"));
            if ($req->get("ville") != null) $l->setVilles($vr->findOneBy(["nom_ville" => $req->get("ville")]));

            $em->persist($l);
            $em->flush();

            return $this->redirectToRoute("showLieux");
        } else {
            return $this->render("lieux/editLieu.html.twig", [
                "lieu" => $l,
                "form" => $form->createView(),
                "villes" => $vr->findAll()
            ]);
        }
    }

    /**
     * @Route("/admin/delLieu/{l}", name="delLieu")
     */
    public function delLieu(Lieux $l, EntityManagerInterface $em, VillesRepository $villesRepository, SortiesRepository $sortiesRepository): Response
    {
        $listVilles  = $villesRepository->findBy(['id' => $l->getVilles()->getId()]);
        $listSorties = $sortiesRepository->findBy(['lieux' => $l->getId()]);

        foreach ($listSorties as $sortie) {
            $sortie->setLieux(NULL);
        }

        foreach ($listVilles as $ville) {
            $ville->removeLieux($l);
        }
        $em->remove($l);
        $em->flush();

        return $this->redirectToRoute("showLieux");
    }

    /**
     * @Route("/showLieux/search", name="lieux_search")
     */
    public function lieuxSearch(LieuxRepository $lr, Request $req): Response
    {
        if ($req->get('filter_lieux') == null) {
            return $this->redirectToRoute("showLieux");
        } else {
            $lstLieux = $lr->filterSearchLieuxByName($req->get('filter_lieux'));

            return $this->render('lieux/showLieux.html.twig', [
                'lieux' => $lstLieux
            ]);
        }
    }
}
