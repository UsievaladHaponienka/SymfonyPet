<?php

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Movie::class);
    }

    #[Route('/movies/', name: 'movies', methods: ['GET', 'HEAD'])]
    public function index(): Response
    {
        $movies = $this->repository->findAll();
        return $this->render('movies/index.html.twig', compact('movies'));
    }

    #[Route('movies/{id}', name: 'movies.show', methods: ['GET', 'HEAD'])]
    public function show($id)
    {
        $movie = $this->repository->find($id);

        return $this->render('movies/show.html.twig', compact('movie'));

    }
}
