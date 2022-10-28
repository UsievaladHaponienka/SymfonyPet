<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Movie::class);
    }

    #[Route('/movies/', name: 'movies.index', methods: ['GET', 'HEAD'])]
    public function index(): Response
    {
        $movies = $this->repository->findAll();
        return $this->render('movies/index.html.twig', compact('movies'));
    }

    #[Route('movies/create', name: 'movies.create')]
    public function create(Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);

        //TODO: Debug this method
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //TODO: Debug this method
            $newMovie = $form->getData();

            /** @var UploadedFile $imagePath */
            $imagePath = $form->get('imagePath')->getData();
            if ($imagePath) {
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $newMovie->setImagePath('/uploads/' . $newFileName);
            }

            $this->repository->save($newMovie, true);

            return $this->redirectToRoute('movies.index');
        }

        return $this->render('movies/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('movies/edit/{id}', name: 'movie.edit')]
    public function edit($id, Request $request): Response
    {
        $movie = $this->repository->find($id);
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);

        /** @var UploadedFile $imagePath */
        $imagePath = $form->get('imagePath')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($imagePath) {
                if ($movie->getImagePath() !== null &&
                    file_exists(
                        $this->getParameter('kernel.project_dir') . '/public/' . $movie->getImagePath()
                    )) {
                    $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                    try {
                        $imagePath->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFileName
                        );
                    } catch (FileException $e) {
                        return new Response($e->getMessage());
                    }

                    $movie->setImagePath('/uploads/' . $newFileName);
                }
            }

            $movie->setTitle($form->get('title')->getData());
            $movie->setReleaseYear($form->get('releaseYear')->getData());
            $movie->setDescription($form->get('description')->getData());
            $this->repository->save($movie, true);

            return $this->redirectToRoute('movies.index');
        }

        return $this->render('movies/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView()
        ]);
    }

    #[Route('movies/{id}', name: 'movies.show', methods: ['GET', 'HEAD'])]
    public function show($id): Response
    {
        $movie = $this->repository->find($id);

        return $this->render('movies/show.html.twig', compact('movie'));
    }


}
