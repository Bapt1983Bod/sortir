<?php


namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Site;
use App\Form\ImportCsvType;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ImportCsvController extends AbstractController
{
    #[Route('/import/csv', name: 'app_import_csv')]
    public function importCsv(Request $request, EntityManagerInterface $entityManager, SiteRepository $siteRepo): Response
    {
        $form = $this->createForm(ImportCsvType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $csvFile = $form->get('csv_file')->getData();

            try {
                $file = fopen($csvFile->getPathname(), 'r'); //lire le contenu ligne par ligne

                fgetcsv($file); // Ignorer les premières lignes


                while (($line = fgetcsv($file)) !== FALSE) {
                    foreach($line as $import) {
                        $data = explode(";", $import);
                        //dd($data);
                        $participant = new Participant();
                        $participant->setNom($data[0]);
                        $participant->setPrenom($data[1]);
                        $participant->setTelephone($data[2]);
                        $participant->setPassword($data[3]);
                        $participant->setAdministrateur($data[4]);
                        $participant->setActif($data[5]);

                        $site = $siteRepo->find((int)$data[6]);
                        $participant->setSite($site);


                        $participant->setEmail($data[7]);
                        $tableau = [];
                        $tableau[] = $data[8];
                        $participant->setRoles($tableau);

                        //dd($participant);

                        $entityManager->persist($participant);
                        $entityManager->flush();

                    }
                    //$data = explode(";", $line);
                    //dd($line);
                    /*$participant = new Participant();
                    $participant->setNom($line[0]);
                    $participant->setPrenom($line[1]);
                    $participant->setTelephone($line[2]);
                    $participant->setPassword($line[3]);
                    $participant->setAdministrateur($line[4]);
                    $participant->setActif($line[5]);
                    $participant->setSite($line[6]);
                    $participant->setEmail($line[7]);
                    $participant->setRoles(json_decode($line[8], true));


                    $entityManager->persist($participant);*/
                }

                fclose($file);
                $entityManager->flush();

                $this->addFlash('success', 'Importation CSV réussie!');
                return $this->redirectToRoute('app_profil');
            } catch (\Exception $exception) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'importation: ' . $exception->getMessage());
            }
        }

        // Passez le formulaire au modèle Twig lors du rendu
        return $this->render('import_csv/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
