<?php

namespace Buseta\TransitoBundle\Controller;

use Buseta\TransitoBundle\Entity\Multa;
use Buseta\TransitoBundle\Form\Filter\MultaFilter;
use Buseta\TransitoBundle\Form\Model\MultaFilterModel;
use Buseta\TransitoBundle\Form\Type\MultaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Multa controller.
 *
 * @Route("/multa")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Tránsito", routeName="transito_principal")
 */
class MultaController extends Controller
{
    /**
     * Lists all Multa entities.
     *
     * @Route("/", name="multa")
     * @Method("GET")
     * @Breadcrumb(title="Listado de Multas", routeName="multa")
     */
    public function indexAction(Request $request)
    {
        $filter = new MultaFilterModel();

        $form = $this->createForm(new MultaFilter(), $filter, array(
            'action' => $this->generateUrl('multa'),
        ));

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaTransitoBundle:Multa')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaTransitoBundle:Multa')->filter();
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        return $this->render('BusetaTransitoBundle:Multa:index.html.twig', array(
            'entities'      => $entities,
            'filter_form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Multa entity.
     *
     * @Route("/new", name="multa_new", methods={"GET"}, options={"expose":true})
     * @Breadcrumb(title="Crear Nueva Multa", routeName="multa_new")
     */
    public function newAction()
    {
        $entity = new Multa();
        $form   = $this->createCreateForm($entity);

        return $this->render('BusetaTransitoBundle:Multa:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Multa entity.
     *
     * @param Multa $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Multa $entity)
    {
        $form = $this->createForm('transito_multa', $entity, array(
            'action' => $this->generateUrl('multa_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Creates a new Multa entity.
     *
     * @Route("/create", name="multa_create", methods={"POST"}, options={"expose":true})
     * @Breadcrumb(title="Crear Nueva Multa", routeName="multa_create")
     */
    public function createAction(Request $request)
    {
        $entity = new Multa();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('multa_show', array('id' => $entity->getId())));
        }

        return $this->render('BusetaTransitoBundle:Multa:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Multa entity.
     *
     * @Route("/{id}/show", name="multa_show")
     * @Method("GET")
     * @Breadcrumb(title="Ver Datos de Multa", routeName="multa_show", routeParameters={"id"})
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaTransitoBundle:Multa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Multa entity.');
        }

        return $this->render('BusetaTransitoBundle:Multa:show.html.twig', array(
            'entity'      => $entity
        ));
    }

    /**
     * Displays a form to edit an existing Multa entity.
     *
     * @param Multa $multa
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{id}/edit", name="multa_edit")
     * @Method("GET")
     * @Breadcrumb(title="Modificar Multa", routeName="multa_edit", routeParameters={"id"})
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaTransitoBundle:Multa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Multa entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('BusetaTransitoBundle:Multa:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Multa entity.
     *
     * @param Multa $multa The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Multa $entity)
    {
        $form = $this->createForm(new MultaType(), $entity, array(
            'action' => $this->generateUrl('multa_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Multa entity.
     *
     * @Route("/{id}/update", name="multa_update", options={"expose": true})
     * @Method({"POST", "PUT"})
     * @Breadcrumb(title="Modificar Multa", routeName="multa_update", routeParameters={"id"})
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaTransitoBundle:Multa')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Multa entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('multa_show', array('id' => $id)));
        }

        return $this->render('BusetaTransitoBundle:Multa:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * @Route("/{id}/cambiarEstadoMulta/{state}", name="cambiarEstadoMulta", options={"expose": true})
     * @Breadcrumb(title="Cambiar estado multa", routeName="cambiarEstadoMulta", routeParameters={"id", "state"})
     */
    public function cambiarEstadoMultaAction($id, $state)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BusetaTransitoBundle:Multa')->find($id);

        if($state == 'AP') {
            $entity->setEstado('AP');
            $entity->setApelada(true);
        } else if($state == 'PA') {
            $entity->setEstado('PA');
        }

        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('multa_show', array('id' => $id)));
    }

    /**
     *
     * @param Request $request
     *
     *
     * @Route("/multa_apelada_result", name="multa_apelada_result",
     *   options={"expose": true})
     * @return Response
     */
    public function multaApeladaResultAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $entity = $em->getRepository('BusetaTransitoBundle:Multa')->find($request->query->get('id'));
        $state = $request->query->get('state');
        $importeAbogado = $request->query->get('importeAbogado');
        if($state == 'WN') {
            $entity->setEstado('WN');
            $entity->setImporteAbogado($importeAbogado);
            $entity->setGanada(true);
        } else if($state == 'LS') {
            $entity->setEstado('LS');
            $entity->setImporteAbogado($importeAbogado);
        }
        $em->persist($entity);
        $em->flush();

        return new Response(json_encode("success"), 200);
    }
}
