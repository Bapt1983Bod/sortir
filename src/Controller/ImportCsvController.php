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
use App\Services\HashPassword;


class ImportCsvController extends AbstractController
{
    private $hashPasswordService;
    public function __construct(HashPassword $hashPasswordService)
    {
        $this->hashPasswordService = $hashPasswordService;
    }

    #[Route('/import/csv', name: 'app_import_csv')]
    public function importCsv(Request $request, EntityManagerInterface $entityManager, SiteRepository $siteRepo): Response
    {
        $form = $this->createForm(ImportCsvType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $csvFile = $form->get('csv_file')->getData();

            try {
                $file = fopen($csvFile->getPathname(), 'r'); //lire le contenu ligne par ligne
                fgetcsv($file); // Ignorer la première ligne

                while (($line = fgetcsv($file)) !== FALSE) {
                    foreach($line as $import) {
                        $data = explode(";", $import);

                        $participant = new Participant();
                        $participant->setNom($data[0]);
                        $participant->setPrenom($data[1]);
                        $participant->setTelephone($data[2]);
                        $password = $data[3];
                        // Service pour hacher le mot de passe
                        $hashedPassword = $this->hashPasswordService->hashPassword($password);
                        // Enregistrez l'utilisateur avec le mot de passe haché
                        $participant->setPassword($hashedPassword);
                        $participant->setAdministrateur($data[4]);
                        $participant->setActif($data[5]);
                        $site = $siteRepo->find((int)$data[6]);
                        $participant->setSite($site);
                        $participant->setEmail($data[7]);
                        $tableau = [];
                        $tableau[] = $data[8];
                        $participant->setRoles($tableau);

                        $entityManager->persist($participant);
                        $entityManager->flush();
                    }
                }

                fclose($file);
                $entityManager->flush();

                $this->addFlash('success', 'Importation CSV réussie!');
                return $this->redirectToRoute('app_admin_utilisateurs');
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
