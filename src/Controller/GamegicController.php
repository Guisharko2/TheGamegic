<?php

namespace App\Controller;

use App\Entity\GameCard;
use GuzzleHttp\TransferStats;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client;

class GamegicController extends AbstractController
{

//    private $scryfallUrl = 'https://api.scryfall.com/cards?page=';
//
//    /**
//     * @Route("/gamegic", name="gamegic")
//     */
//    public function setCardOnDataBase(Request $request): Response
//    {
//        set_time_limit(300000);
//        ini_set('memory_limit', '-1');
//        $entityManager = $this->getDoctrine()->getManager();
//        $client = new Client();
//        for ($i = 953; $i < 1419; $i++) {
//            $json = NULL;
//            $cards = $client->get('https://api.scryfall.com/cards',[
//                'query' => ['page'=>$i],
//                'on_stats' => function(TransferStats $stats) use (&$url){
//                $url = $stats->getEffectiveUri();
//                }
//            ]);
//            $body = $cards->getBody();
//            $json[] = json_decode($body->getContents(), true);
//
//            foreach ($json[0]['data'] as $value) {
//                $gameCard = new GameCard();
//                if (isset($value['object'])) {
//                    $gameCard->setObject($value['object']);
//                }
//                if (isset($value['id'])) {
//                    $gameCard->setCardId($value['id']);
//                }
//
//                if (isset($value['oracle_id'])) {
//                    $gameCard->setOracleId($value['oracle_id']);
//                }
//                if (isset($value['tcgplayer_id'])) {
//                    $gameCard->setTcgplayerId($value['tcgplayer_id']);
//                }
//                if (isset($value['name'])) {
//                    $gameCard->setName($value['name']);
//                }
//                if (isset($value['printed_name'])) {
//                    $gameCard->setPrintedName($value['printed_name']);
//                }
//                if (isset($value['lang'])) {
//                    $gameCard->setLang($value['lang']);
//                }
//                if (isset($value['released_at'])) {
//                    $gameCard->setReleasedAt($value['released_at']);
//                }
//                if (isset($value['uri'])) {
//                    $gameCard->setUri($value['uri']);
//                }
//                if (isset($value['printed_text'])) {
//                    $gameCard->setPrintedText($value['printed_text']);
//                }
//                if (isset($value['PrintedTypeLine'])) {
//                    $gameCard->setUri($value['printed_type_line']);
//                }
//                if (isset($value['scryfall_uri'])) {
//                    $gameCard->setScryfallUri($value['scryfall_uri']);
//                }
//                if (isset($value['layout'])) {
//                    $gameCard->setLayout($value['layout']);
//                }
//                if (isset($value['image_uris']['small'])) {
//                      $gameCard->setSmall($value['image_uris']['small']);
//                }
//                if (isset($value['image_uris']['normal'])) {
//                      $gameCard->setNormal($value['image_uris']['normal']);
//                }
//                if (isset($value['image_uris']['png'])) {
//                      $gameCard->setPng($value['image_uris']['png']);
//                }
//                if (isset($value['mana_cost'])) {
//                    $gameCard->setManaCost($value['mana_cost']);
//                }
//                if (isset($value['type_line'])) {
//                    $gameCard->setTypeLine($value['type_line']);
//                }
//                if (isset($value['oracle_text'])) {
//                    $gameCard->setOracleText($value['oracle_text']);
//                }
//                if (isset($value['power'])) {
//                    $gameCard->setPower($value['power']);
//                }
//                if (isset($value['toughness'])) {
//                    $gameCard->setToughness($value['toughness']);
//                }
//                if (isset($value['colors'])) {
//                    $gameCard->setColors($value['colors']);
//                }
//                if (isset($value['color_identity'])) {
//                    $gameCard->setColorIdentity($value['color_identity']);
//                }
//                if (isset($value['artist'])) {
//                    $gameCard->setArtist($value['artist']);
//                }
//                if (isset($value['collector_number'])) {
//                    $gameCard->setCollectorNumber($value['collector_number']);
//                }
//                if (isset($value['rarity'])) {
//                    $gameCard->setRarity($value['rarity']);
//                }
//                if (isset($value['illustration_id'])) {
//                    $gameCard->setIllustrationId($value['illustration_id']);
//                }
//                if (isset($value['prices'])) {
//                    $gameCard->setPrices($value['prices']);
//                }
//                if (isset($value['purchase_uris'])) {
//                    $gameCard->setPurchaseUris($value['purchase_uris']);
//                }
//                $entityManager->persist($gameCard);
//            }
//            $entityManager->flush();
//        }
//
//        return $this->render('gamegic/index.html.twig', [
//            'controller_name' => 'GamegicController',
//        ]);
//    }
}