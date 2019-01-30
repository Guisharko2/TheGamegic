<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\User;
use App\Repository\CardRepository;
use App\Repository\DeckRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/card")
 */
class CardController extends AbstractController
{
    private $uri = 'https://api.scryfall.com/cards/search?q=';
    private $url = '&unique=card&include_multilingual=true&format=image&sas=grid&order=name';
    private $urlPage = '&page=';
    private $urc = 'https://api.scryfall.com/cards/';

    /**
     * @Route("/", name="card_index", methods={"GET"})
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index(
        CardRepository $cardRepository,
        PaginatorInterface $paginator,
        Request $request,
        DeckRepository $deckRepository,
        int $next = 1
    ): Response {
        $client = new Client([
            RequestOptions::HTTP_ERRORS => false,
        ]);
        $cards = $cardRepository->findCardsByUser($this->getUser());
        if (count($cards) !== 0) {
            foreach ($cards as $card) {
                $nameCard = $client->request('GET', $this->urc . $card->getCardId());
                $statusCode = $nameCard->getStatusCode();

                $body = $nameCard->getBody();
                $json[] = json_decode($body->getContents(), true);
            }
        } else {
            $this->addFlash('warning', "Votre bibliothèque est vide");
            return $this->redirectToRoute('homepage');
        }
        if (isset($_GET['search'])) {
            $search = $_GET['search'];

            $nameCard = $client->request('GET', $this->uri . $search . $this->url . $this->urlPage . $next);
            $statusCode = $nameCard->getStatusCode();
            if ($statusCode > 400) {
                $this->addFlash('danger', "Aucune carte ne correspond à votre recherche");
                return $this->redirectToRoute('card_index');
            }
            if (!empty(trim($search))) {
                return $this->redirectToRoute('searchpage', ['search' => $_GET['search'], 'next' => $next,]);
            }
        }

        $cardsPages = $json;
        $cardsPages = $paginator->paginate(
            $cardsPages,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            20
        );
        return $this->render('card/index.html.twig', [
            'cards' => $json,
            'cardsPages' => $cardsPages,
            'decks' => $deckRepository->findDecksForUser($this->getUser()),

        ]);
    }


    /**
     * @Route("/{id}", name="card_show", methods={"GET"})
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function show(string $id, int $next = 1): Response
    {
        $card = new Card();
        $card->setCardId($id);
        $client = new Client([
            RequestOptions::HTTP_ERRORS => false,
        ]);

        $nameCard = $client->request('GET', $this->urc . $card->getCardId());
        $statusCode = $nameCard->getStatusCode();
        if ($statusCode > 400) {
            $this->addFlash('danger', "Aucun résultat ne correspond");

            return $this->redirectToRoute('homepage');
        }
        if ($_GET) {
            $search = $_GET['search'];

            $nameCard = $client->request('GET', $this->uri . $search . $this->url . $this->urlPage . $next);
            $statusCode = $nameCard->getStatusCode();
            if ($statusCode > 400) {
                $this->addFlash('danger', "Aucune carte ne correspond à votre recherche");
                return $this->redirectToRoute('card_index');
            }
            if (!empty(trim($search))) {
                return $this->redirectToRoute('searchpage', ['search' => $_GET['search'], 'next' => $next,]);
            }
        }
        $body = $nameCard->getBody();
        $json = json_decode($body->getContents(), true);
        $manas = str_split($json['mana_cost'], 3);
        return $this->render('card/show.html.twig', ['card' => $json, 'manas' => $manas]);
    }


    /**
     * @Route("/{id}", name="card_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Card $card): Response
    {
        if ($this->isCsrfTokenValid('delete' . $card->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($card);
            $entityManager->flush();
        }

        return $this->redirectToRoute('card_index');
    }

    /**
     * @Route("/{idCard}/add/{search}/{next}", name="add_card", methods="GET")
     */
    public function addCard(string $idCard, string $search, int $next, CardRepository $cardRepository): Response
    {
        if (!$cardRepository->findBy(['cardId' => $idCard])) {
            $card = new Card();
            $card->addUser($this->getUser());
            $card->setCardId($idCard);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($card);
            $entityManager->flush();
        } elseif ($cardRepository->findBy(['cardId' => $idCard])) {
            $card = $cardRepository->findOneBy(['cardId' => $idCard]);
            $card->addUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($card);
            $entityManager->flush();
        }


        return $this->redirectToRoute('searchpage', ['search' => $search, 'next' => $next,]);
    }
}
