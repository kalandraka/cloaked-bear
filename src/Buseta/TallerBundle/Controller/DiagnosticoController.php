<?php

namespace Buseta\TallerBundle\Controller;

use Buseta\TallerBundle\Entity\Diagnostico;
use Buseta\TallerBundle\Entity\Observacion;
use Buseta\TallerBundle\Entity\ObservacionDiagnostico;
use Buseta\TallerBundle\Entity\OrdenTrabajo;
use Buseta\TallerBundle\Form\Type\ObservacionDiagnosticoType;
use Buseta\TallerBundle\Form\Type\ObservacionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Buseta\TallerBundle\Form\Type\DiagnosticoType;
use Buseta\TallerBundle\Form\Model\DiagnosticoFilterModel;
use Buseta\TallerBundle\Form\Filter\DiagnosticoFilter;

/**
 * Diagnostico controller.
 *
 */
class DiagnosticoController extends Controller
{

    public function generarOrdenTrabajoAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $diagnostico = $em->getRepository('BusetaTallerBundle:Diagnostico')->find($id);

        if (!$diagnostico) {
            throw $this->createNotFoundException('Unable to find Diagnóstico entity.');
        }

        //Crear nueva Orden de Trabajo a partir del Diagnóstico seleccionado
        $ordenTrabajo = new OrdenTrabajo();
        $ordenTrabajo->setAutobus($diagnostico->getAutobus());
        $em->persist($ordenTrabajo);
        $em->flush();

        //Si se creó satisfactoriamente el Diagnostico
        //entonces deshabilitamos la opción "Adicionar Observaciones en reporte_show"
        //----PENDIENTE---

        return $this->redirect($this->generateUrl('diagnostico'));
    }

    /**
     * Lists all Diagnostico entities.
     */
    public function indexAction(Request $request)
    {
        $filter = new DiagnosticoFilterModel();

        $form = $this->createForm(new DiagnosticoFilter(), $filter, array(
            'action' => $this->generateUrl('diagnostico'),
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaTallerBundle:Diagnostico')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaTallerBundle:Diagnostico')->filter();
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            5
        );


        return $this->render('BusetaTallerBundle:Diagnostico:index.html.twig', array(
            'entities' => $entities,
            'filter_form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Diagnostico entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaTallerBundle:Diagnostico')->find($id);

        $reportes = $em->getRepository('BusetaTallerBundle:Reporte')->find($entity->getReporte()->getId());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Diagnostico entity.');
        }

        return $this->render('BusetaTallerBundle:Diagnostico:show.html.twig', array(
            'entity' => $entity,
            'reportes' => $reportes,
            'id' => $id
        ));
    }

    /**
     * Displays a form to create a new Diagnostico entity.
     *
     */
    public function newAction()
    {
        $entity = new Diagnostico();

        $observacion = $this->createForm(new ObservacionDiagnosticoType());

        $form   = $this->createCreateForm($entity);

        return $this->render('BusetaTallerBundle:Diagnostico:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'observacion'  => $observacion->createView(),
        ));
    }

    /**
     * Creates a form to create a Diagnostico entity.
     *
     * @param Diagnostico $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Diagnostico $entity)
    {
        $form = $this->createForm(new DiagnosticoType(), $entity, array(
            'action' => $this->generateUrl('diagnostico_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Creates a new Diagnostico entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Diagnostico();
        $form = $this->createCreateForm($entity);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            try {
                $em->persist($entity);
                $em->flush();

                $this->get('session')->getFlashBag()
                    ->add('success', 'Se ha creado el Diagnóstico de forma satisfactoria.');

                return $this->redirect($this->generateUrl('diagnostico_show', array('id' => $entity->getId())));
            } catch(\Exception $e) {
                $this->get('logger')
                    ->addCritical(sprintf('Ha ocurrido un error creando el Diagnóstico. Detalles: %s', $e->getMessage()));

                $this->get('session')->getFlashBag()
                    ->add('danger', 'Ha ocurrido un error creando el Diagnóstico.');
            }
        }

        return $this->render('BusetaTallerBundle:Diagnostico:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
}