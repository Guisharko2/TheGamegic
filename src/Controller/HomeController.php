<?php
/**
 * Created by PhpStorm.
 * User: wilder4
 * Date: 04/01/19
 * Time: 13:01
 */

namespace App\Controller;

use GuzzleHttp\RequestOptions;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use App\Entity\Card;

class HomeController extends AbstractController
{
    private $uri = 'https://api.scryfall.com/cards/search?q=';
    private $url = '&unique=card&include_multilingual=true&format=image&sas=grid&order=name';
    private $urlPage = '&page=';
    private $advanceSearch = 'grid&order=name&q=';
    private $language = '&include_multilingual=true';


    /**
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function index(int $next = 1): Response
    {
        $client = new Client([
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($_GET) {
            $search = $_GET['search'];
        } else {
            $search = '';
        }
        $nameCard = $client->request('GET', $this->uri . $search . $this->url . $this->urlPage . $next);
        $statusCode = $nameCard->getStatusCode();
        if ($statusCode > 400) {
            $this->addFlash('danger', "Aucune carte ne correspond à votre recherche");
            return $this->redirectToRoute('homepage');
        }
        if (!empty(trim($search))) {
            return $this->redirectToRoute('searchpage', ['search' => $_GET['search'], 'next' => $next,]);
        }

        return $this->render('homepage/index.html.twig');
    }

    /**
     * @Route("/searchpage/{search}/{next}", name="searchpage", methods={"GET|POST"},requirements={"next"})
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function basicResearch(
        string $search,
        Request $request,
        PaginatorInterface $paginator,
        int $next = 1
    ): Response {

        $client = new Client([
            RequestOptions::HTTP_ERRORS => false,
        ]);

        $nameCard = $client->request('GET', $this->uri . $search . $this->url . $this->urlPage . $next);
        $statusCode = $nameCard->getStatusCode();

        if (isset($_GET['search'])) {
            return $this->redirectToRoute('searchpage', ['search' => $_GET['search'], 'next' => $next]);
        }
        if ($statusCode > 300) {
            $this->addFlash('danger', "Aucune carte ne correspond à votre recherche");

            return $this->redirectToRoute('homepage');
        }
        $body = $nameCard->getBody();
        $json = json_decode($body->getContents(), true);
        $cardsPages = $json['data'];
        $cardsPages = $paginator->paginate(
            $cardsPages,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            20
        );
        $cardsPages->setPageRange(9);
        return $this->render('homepage/search.html.twig', [
            'cards' => $json['data'],
            'search' => $search,
            'next' => $next,
            'total' => $json['total_cards'],
            'cardsPages' => $cardsPages,
        ]);
    }

    /**
     * @Route("/advancedsearchpage/", name="advanced_search", methods={"GET"})
     */
    public function advancedResearch(int $next = 1): Response
    {
        $client = new Client([
            RequestOptions::HTTP_ERRORS => false,
        ]);
        $search = '';

        dump($_GET);
        if ($_GET) {
            if ($_GET['name']) {
                $search = $_GET['name'];
            }
            if ($_GET['oracle']) {
                $search .= '+oracle%3A' . $_GET['oracle'];
            }
        }
        $nameCard = $client->request(
            'GET',
            $this->uri .
            $this->advanceSearch .
            $search .
            $this->urlPage .
            $next .
            $this->language
        );
        $statusCode = $nameCard->getStatusCode();
        if ($statusCode > 400) {
            $this->addFlash('danger', "Aucune carte ne correspond à votre recherche");
            dump($search);

            return $this->redirectToRoute('advanced_search');
        }
        if (!empty($search)) {
            return $this->redirectToRoute('searchpage', ['search' => $search, 'next' => $next,]);
        }
        return $this->render('homepage/advanced_search.html.twig');
    }
}
