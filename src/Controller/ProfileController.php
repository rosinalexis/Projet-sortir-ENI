<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sorties;
use App\Form\ParticipantType;
use App\Repository\InscriptionsRepository;
use App\Repository\SitesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ParticipantRepository;
use App\Repository\SortiesRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/admin/participant/profil", name="app_profile")
     */
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('profile/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/participant/profil/{id}/show", name="app_profile_show")
     */
    public function showParticipant(Participant $participant): Response
    {

        return $this->render('profile/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/participant/profil/{id}/status", name="app_profile_status")
     */
    public function statusParticipant(Participant $participant, EntityManagerInterface $em): Response
    {
        $etat = !$participant->getActif();
        $participant->setActif($etat);

        $em->flush();

        return $this->redirectToRoute('app_profile');
    }

    /**
     * @Route("/admin/participant/profil/add", name="app_profile_add")
     */
    public function addParticipant(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $participant = new Participant;

        $formParticipant = $this->createForm(ParticiPantType::class, $participant);

        $formParticipant->handleRequest($request);

        if ($formParticipant->isSubmitted() && $formParticipant->isValid()) {
            $participant->setRoles(['ROLE_USER']);
            // encode the plain password
            $participant->setPassword(
                $passwordEncoder->encodePassword(
                    $participant,
                    $participant->getPassword()
                )
            );
            $em->persist($participant);
            $em->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/add.html.twig', [
            'formParticipant' => $formParticipant->createView()
        ]);
    }


    /**
     * @Route("/participant/profil/{id}/eddit", name="app_profile_eddit")
     */
    public function eddit(Participant $participant, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();

        if ((!$user->getRoles() == ['ROLE_ADMIN']) or (!$user->getId() == $participant->getId())) {
            return $this->redirectToRoute('home');
        }

        $formParticipant = $this->createForm(ParticipantType::class, $participant);
        $formParticipant->handleRequest($request);


        if ($formParticipant->isSubmitted() && $formParticipant->isValid()) {

            $em->persist($participant);
            $em->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/eddit.html.twig', [
            'participant' => $participant,
            'formParticipant' => $formParticipant->createView()
        ]);
    }

    /**
     * @Route("/admin/participant/profil/{id}/delete", name="app_profile_delete")
     */
    public function deleteParticipant(Participant $participant, EntityManagerInterface $em, SortiesRepository $sortiesRepository, InscriptionsRepository $inscriptionsRepository): Response
    {

        // lister les sorites 
        $AllSorties  = $sortiesRepository->findBy(['organisateur' => $participant->getId()]);


        if ($AllSorties) {
            // Pour chaque sortie suppression des inscriptions à la sortie 
            foreach ($AllSorties as $sortie) {

                $allInscriptions =  $inscriptionsRepository->findBy(['sorties' => $sortie->getId()]);

                foreach ($allInscriptions as $inscription) {
                    $em->remove($inscription);
                    //envoyer un msg à l'utilisateur pour l'informer
                }
                $em->remove($sortie);
            }
        } else {
            $allInscriptions =  $inscriptionsRepository->findBy(['participants' => $participant->getId()]);

            foreach ($allInscriptions as $inscription) {
                $em->remove($inscription);
                //envoyer un msg à l'utilisateur pour l'informer
            }
        }

        $em->remove($participant);
        $em->flush();

        return $this->redirectToRoute('app_profile');
    }

    /**
     * @Route("/admin/participant/profil/addJson", name="app_profile_add_json")
     */
    public function addJsonParticipant(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, SitesRepository $sitesRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($request->getMethod() == "POST") {
            try {
                $files = $request->files->all();
                foreach ($files as $file) {
                    //vérification de l'extension de mon ficher
                    $nomFichier = $file->getClientOriginalName();

                    //vérification si  .json ou .csv 
                    if (str_contains($nomFichier, '.csv') && $file->getMimetype() == ("text/plain" or "application/vnd.ms-excel")) {

                        //traitement dans le cas ou c'est du csv 
                        $context = [CsvEncoder::DELIMITER_KEY => ','];
                        $csvEncoder = new CsvEncoder($context);

                        $data = $csvEncoder->decode(file_get_contents($file), 'csv', $context);

                        foreach ($data as $tabCsvParticipant) {

                            $participant =  new Participant;
                            $participant->setEmail($tabCsvParticipant['email']);
                            $participant->setRoles([$tabCsvParticipant['roles']]);
                            $participant->setNom($tabCsvParticipant['nom']);
                            $participant->setPrenom($tabCsvParticipant['prenom']);
                            $participant->setPseudo();
                            $participant->setTelephone($tabCsvParticipant['telephone']);
                            $participant->setActif(boolval($tabCsvParticipant['actif']));
                            $participant->setPassword(
                                $passwordEncoder->encodePassword(
                                    $participant,
                                    $tabCsvParticipant['password']
                                )
                            );

                            $siteId = $tabCsvParticipant['sites'];
                            $site = $sitesRepository->findOneBy(['id' => $siteId]);

                            $participant->setSites($site);

                            $em->persist($participant);
                            $em->flush();
                        }
                    } elseif (str_contains($nomFichier, '.json') && $file->getMimetype() == "application/json") {

                        //traitement dans le cas ou c'est du json 
                        $json = file_get_contents($file->getPathname());

                        //conversion en tableau 
                        $tabJson = json_decode($json);

                        foreach ($tabJson as $tabJsonParticipant) {
                            $siteId = $tabJsonParticipant->sites;

                            //traitement des participant
                            $participant = $serializer->deserialize(json_encode($tabJsonParticipant), Participant::class, 'json');
                            $site = $sitesRepository->findOneBy(['id' => $siteId]);
                            $participant->setSites($site);
                            $participant->setPassword(
                                $passwordEncoder->encodePassword(
                                    $participant,
                                    $tabJsonParticipant->password
                                )
                            );

                            $em->persist($participant);
                            $em->flush();
                        }
                    }
                }
                // je signale l'utilisateur 
                $this->addFlash('success', 'converssion ok');
                return $this->render('profile/test.html.twig');
            } catch (NotEncodableValueException $th) {

                $this->addFlash('error', 'impossible de convertir le ficher');
                return $this->render('profile/test.html.twig');
            }
        }

        return $this->render('profile/test.html.twig');
    }
}
