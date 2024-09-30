<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;

use App\Entity\Question;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;

use App\Entity\Reponse;
use App\Form\QuizzType;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Doctrine\Persistence\ManagerRegistry;




class QuizzController extends AbstractController
{
    #[Route('/quizz', name: 'app_quizz', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('quizz/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/quizz/{id}', name: 'app_quizz_make', methods: ['GET', 'POST'])]
    public function make( Request $request, Categorie $categorie, QuestionRepository $questionRepository,): Response
    {

        $questions = $questionRepository->findBy(['categorie' => $categorie]);

        $form = $this->createForm(QuizzType::class);
        $form->handleRequest($request);
        $total = count($questions);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $score = 0;

            foreach ($data as $answer) {

                if ($answer !== "") {
                    $score++;
                }
            }

            return $this->render('quizz/result.html.twig', [
                'score' => $score,
                'total' => $total,
                'categorie' => $categorie,
                'questions' => $questions
            ]);
        }

        return $this->render('quizz/make.html.twig', [
            'categorie' => $categorie,
            'questions' => $questions,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/quizz/result', name: 'app_quizz_result', methods: ['GET','POST'])]
    public function result(Request $request, Categorie $categorie, QuestionRepository $questionRepository): Response
    {
        $score = $request->query->get('score', 0);
        $total = $request->query->get('total', 0);
        $questions = $questionRepository->findBy(['categorie' => 'id']);


        return $this->render('quizz/result.html.twig', [
            'score' => $score,
            'total' => $total,
          
        ]);
    }

}