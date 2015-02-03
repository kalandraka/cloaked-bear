<?php

namespace Buseta\TallerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Buseta\TallerBundle\Entity\MantenimientoPreventivo;
use Buseta\TallerBundle\Form\Type\MantenimientoPreventivoType;

/**
 * MantenimientoPreventivo controller.
 *
 * @Route("/mpreventivo")
 */
class MantenimientoPreventivoController extends Controller
{

    /**
     * Lists all MantenimientoPreventivo entities.
     *
     * @Route("/", name="mantenimientopreventivo", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BusetaTallerBundle:MantenimientoPreventivo')->findAll();

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1),
            10,
            array('pageParameterName' => 'page')
        );

        return $this->render('BusetaTallerBundle:MantenimientoPreventivo:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new MantenimientoPreventivo entity.
     *
     * @Route("/create", name="mantenimientopreventivo_create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $entity = new MantenimientoPreventivo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('mantenimientopreventivo_show', array('id' => $entity->getId())));
        }

        return $this->render('BusetaTallerBundle:MantenimientoPreventivo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a MantenimientoPreventivo entity.
     *
     * @param MantenimientoPreventivo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MantenimientoPreventivo $entity)
    {
        $form = $this->createForm(new MantenimientoPreventivoType(), $entity, array(
            'action' => $this->generateUrl('mantenimientopreventivo_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new MantenimientoPreventivo entity.
     *
     * @Route("/new", name="mantenimientopreventivo_new", methods={"GET"})
     */
    public function newAction()
    {
        $entity = new MantenimientoPreventivo();
        $form   = $this->createCreateForm($entity);

        return $this->render('BusetaTallerBundle:MantenimientoPreventivo:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a MantenimientoPreventivo entity.
     *
     * @Route("/{id}/show", name="mantenimientopreventivo_show", methods={"GET"})
     */
    public function showAction(MantenimientoPreventivo $entity)
    {
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('BusetaTallerBundle:MantenimientoPreventivo:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing MantenimientoPreventivo entity.
     *
     * @Route("/{id}/edit", name="mantenimientopreventivo_edit", methods={"GET"})
     */
    public function editAction(MantenimientoPreventivo $entity)
    {
        $editForm = $this->createEditForm($entity);

        return $this->render('BusetaTallerBundle:MantenimientoPreventivo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a MantenimientoPreventivo entity.
    *
    * @param MantenimientoPreventivo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(MantenimientoPreventivo $entity)
    {
        $form = $this->createForm(new MantenimientoPreventivoType(), $entity, array(
            'action' => $this->generateUrl('mantenimientopreventivo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing MantenimientoPreventivo entity.
     *
     * @Route("/{id}/update", name="mantenimientopreventivo_update", methods={"PUT"})
     */
    public function updateAction(Request $request, MantenimientoPreventivo $entity)
    {
        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('mantenimientopreventivo_edit', array('id' => $entity)));
        }

        return $this->render('', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a MantenimientoPreventivo entity.
     *
     * @Route("/{id}/delete", name="mantenimientopreventivo_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, MantenimientoPreventivo $entity)
    {
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');

            try {
                $em->remove($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'Ha sido eliminado satisfactoriamente.');
            } catch (\Exception $e) {
                $this->get('logger')->addCritical(
                    sprintf('Ha ocurrido un error eliminando una tarea de mantenimiento. Detalles: %s',
                        $e->getMessage()
                    ));
            }
        }

        return $this->redirect($this->generateUrl('mantenimientopreventivo'));
    }

    /**
     * Creates a form to delete a MantenimientoPreventivo entity by id.
     *
     * @param mixed $entity The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MantenimientoPreventivo $entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mantenimientopreventivo_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Selecciona los Subgrupos asociados al Grupo seleccionado.
     * @param Request $request
     * @Route("/select_subgrupo_grupo", name="ajax_select_subgrupo_grupo", methods={"GET"})
     */
    public function selectSubgroupBelongGroupAction(Request $request) {
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))
            return new \Symfony\Component\HttpFoundation\Response('Acceso Denegado', 403);

        if (!$request->isXmlHttpRequest())
            return new \Symfony\Component\HttpFoundation\Response('No es una petición Ajax', 500);

        $em = $this->get('doctrine.orm.entity_manager');
        $grupo_id = $request->query->get('grupo_id');
        $subgrupos = $em->getRepository('BusetaNomencladorBundle:Subgrupo')->findBy(array(
                'grupo' => $grupo_id
            ));

        $json = array();
        foreach ($subgrupos as $subgrupo) {
            $json[] = array(
                'id' => $subgrupo->getId(),
                'valor' => $subgrupo->getValor()
            );
        }

        return new \Symfony\Component\HttpFoundation\Response(json_encode($json), 200);
    }
}
