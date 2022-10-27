<?php

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/movies/{name}', name: 'movies', defaults: ['name' => null], methods: ['GET', 'HEAD'])]
    public function index(): Response
    {
        $repository = $this->entityManager->getRepository(Movie::class);
        $movies = $repository->findOneBy(['id' => 6, 'title' => 'The Dark Knight'], ['id' => 'DESC']);


        dd($movies);
        return $this->render('movies/index.html.twig', [
            'movies' => $movies,
        ]);
    }
}
