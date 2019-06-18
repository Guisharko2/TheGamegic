<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\User;
use App\Entity\UserCard;
use App\Form\BasicSearchType;
use App\Repository\DeckRepository;
use App\Repository\GameCardRepository;
use App\Repository\UserCardRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/card")
 */
class CardController extends AbstractController
{
    /**
     * @Route("/", name="card_index", methods={"GET|POST"})
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index(
        UserCardRepository $userCardRepository,
        PaginatorInterface $paginator,
        Request $request,
        DeckRepository $deckRepository,
        GameCardRepository $gameCardRepository
    ): Response
    {
        $form = $this->createForm(BasicSearchType::class);
        $form->handleRequest($request);
        $totalCards = $userCardRepository->byUser($this->getUser());
        $countCards = [];
        if (count($totalCards) === 0) {
            $this->addFlash('warning', "Votre bibliothèque est vide");
            return $this->redirectToRoute('homepage');
        }
        foreach ($totalCards as $card) {
            $gamecard = $gameCardRepository->findBy(['card_id' => $card->getIdCard()]);
            $cards[$gamecard[0]->getCardId()] = $gamecard[0];
            $countCards[] = $card->getCount();
        }
        if ($_POST) {
            $search = $_POST['basic_search']['name'];
            return $this->redirectToRoute('searchpage', ['search' => $search]);
        }
        $cardsPages = $cards;
        $cardsPages = $paginator->paginate(
            $cardsPages,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            20
        );
        return $this->render('card/index.html.twig', [
            'cards' => $cards,
            'cardsPages' => $cardsPages,
            'countCards' => $countCards,
            'decks' => $deckRepository->findDecksForUser($this->getUser()),
            'form' => $form->createView(),

        ]);
    }


    /**
     * @Route("/{id}", name="card_show", methods={"GET|POST"})
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function show(Request $request, string $id, GameCardRepository $gameGardRepository): Response
    {
        $card = $gameGardRepository->findBy(['id' => $id]);
        $form = $this->createForm(BasicSearchType::class);
        $form->handleRequest($request);
        if ($_POST) {
            $search = $_POST['basic_search']['name'];
        }
        if (!empty(trim($search))) {
            return $this->redirectToRoute('searchpage', ['search' => $search]);
        }
        $manas = '';
        return $this->render('card/show.html.twig', ['card' => $card, 'manas' => $manas]);
    }

//    /**
//     * @Route("/{idCard}/add/{path}", name="add_card", methods="GET", requirements={"path"=".+"})
//     */
//    public function addCard(string $idCard,GameCardRepository $gameGardRepository, $path): Response
//    {
//        if ($gameGardRepository->findBy(['id' => $idCard])) {
//            $card = $gameGardRepository->findOneBy(['id' => $idCard]);
//            $card->addUser($this->getUser());
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($card);
//            $entityManager->flush();
//        }
//        return $this->redirect($path);
//    }

    /**
     * @Route("/{idCard}/add/{path}", name="add_card", methods="GET", requirements={"path"=".+"})
     */
    public function addCard(string $idCard, UserCardRepository $userCardRepository, GameCardRepository $gameCardRepository, $path): Response
    {
        $card = $gameCardRepository->findOneBy(['id' => $idCard]);

        $usercard = $userCardRepository->findBy(['idCard' => $card->getCardId()]);


        if ((!$userCardRepository->findBy(['idCard' => $card->getCardId()]))
            || ($usercard[0]->getUser()->getValues()[0] != $this->getUser())
        ) {
            $usercard = new UserCard;
            $usercard->addUser($this->getUser());
            $usercard->setCount(1);
            $usercard->setIdCard($card->getCardId());
        } else {

            $usercard[0]->setCount($usercard[0]->getCount() + 1);
            $usercard = $usercard[0];
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($usercard);
        $entityManager->flush();

        return $this->redirect($path);
    }

    /**
     * @Route("/{idCard}/remove/{path}", name="remove_card", methods="GET", requirements={"path"=".+"})
     */
    public function removeCard(string $idCard, UserCardRepository $userCardRepository, GameCardRepository $gameCardRepository, $path): Response
    {
        $card = $gameCardRepository->findOneBy(['id' => $idCard]);

        $usercard = $userCardRepository->findBy(['idCard' => $card->getCardId()]);

        $usercard[0]->setCount($usercard[0]->getCount() - 1);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($usercard[0]);
        $entityManager->flush();

        return $this->redirect($path);
    }
}
