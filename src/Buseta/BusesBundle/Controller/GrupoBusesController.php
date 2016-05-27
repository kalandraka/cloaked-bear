<?php

namespace Buseta\BusesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Buseta\BusesBundle\Entity\GrupoBuses;
use Buseta\BusesBundle\Form\Type\GrupoBusesType;
use Buseta\BusesBundle\Form\Filter\GrupoBusesFilter;
use Buseta\BusesBundle\Form\Model\GrupoBusesFilterModel;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * GrupoBuses controller.
 *
 * @Route("/grupobuses")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Vehículos", routeName="busetabuses_dashboard")
 */
class GrupoBusesController extends Controller
{

    /**
     * Lists all GrupoBuses entities.
     *
     * @Route("/", name="grupobuses")
     * @Method("GET")
     * @Template()
     * @Breadcrumb(title="Listado de Grupos Buses ", routeName="grupobuses")
     */
    public function indexAction(Request $request)
    {
        $filter = new GrupoBusesFilterModel();

        $form = $this->createForm(new GrupoBusesFilter(), $filter, array(
            'action' => $this->generateUrl('grupobuses'),
        ));

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBusesBundle:GrupoBuses')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBusesBundle:GrupoBuses')->filter();
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        return $this->render('BusetaBusesBundle:GrupoBuses:index.html.twig', array(
            'entities'      => $entities,
            'filter_form'   => $form->createView(),
        ));
    }
    /**
     * Creates a new GrupoBuses entity.
     *
     * @Route("/", name="grupobuses_create")
     * @Method("POST")
     * @Template("BusetaBusesBundle:GrupoBuses:new.html.twig")
     * @Breadcrumb(title="Crear Nuevo Grupo Buses", routeName="grupobuses_create")
     */
    public function createAction(Request $request)
    {
        $entity = new GrupoBuses();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('grupobuses_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a GrupoBuses entity.
     *
     * @param GrupoBuses $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(GrupoBuses $entity)
    {
        $form = $this->createForm(new GrupoBusesType(), $entity, array(
            'action' => $this->generateUrl('grupobuses_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new GrupoBuses entity.
     *
     * @Route("/new", name="grupobuses_new")
     * @Method("GET")
     * @Template()
     * @Breadcrumb(title="Crear Nuevo Grupo Buses", routeName="grupobuses_new")
     */
    public function newAction()
    {
        $entity = new GrupoBuses();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a GrupoBuses entity.
     *
     * @Route("/{id}", name="grupobuses_show")
     * @Method("GET")
     * @Template()
     * @Breadcrumb(title="Ver Datos de Grupo Buses", routeName="grupobuses_show", routeParameters={"id"})
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBusesBundle:GrupoBuses')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GrupoBuses entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing GrupoBuses entity.
     *
     * @Route("/{id}/edit", name="grupobuses_edit")
     * @Method("GET")
     * @Template()
     * @Breadcrumb(title="Modificar Grupo Buses", routeName="grupobuses_edit", routeParameters={"id"})
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBusesBundle:GrupoBuses')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GrupoBuses entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a GrupoBuses entity.
    *
    * @param GrupoBuses $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(GrupoBuses $entity)
    {
        $form = $this->createForm(new GrupoBusesType(), $entity, array(
            'action' => $this->generateUrl('grupobuses_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing GrupoBuses entity.
     *
     * @Route("/{id}", name="grupobuses_update")
     * @Method("PUT")
     * @Template("BusetaBusesBundle:GrupoBuses:edit.html.twig")
     * @Breadcrumb(title="Modificar Grupo Buses", routeName="grupobuses_update", routeParameters={"id"})
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBusesBundle:GrupoBuses')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find GrupoBuses entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('grupobuses_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a GrupoBuses entity.
     *
     * @Route("/{id}", name="grupobuses_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BusetaBusesBundle:GrupoBuses')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find GrupoBuses entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('grupobuses'));
    }

    /**
     * Creates a form to delete a GrupoBuses entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('grupobuses_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
