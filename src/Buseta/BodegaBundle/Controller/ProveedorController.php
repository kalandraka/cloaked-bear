<?php

namespace Buseta\BodegaBundle\Controller;

use Buseta\BodegaBundle\Form\Filter\ProveedorFilter;
use Buseta\BodegaBundle\Form\Model\ProveedorFilterModel;
use Buseta\BodegaBundle\Form\Model\ProveedorModel;
use Buseta\NomencladorBundle\Entity\FormaPago;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Buseta\BodegaBundle\Entity\Proveedor;
use Buseta\BodegaBundle\Form\Type\ProveedorType;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Proveedor controller.
 *
 * @Route("/bodega/proveedor")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Bodegas", routeName="bodega_principal")
 */
class ProveedorController extends Controller
{
    /**
     * Lists all Proveedor entities.
     *
     * @Route("/", name="proveedor")
     *
     * @Method("GET")
     * @Breadcrumb(title="Listado de Proveedores", routeName="proveedor")
     */
    public function indexAction(Request $request)
    {
        $filter = new ProveedorFilterModel();

        $form = $this->createForm(new ProveedorFilter(), $filter, array(
            'action' => $this->generateUrl('proveedor'),
        ));

        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBodegaBundle:Proveedor')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBodegaBundle:Proveedor')->filter();
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        return $this->render('BusetaBodegaBundle:Proveedor:index.html.twig', array(
            'entities' => $entities,
            'filter_form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Proveedor entity.
     *
     * @Route("/", name="proveedor_create", options={"expose":true})
     *
     * @Method("POST")
     * @Breadcrumb(title="Crear Nuevo Proveedor", routeName="proveedor_create")
     */
    public function createAction(Request $request)
    {
        $entity = new ProveedorModel();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $proveedor = $entity->getProveedorData();
            $em->persist($proveedor);
            $em->flush();

            return $this->redirectToRoute('proveedor', array());
        }
    }

    /**
     * Creates a form to create a Proveedor entity.
     *
     * @param Proveedor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ProveedorModel $entity)
    {
        $form = $this->createForm(new ProveedorType(), $entity, array(
            'action' => $this->generateUrl('proveedor_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Proveedor entity.
     *
     * @Route("/new", name="proveedor_new")
     *
     * @Method("GET")
     * @Breadcrumb(title="Crear Nuevo Proveedor", routeName="proveedor_new")
     */
    public function newAction()
    {
        $entity = new ProveedorModel();

        $form = $this->createCreateForm($entity);

        return $this->render('BusetaBodegaBundle:Proveedor:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Proveedor entity.
     *
     * @Route("/{id}", name="proveedor_show")
     *
     * @Method("GET")
     * @Breadcrumb(title="Ver Datos de Proveedor", routeName="proveedor_show", routeParameters={"id"})
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:Proveedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proveedor entity.');
        }

        $entity = new ProveedorModel($entity);

        return $this->render('BusetaBodegaBundle:Proveedor:show.html.twig', array(
            'entity' => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing Proveedor entity.
     *
     * @Route("/{id}/edit", name="proveedor_edit")
     *
     * @Method("GET")
     * @Breadcrumb(title="Modificar Proveedor", routeName="proveedor_edit", routeParameters={"id"})
     */
    public function editAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $entity = $em->getRepository('BusetaBodegaBundle:Proveedor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proveedor entity.');
        }

        $entity = new ProveedorModel($entity);
        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BusetaBodegaBundle:Proveedor:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Proveedor entity.
     *
     * @param Proveedor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ProveedorModel $entity)
    {
        $form = $this->createForm(new ProveedorType(), $entity, array(
            'action' => $this->generateUrl('proveedor_update', array('id' => $entity->getProveedorId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Proveedor entity.
     *
     * @Route("/{id}", name="proveedor_update", options={"expose":true})
     *
     * @Method("PUT")
     * @Breadcrumb(title="Modificar Proveedor", routeName="proveedor_update", routeParameters={"id"})
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $entity = $em->getRepository('BusetaBodegaBundle:Proveedor')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proveedor entity.');
        }

        $model = new ProveedorModel($entity);
        $editForm = $this->createEditForm($model);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setModelData($model);
            $entity->setFoto(null);
            $em->persist($entity);

            $em->flush();

            return $this->redirect($this->generateUrl('proveedor_update', array('id' => $entity->getId())));

        }
        $entity->setFoto(null);
        return $this->redirect($this->generateUrl('proveedor_update', array('id' => $entity->getId())));

    }

    /**
     * Deletes a Proveedor entity.
     *
     * @Route("/{id}", name="proveedor_delete")
     *
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $entity = $em->getRepository('BusetaBodegaBundle:Proveedor')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Proveedor entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('proveedor'));
    }

    /**
     * Creates a form to delete a Proveedor entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('proveedor_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }
}
