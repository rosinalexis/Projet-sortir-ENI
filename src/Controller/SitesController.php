<?php

namespace App\Controller;

use App\Entity\Sites;
use App\Entity\Sorties;
use App\Form\SitesType;
use App\Repository\LieuxRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SitesRepository;
use App\Repository\SortiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitesController extends AbstractController
{
    /**
     * @Route("/sites", name="sites")
     */
    public function sitesSelect(SitesRepository $sites,Request $req): Response
    {
        $lstSites = $sites->findAll();

        return $this->render('sites/site.html.twig', [
            'lstSites' => $lstSites,
        ]);
    }

    /**
     * @Route("/admin/sites/add", name="site_add")
     */
    public function sitesAdd(EntityManagerInterface $em, Request $req): Response
    {
        $site = new Sites();
        $site->setNomSite($req->get('search_sites'));

        $em->persist($site);
        $em->flush();

        return $this->redirectToRoute("sites");
    }

    /**
     * @Route("/admin/sites/update/{id}", name="site_update")
     */
    public function sitesUpdate(Sites $site, EntityManagerInterface $em, Request $req): Response
    {
        $form = $this->createForm(SitesType::class,$site);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            return $this->redirectToRoute("sites");
        }

        return $this->render('sites/sitesmodif.html.twig',[
            'forms' => $form->createView()
        ]);
    
    }

    /**
     * @Route("/admin/sites/del/{id}", name="site_delete")
     */
    public function sitesDelete(Sites $site, EntityManagerInterface $em): Response
    {
        foreach($site->getSorties() as $s){
            $s->setSite(null);
        }

        foreach($site->getParticipants() as $p){
            $p->setSites(null);
        }
        $em->remove($site);
        $em->flush();

        return $this->redirectToRoute('sites');
    }

    /**
     * @Route("/sites/search", name="sites_search")
     */
    public function sitesSearch(SitesRepository $sites,Request $req): Response
    {

        if($req->get('filter_sites') == null){
            return $this->redirectToRoute("sites");    
        }else{
            $lstSites = $sites->filterSearchSitesByName($req->get('filter_sites'));

            return $this->render('sites/site.html.twig', [
                'lstSites' => $lstSites,
            ]);
        }
    }

    /**
     * @Route("/site/{id}/sorties", name="sorties_site")
     */
    public function sorties_site(Sites $s, SortiesRepository $sr, ParticipantRepository $pr): Response
    {
        $organisateurs = $pr->findBy(array("sites" => $s->getId()));
        $sorties = $sr->findBy(array("organisateur" => $organisateurs));
        
        return $this->render('sites/sorties.html.twig', [
            "site" => $s,
            "sorties" => $sorties
        ]);
    }
}
