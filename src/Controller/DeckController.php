<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\DeckCard;
use App\Form\BasicSearchType;
use App\Form\DeckType;
use App\Repository\DeckCardRepository;
use App\Repository\GameCardRepository;
use App\Repository\DeckGameCardRepository;
use App\Repository\DeckRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/deck")
 */
class DeckController extends AbstractController
{

    /**
     * @Route("/", name="deck_index", methods={"GET","POST"})
     */
    public function new(Request $request, DeckRepository $deckRepository): Response
    {

        $deck = new Deck();
        $deckForm = $this->createForm(DeckType::class, $deck);
        $deckForm->handleRequest($request);

        if ($deckForm->isSubmitted() && $deckForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $deck->addUser($this->getUser());
            $entityManager->persist($deck);
            $entityManager->flush();

            return $this->redirectToRoute('deck_index');
        }
        $form = $this->createForm(BasicSearchType::class);
        $form->handleRequest($request);
        if (isset($_POST['basic_search']['name'])) {
            $search = $_POST['basic_search']['name'];
            return $this->redirectToRoute('searchpage', ['search' => $search]);
        }
        return $this->render('deck/index.html.twig', [
            'deck_card' => $deck,
            'deckForm' => $deckForm->createView(),
            'form' => $form->createView(),
            'decks' => $deckRepository->findDecksForUser($this->getUser())
        ]);
    }

    /**
     * @Route("/{id}", name="deck_show", methods={"GET","POST"})
     */
    public function show(Deck $deck, DeckRepository $deckRepository, PaginatorInterface $paginator, GameCardRepository $gameCardRepository, Request $request): Response
    {
        $deckForm = $this->createForm(DeckType::class, $deck);
        $deckForm->handleRequest($request);
        $totalCards = $deck->getDeckCards()->getValues();
        $countCards = [];
        $totalCount = 0;

        foreach ($totalCards as $card) {
            $gamecard = $gameCardRepository->findBy(['id' => $card->getIdCard()]);
            $cards[$gamecard[0]->getCardId()] = $gamecard[0];
            $countCards[] = $card->getCount();
            $totalCount += $card->getCount();
        }
        if (count($totalCards) === 0) {
            $this->addFlash('warning', "Votre deck est vide");
            return $this->redirectToRoute('deck_index');
        }

        if ($deckForm->isSubmitted() && $deckForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $deck->addUser($this->getUser());
            $entityManager->persist($deck);
            $entityManager->flush();

            return $this->redirectToRoute('deck_index');
        }
        $cardsPages = $paginator->paginate(
            $cards,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            20
        );
        $form = $this->createForm(BasicSearchType::class);
        $form->handleRequest($request);
        if (isset($_POST['basic_search']['name'])) {
            $search = $_POST['basic_search']['name'];
            return $this->redirectToRoute('searchpage', ['search' => $search]);
        }

        return $this->render('deck/show.html.twig', [
            'deck' => $deck,
            'cards' => $cards,
            'countCards' => $countCards,
            'count' => $totalCards,
            'totalCount' => $totalCount,
            'cardsPages' => $cardsPages,
            'form' => $form->createView(),
            'deckForm' => $deckForm->createView(),
            'decks' => $deckRepository->findDecksForUser($this->getUser()),
            'library' => $gameCardRepository->findCardsByUser($this->getUser()),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="deck_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Deck $deck): Response
    {
        $deckForm = $this->createForm(DeckType::class, $deck);
        $deckForm->handleRequest($request);

        if ($deckForm->isSubmitted() && $deckForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('deck_index', ['id' => $deck->getId()]);
        }
        $form = $this->createForm(BasicSearchType::class);
        $form->handleRequest($request);
        if (isset($_POST['basic_search']['name'])) {
            $search = $_POST['basic_search']['name'];
            return $this->redirectToRoute('searchpage', ['search' => $search]);
        }
        return $this->render('deck/edit.html.twig', [
            'deck' => $deck,
            'deckForm' => $deckForm->createView(),
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
     * @Route("/{idCard}/{idDeck}/add/{path}", name="add_to_deck", methods="GET", requirements={"path"=".+"})
     */
    public function addToDeck(
        string $idCard,
        int $idDeck,
        DeckRepository $deckRepository,
        DeckCardRepository $deckCardRepository,
        $path

    ): Response
    {
        if (empty($deckCardRepository->findDeckByCard($idCard,$idDeck))) {
            $deckCard = new DeckCard();
            $deckCard->setIdCard($idCard);
            $deckCard->addDeck($deckRepository->findOneBy(['id' => $idDeck]));

        } else {
//            $deckCard = $deckCardRepository->findBy(['idCard' => $idCard]);
            $deckCard = $deckCardRepository->findDeckByCard($idCard,$idDeck);

            $deckCard = $deckCard[0];
//            dd($deckCard);

            $deckCard->addDeck($deckRepository->findOneBy(['id' => $idDeck]));
        }

        $deckCard->setCount($deckCard->getCount()+1);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($deckCard);
        $entityManager->flush();

        return $this->redirect($path);

    }

    /**
     * @Route("/{idCard}/{idDeck}/remove/{path}", name="remove_to_deck", methods="GET|DELETE", requirements={"path"=".+"})
     */
    public function removeToDeck(
        string $idCard,
        int $idDeck,
        DeckRepository $deckRepository,
        DeckCardRepository $deckCardRepository,
        Request $request,
        $path

    ): Response
    {
        $deckCard = $deckCardRepository->findBy(['idCard' => $idCard]);

        $deckCard[0]->setCount($deckCard[0]->getCount()-1);
        if ($deckCard[0]->getCount() === 0) {
            return $this->redirect($path);
        }
        $deckCard[0]->addDeck($deckRepository->findOneBy(['id' => $idDeck]));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($deckCard[0]);
        $entityManager->flush();

        return $this->redirect($path);
    }
}
