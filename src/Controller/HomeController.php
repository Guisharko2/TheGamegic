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

class HomeController extends AbstractController
{

    /**
     * @Route("/not_found", name="not_found", methods={"GET"})
     */
    public function not(int $next=1): Response
    {
        $client = new Client([
            RequestOptions::HTTP_ERRORS => false,
        ]);

        $url = '&unique=card&include_multilingual=true&format=image&sas=grid&order=name&page=';
        $search = '';
        if ($_GET){
            $search=$_GET['search'];
        }
        $nameCard = $client->request('GET', $this->uri . $search . $url . $next);
        $statusCode = $nameCard->getStatusCode();
        if ($statusCode > 400) {
            return $this->redirectToRoute('not_found');
        }
        if(isset($_GET['search'])) {

            return $this->redirectToRoute('searchpage', ['search' => $_GET['search'], 'next' => $next, ]);
        }

        return $this->render('homepage/not_found.html.twig');
    }
    /**
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function index(int $next=1): Response
    {
        $client = new Client([
            RequestOptions::HTTP_ERRORS => false,
        ]);

        $url = '&unique=card&include_multilingual=true&format=image&sas=grid&order=name&page=';
        $search = '';
        if ($_GET){
            $search=$_GET['search'];
        }
        $nameCard = $client->request('GET', $this->uri . $search . $url . $next);
        $statusCode = $nameCard->getStatusCode();
        if ($statusCode > 400) {
            return $this->redirectToRoute('not_found');
        }
        if(isset($_GET['search'])) {

            return $this->redirectToRoute('searchpage', ['search' => $_GET['search'], 'next' => $next, ]);
        }

        return $this->render('homepage/index.html.twig');
    }

    private $uri = 'https://api.scryfall.com/cards/search?q=';

    /**
     * @Route("/searchpage/{search}/{next}", name="searchpage", methods={"GET"},requirements={"next"})
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function basicResearch(string $search, Request $request, PaginatorInterface $paginator, int $next=1): Response
    {

        $client = new Client([
            RequestOptions::HTTP_ERRORS => false,
        ]);

        $url = '&unique=card&include_multilingual=true&format=image&sas=grid&order=name&page=';
        $nameCard = $client->request('GET', $this->uri . $search . $url .$next);
        $statusCode = $nameCard->getStatusCode();
        if ($statusCode > 300) {
                return $this->redirectToRoute('not_found');
        }
        if(isset($_GET['search'])) {

                return $this->redirectToRoute('searchpage', ['search' => $_GET['search'], 'next' => $next]);
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
     * @Route("/advancedSearchpage/", name="advanced_search", methods={"GET"})
     */
    public function advancedResearch(int $next=1): Response
    {
        $client = new Client([
            RequestOptions::HTTP_ERRORS => false,
        ]);

        $url = '&unique=card&include_multilingual=true&format=image&sas=grid&order=name&page=';
        $search = '';
        if ($_GET){
            $search=$_GET['search'];
        }
        $nameCard = $client->request('GET', $this->uri . $search . $url . $next);
        $statusCode = $nameCard->getStatusCode();
        if ($statusCode > 400) {
            return $this->redirectToRoute('not_found');
        }
        if(isset($_GET['search'])) {

            return $this->redirectToRoute('searchpage', ['search' => $_GET['search'], 'next' => $next, ]);
        }
        return $this->render('homepage/advanced_search.html.twig');
    }
}
