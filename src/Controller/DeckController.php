<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\DeckCard;
use App\Form\DeckType;
use App\Repository\DeckCardRepository;
use App\Repository\DeckRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client;

/**
 * @Route("/deck")
 */
class DeckController extends AbstractController
{
    private $uri = 'https://api.scryfall.com/cards/search?q=';
    private $url = '&unique=card&include_multilingual=true&format=image&sas=grid&order=name';
    private $urlPage = '&page=';
    private $urc = 'https://api.scryfall.com/cards/';

    /**
     * @Route("/", name="deck", methods={"GET","POST"})
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function new(Request $request, DeckRepository $deckRepository, int $next = 1): Response
    {
        $deck = new Deck();
        $form = $this->createForm(DeckType::class, $deck);
        $form->handleRequest($request);

        $client = new Client([
            RequestOptions::HTTP_ERRORS => false,
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $deck->addUser($this->getUser());
            $entityManager->persist($deck);
            $entityManager->flush();

            return $this->redirectToRoute('deck');
        }
        if ($_GET) {
            $search = $_GET['search'];

            $nameCard = $client->request('GET', $this->uri . $search . $this->url . $this->urlPage . $next);
            $statusCode = $nameCard->getStatusCode();
            if ($statusCode > 400) {
                $this->addFlash('danger', "Aucune carte ne correspond à votre recherche");
                return $this->redirectToRoute('deck');
            }
            if (!empty(trim($search))) {
                return $this->redirectToRoute('searchpage', ['search' => $_GET['search'], 'next' => $next,]);
            }
        }
        return $this->render('deck/index.html.twig', [
            'deck_card' => $deck,
            'form' => $form->createView(),
            'decks' => $deckRepository->findDecksForUser($this->getUser())
        ]);
    }

    /**
     * @Route("/{id}", name="deck_show", methods={"GET"})
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function show(Deck $deck, int $next = 1): Response
    {
        $client = new Client([
            RequestOptions::HTTP_ERRORS => false,
        ]);
        $cards = $deck->getDeckCards();
        if (count($cards) !== 0) {
            foreach ($cards as $card) {
                $nameCard = $client->request('GET', $this->urc . $card->getIdCard());
                $statusCode = $nameCard->getStatusCode();

                $body = $nameCard->getBody();
                $json[] = json_decode($body->getContents(), true);
            }
        } else {
            $this->addFlash('warning', "Votre deck est vide");
            return $this->redirectToRoute('deck');
        }
        if ($_GET) {
            $search = $_GET['search'];

            $nameCard = $client->request('GET', $this->uri . $search . $this->url . $this->urlPage . $next);
            $statusCode = $nameCard->getStatusCode();
            if ($statusCode > 400) {
                $this->addFlash('danger', "Aucune carte ne correspond à votre recherche");
                return $this->redirectToRoute('deck');
            }
            if (!empty(trim($search))) {
                return $this->redirectToRoute('searchpage', ['search' => $_GET['search'], 'next' => $next,]);
            }
        }

        return $this->render('deck/show.html.twig', [
            'deck' => $deck,
            'cards' => $json,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="deck_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Deck $deck): Response
    {
        $form = $this->createForm(DeckType::class, $deck);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('deck_index', ['id' => $deck->getId()]);
        }

        return $this->render('deck/edit.html.twig', [
            'deck' => $deck,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="deck_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Deck $deck): Response
    {
        if ($this->isCsrfTokenValid('delete' . $deck->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($deck);
            $entityManager->flush();
        }

        return $this->redirectToRoute('deck_index');
    }

    /**
     * @Route("/{idCard}/{idDeck}/add/{search}/{next}", name="add_to_deck", methods="GET")
     */
    public function addToDeck(
        string $idCard,
        string $search,
        int $next,
        int $idDeck,
        DeckRepository $deckRepository
    ): Response {

        $deckCard = new DeckCard();
        $deckCard->setIdCard($idCard);
        $deckCard->addDeck($deckRepository->findOneBy(['id' => $idDeck]));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($deckCard);
        $entityManager->flush();

        return $this->redirectToRoute('searchpage', ['search' => $search, 'next' => $next,]);
    }
}
