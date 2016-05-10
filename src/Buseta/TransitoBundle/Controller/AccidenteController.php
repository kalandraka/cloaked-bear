<?php

namespace Buseta\TransitoBundle\Controller;

use Buseta\TransitoBundle\Entity\Accidente;
use Buseta\TransitoBundle\Form\Filter\AccidenteFilter;
use Buseta\TransitoBundle\Form\Model\AccidenteFilterModel;
use Buseta\TransitoBundle\Form\Type\AccidenteType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Accidente controller.
 *
 * @Route("/accidente")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Tránsito", routeName="transito_principal")
 */
class AccidenteController extends Controller
{
    /**
     * Lists all Accidente entities.
     *
     * @Route("/", name="accidente")
     * @Method("GET")
     * @Breadcrumb(title="Listado de accidentes", routeName="accidente")
     */
    public function indexAction(Request $request)
    {
        $filter = new AccidenteFilterModel();

        $form = $this->createForm(new AccidenteFilter(), $filter, array(
            'action' => $this->generateUrl('accidente'),
        ));

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaTransitoBundle:Accidente')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaTransitoBundle:Accidente')->filter();
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        return $this->render('BusetaTransitoBundle:Accidente:index.html.twig', array(
            'entities'      => $entities,
            'filter_form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Accidente entity.
     *
     * @Route("/new", name="accidente_new", methods={"GET"}, options={"expose":true})
     * @Breadcrumb(title="Crear Nuevo Accidente", routeName="accidente_new")
     */
    public function newAction()
    {
        $entity = new Accidente();
        $form   = $this->createCreateForm($entity);

        return $this->render('BusetaTransitoBundle:Accidente:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Accidente entity.
     *
     * @param Accidente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Accidente $entity)
    {
        $form = $this->createForm('transito_accidente', $entity, array(
            'action' => $this->generateUrl('accidente_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Creates a new Accidente entity.
     *
     * @Route("/create", name="accidente_create", methods={"POST"}, options={"expose":true})
     * @Breadcrumb(title="Crear Nuevo Accidente", routeName="accidente_create")
     */
    public function createAction(Request $request)
    {
        $entity = new Accidente();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('accidente_show', array('id' => $entity->getId())));
        }

        return $this->render('BusetaTransitoBundle:Accidente:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Accidente entity.
     *
     * @Route("/{id}/show", name="accidente_show")
     * @Method("GET")
     * @Breadcrumb(title="Ver Datos de Accidente", routeName="accidente_show", routeParameters={"id"})
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaTransitoBundle:Accidente')->find($id);
        $penal = null;
        if($entity->getParte() == 'PENAL')
        {
            $penal = $em->getRepository('BusetaTransitoBundle:PenalAccidente')->findOneByAccidente($entity);
        }
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Accidente entity.');
        }

        return $this->render('BusetaTransitoBundle:Accidente:show.html.twig', array(
            'entity'      => $entity,
            'penal'      => $penal
        ));
    }

    /**
     * Displays a form to edit an existing Accidente entity.
     *
     * @param $id
     *
     * @return Response
     *
     * @Route("/{id}/edit", name="accidente_edit")
     * @Method("GET")
     * @Breadcrumb(title="Modificar Accidente", routeName="accidente_edit", routeParameters={"id"})
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaTransitoBundle:Accidente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Accidente entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('BusetaTransitoBundle:Accidente:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Accidente entity.
     *
     * @param Accidente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Accidente $entity)
    {
        $form = $this->createForm(new AccidenteType(), $entity, array(
            'action' => $this->generateUrl('accidente_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Accidente entity.
     *
     * @Route("/{id}/update", name="accidente_update", options={"expose": true})
     * @Method({"POST", "PUT"})
     * @Breadcrumb(title="Modificar Accidente", routeName="accidente_update", routeParameters={"id"})
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaTransitoBundle:Accidente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Accidente entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('accidente_show', array('id' => $id)));
        }

        return $this->render('BusetaTransitoBundle:Accidente:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * @Route("/{id}/cambiarParteAccidente/{state}", name="cambiarParteAccidente", options={"expose": true})
     * @Breadcrumb(title="Cambiar parte accidente", routeName="cambiarParteAccidente", routeParameters={"id", "state"})
     */
    public function cambiarParteAccidenteAction($id, $state)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BusetaTransitoBundle:Accidente')->find($id);
        $entity->setEstado($state);
        $entity->setParte($state);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('accidente_show', array('id' => $id)));
    }
}
