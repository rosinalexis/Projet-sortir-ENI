<?php

namespace App\Controller;

use App\Repository\ArchiveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArchiveController extends AbstractController
{
    /**
     * @Route("/admin/showArchives", name="showArchives")
     */
    public function index(ArchiveRepository $ar): Response
    {
        return $this->render('archive/index.html.twig', [
            "archives" => $ar->findAll()
        ]);
    }
}
