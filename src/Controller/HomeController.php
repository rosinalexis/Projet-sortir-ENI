<?php

namespace App\Controller;

use App\Repository\SitesRepository;
use App\Repository\SortiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SortiesRepository $sortie, SitesRepository $sites, Request $req): Response
    {
        $lstSortie = $sortie->filtersHomeSorties($req,$this->getUser());
        $lstSites = $sites->findAll();
        
        $memory = [];

        if($req->get('filter_site') != null){
            $memory['site'] = $req->get('filter_site');
        }else if($req->get('filter_site') == null){
            $memory['site'] = 'default';
        }

        if($req->get('filter_d1') != null){
            $memory['d1'] = $req->get('filter_d1');
        }
        
        if($req->get('filter_d2') != null){
            $memory['d2'] = $req->get('filter_d2');
        }

        if($req->get('ch_s_1') != null){
            $memory['ch_1'] = true;
        }

        if($req->get('ch_s_2') != null){
            $memory['ch_2'] = true;
        }

        if($req->get('ch_s_3') != null){
            $memory['ch_3'] = true;
        }

        if($req->get('ch_s_4') != null){
            $memory['ch_4'] = true;
        }


      //  dd($memory);
        return $this->render('home/index.html.twig', [
            'lstSortie' => $lstSortie,
            'lstSites' => $lstSites,
            'memory' => $memory,
        ]);
    }
}
