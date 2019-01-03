<?php

namespace App\Controller;

use App\Entity\Card;
use App\Form\CardType;
use App\Repository\CardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;

/**
 * @Route("/card")
 */
class CardController extends AbstractController
{
    private $uri = 'https://api.scryfall.com/cards/?page=';
    private $cardName = 'name';

    /**
     * @Route("/apirequest/{page}", name="api_request", methods={"GET"},requirements={"page"})
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function apiRequest(int $page): Response
    {
        $client = new Client();

        $nameCard= $client->request('GET', $this->uri .$page);
        $body = $nameCard->getBody();
        $json = json_decode($body->getContents(), true);
        dump($json);


        return $this->render('card//api.html.twig', [
            'cards' => $json['data'],
            'page' =>$page +1,
            ]);
    }

    /**
     * @Route("/", name="card_index", methods={"GET"})
     */
    public function index(CardRepository $cardRepository): Response
    {
        return $this->render('card/index.html.twig', ['cards' => $cardRepository->findAll()]);
    }

    /**
     * @Route("/new", name="card_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $card = new Card();
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($card);
            $entityManager->flush();

            return $this->redirectToRoute('card_index');
        }

        return $this->render('card/new.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="card_show", methods={"GET"})
     */
    public function show(Card $card): Response
    {
        return $this->render('card/show.html.twig', ['card' => $card]);
    }

    /**
     * @Route("/{id}/edit", name="card_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Card $card): Response
    {
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('card_index', ['id' => $card->getId()]);
        }

        return $this->render('card/edit.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="card_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Card $card): Response
    {
        if ($this->isCsrfTokenValid('delete'.$card->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($card);
            $entityManager->flush();
        }

        return $this->redirectToRoute('card_index');
    }
}
