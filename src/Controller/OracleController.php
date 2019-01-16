<?php

namespace App\Controller;

use App\Entity\Oracle;
use App\Form\OracleType;
use App\Repository\OracleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/oracle")
 */
class OracleController extends AbstractController
{
    /**
     * @Route("/", name="oracle_index", methods={"GET"})
     */
    public function index(OracleRepository $oracleRepository): Response
    {
        return $this->render('oracle/index.html.twig', ['oracles' => $oracleRepository->findAll()]);
    }

    /**
     * @Route("/new", name="oracle_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $oracle = new Oracle();
        $form = $this->createForm(OracleType::class, $oracle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($oracle);
            $entityManager->flush();

            return $this->redirectToRoute('oracle_index');
        }

        return $this->render('oracle/new.html.twig', [
            'oracle' => $oracle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="oracle_show", methods={"GET"})
     */
    public function show(Oracle $oracle): Response
    {
        return $this->render('oracle/show.html.twig', ['oracle' => $oracle]);
    }

    /**
     * @Route("/{id}/edit", name="oracle_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Oracle $oracle): Response
    {
        $form = $this->createForm(OracleType::class, $oracle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('oracle_index', ['id' => $oracle->getId()]);
        }

        return $this->render('oracle/edit.html.twig', [
            'oracle' => $oracle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="oracle_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Oracle $oracle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$oracle->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($oracle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('oracle_index');
    }
}
