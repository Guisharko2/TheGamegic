<?php
/**
 * Created by PhpStorm.
 * User: wilder4
 * Date: 04/01/19
 * Time: 13:01
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;

class HomeController extends AbstractController
{

    /**
     * @Route("/not", name="not", methods={"GET"})
     */
    public function not(): Response
    {
        return $this->render('homepage/not_found.html.twig');
    }
    /**
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function index(int $page=1): Response
    {
        $client = new Client();
        $url = '&unique=card&sas=grid&order=name&page=';

        if(isset($_GET['search'])) {
            $search = $_GET['search'];
            $nameCard = $client->request('GET', $this->uri .$search. $url. $page);

            return $this->redirectToRoute('searchpage', ['search' => $_GET['search'], 'page' => 1, ]);
        }

        return $this->render('homepage/index.html.twig');
    }

    private $uri = 'https://api.scryfall.com/cards/search?q=';

    /**
     * @Route("/searchpage/{search}/{page}", name="searchpage", methods={"GET"},requirements={"page"})
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function apiRequest(string $search, int $page=1): Response
    {

        $client = new Client();
        $url = '&unique=card&sas=grid&order=name&page=';

        $nameCard = $client->request('GET', $this->uri . $search . $url . $page);
        $statusCode = $nameCard->getStatusCode();
        dump($statusCode);
        if ($statusCode > 300) {
                return $this->redirectToRoute('not');
        }
        if(isset($_GET['search'])) {

                return $this->redirectToRoute('searchpage', ['search' => $_GET['search'], 'page' => 1, ]);
        }
        $body = $nameCard->getBody();
        $json = json_decode($body->getContents(), true);


        return $this->render('homepage/search.html.twig', [
            'cards' => $json['data'],
            'search' => $search,
            'total' => $json['total_cards']

        ]);
    }
}
