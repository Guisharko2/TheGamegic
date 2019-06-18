<?php
/**
 * Created by PhpStorm.
 * User: wilder4
 * Date: 04/01/19
 * Time: 13:01
 */

namespace App\Controller;

use App\Form\BasicSearchType;
use App\Repository\GameCardRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Card;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage", methods={"GET|POST"})
     */
    public function index(Request $request, $search = ''): Response
    {
        $form = $this->createForm(BasicSearchType::class);
        $form->handleRequest($request);
        if ($_POST) {
            $search = $_POST['basic_search']['name'];
            if (!empty(trim($search))) {
                return $this->redirectToRoute('searchpage', ['search' => $search]);
            }
        }

        return $this->render('homepage/index.html.twig',
            [
                'form' => $form->createView(),
            ]);
    }

    /**
     * @Route("/searchpage/{search}", name="searchpage", methods={"GET|POST"})
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function basicResearch(
        string $search,
        Request $request,
        GameCardRepository $gameCardRepository,
        PaginatorInterface $paginator
    ): Response
    {

        $form = $this->createForm(BasicSearchType::class);
        $form->handleRequest($request);
        if ($_POST) {
            if ($_POST['basic_search']['name']) {
                $search = $_POST['basic_search']['name'];
                return $this->redirectToRoute('searchpage', ['search' => $search]);
            }

        }
        $cards = $gameCardRepository->basicSearch($search);
        $cardsPages = $paginator->paginate(
            $cards,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            20
        );
        $decks = '';
        if ($this->getUser() !== null) {
            $decks = $this->getUser()->getDecks();
        }
        $cardsPages->setPageRange(9);
        return $this->render('homepage/search.html.twig', [
            'cards' => $cards,
            'search' => $search,
            'cardsPages' => $cardsPages,
            'decks' => $decks,
            'form' => $form->createView(),
        ]);
    }
}
