<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use mikehaertl\pdftk\Pdf;

class HomeController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * HomeController constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/")
     */
    public function index(Request $request): Response
    {
        $users = $this->manager->getRepository(User::class)->findAll();

        if($request->files->has("file")){
            $baseDir = $this->getParameter('kernel.project_dir');

            foreach ($request->get("data") as $userEmail => $data){

                if(!is_dir($baseDir . "/var/files/")){
                    mkdir($baseDir . "/var/files/");
                }

                $pdf = new Pdf($request->files->get('file')->getPathname());
                $result = $pdf->cat($data['start'], $data['end']);

                $result->saveAs($baseDir . "/var/files/" . $userEmail .'.pdf');
            }
        }

        return $this->render('home/index.html.twig', [
            'users' => $users,
        ]);
    }
}
