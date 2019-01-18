<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\User;
use App\Repository\CardRepository;
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

    private $uri = 'https://api.scryfall.com/cards/';

    /**
     * @Route("/", name="card_index", methods={"GET"})
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index(CardRepository $cardRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $client = new Client([
            RequestOptions::HTTP_ERRORS => false,
        ]);
        $cards = $cardRepository->findCardsByUser($this->getUser());
        foreach ($cards as $card) {
            $nameCard = $client->request('GET', $this->uri . $card->getCardId());
            $statusCode = $nameCard->getStatusCode();

            $body = $nameCard->getBody();
            $json[] = json_decode($body->getContents(), true);
        }
        $cardsPages = $json;

        return $this->render('card/index.html.twig', [
            'cards' => $json,
        ]);
    }


    /**
     * @Route("/{id}", name="card_show", methods={"GET"})
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function show(string $id): Response
    {
        $card = new Card();
        $card->setCardId($id);
        $client = new Client([
            RequestOptions::HTTP_ERRORS => false,
        ]);

        $nameCard = $client->request('GET', $this->uri . $card->getCardId());
        $statusCode = $nameCard->getStatusCode();
        if ($statusCode > 400) {
            $this->addFlash('danger', "Aucun résultat ne correspond");

            return $this->redirectToRoute('homepage');
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
