<?php

namespace Buseta\TallerBundle\Controller;

use Buseta\TallerBundle\Event\FilterOrdenTrabajoEvent;
use Buseta\TallerBundle\Event\FilterReporteEvent;
use Buseta\TallerBundle\Event\ReporteEvents;
use Buseta\TallerBundle\Event\OrdenTrabajoEvents;
use Buseta\TallerBundle\Form\Filter\OrdenTrabajoFilter;
use Buseta\TallerBundle\Form\Model\OrdenTrabajoFilterModel;
use Buseta\TallerBundle\Form\Model\OrdenTrabajoModel;
use Buseta\TallerBundle\Form\Type\TareaAdicionalType;
use Buseta\TallerBundle\Manager\MantenimientoPreventivoManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Buseta\TallerBundle\Entity\OrdenTrabajo;
use Buseta\BodegaBundle\Entity\SalidaBodega;
use Buseta\BodegaBundle\Entity\SalidaBodegaProducto;
use Buseta\TallerBundle\Form\Type\OrdenTrabajoType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Util\ClassUtils;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * OrdenTrabajo controller.
 * @Route("/ordentrabajo")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo Estación de Servicios", routeName="taller_principal")
 */
class OrdenTrabajoController extends Controller
{
    /**
     * Lists all OrdenTrabajo entities.
     * @Route("/ordentrabajo", name="ordentrabajo")
     * @Breadcrumb(title="Ordenes de Trabajo ", routeName="ordentrabajo")
     */
    public function indexAction(Request $request)
    {
        $filter = new OrdenTrabajoFilterModel();

        $form = $this->createForm(new OrdenTrabajoFilter(), $filter, array(
            'action' => $this->generateUrl('ordentrabajo'),
        ));
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (ClassUtils::getRealClass($user) === 'HatueySoft\SecurityBundle\Entity\User'){
            $filter->setGrupoBuses($user->getGrupoBuses());
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

                $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaTallerBundle:OrdenTrabajo')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaTallerBundle:OrdenTrabajo')->filter($filter);
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        return $this->render('BusetaTallerBundle:OrdenTrabajo:index.html.twig', array(
            'entities' => $entities,
            'filter_form' => $form->createView(),
        ));
    }

    /**
     * Creates a new OrdenTrabajoM entity.
     * @Route("/create", name="ordentrabajo_create")
     * @Method("POST")
     * @Breadcrumb(title="Crear Nueva Orden de Trabajo", routeName="ordentrabajo_create")
     */

    public function createAction(Request $request)
    {
        $ordenTrabajoModel = new OrdenTrabajoModel();
        $form = $this->createCreateForm($ordenTrabajoModel);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trans  = $this->get('translator');

            $ordenTrabajoManager = $this->get('buseta.taller.ordentrabajo.manager');

            if ($ordenTrabajo = $ordenTrabajoManager->crear($ordenTrabajoModel)) {
                $this->get('session')->getFlashBag()->add('success',
                    $trans->trans(
                        'messages.create.success',
                        array(),
                        'BusetaTallerBundle'
                    )
                );

                return $this->redirect($this->generateUrl('ordentrabajo_show', array('id' => $ordenTrabajo->getId())));
            } else {
                $this->get('session')->getFlashBag()->add('danger', 'Ha ocurrido un error al crear Orden de Trabajo');
            }
        }

        return $this->render('@BusetaTaller/OrdenTrabajo/new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Creates a form to create a OrdenTrabajo entity.
     *
     * @param OrdenTrabajoModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(OrdenTrabajoModel $entity)
    {
        $form = $this->createForm('buseta_tallerbundle_ordentrabajo', $entity, array(
            'action' => $this->generateUrl('ordentrabajo_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new OrdenTrabajo entity.
     * @Route("/new", name="ordentrabajo_new")
     * @Breadcrumb(title="Crear Nueva Orden de Trabajo", routeName="ordentrabajo_new")
     * @Security("is_granted('CREATE', 'Buseta\\TallerBundle\\Entity\\OrdenTrabajo')")
     */
    public function newAction()
    {
        $entity = new OrdenTrabajo();
        $tarea_adicional = $this->createForm(new TareaAdicionalType());

        $form   = $this->createCreateForm(new OrdenTrabajoModel());

        return $this->render('BusetaTallerBundle:OrdenTrabajo:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'tarea_adicional' => $tarea_adicional->createView(),
        ));
    }

    /**
     * Finds and displays a OrdenTrabajo entity.
     * @Route("/{id}/show", name="ordentrabajo_show")
     * @Breadcrumb(title="Ver Datos de Orden de Trabajo", routeName="ordentrabajo_show", routeParameters={"id"})
     * @Security("is_granted('SHOW', ordenTrabajo)")
     */
    public function showAction(OrdenTrabajo $ordenTrabajo)
    {
        $deleteForm = $this->createDeleteForm($ordenTrabajo->getId());

        return $this->render('BusetaTallerBundle:OrdenTrabajo:show.html.twig', array(
            'entity' => $ordenTrabajo,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing OrdenTrabajo entity.
     * @Route("/{id}/edit", name="ordentrabajo_edit")
     * @Breadcrumb(title="Modificar Orden de Trabajo", routeName="ordentrabajo_edit", routeParameters={"id"})
     * @Security("is_granted('EDIT', ordenTrabajo)")
     */
    public function editAction(OrdenTrabajo $ordenTrabajo)
    {
        $tarea_adicional = $this->createForm(new TareaAdicionalType());

        $editForm = $this->createEditForm(new OrdenTrabajoModel($ordenTrabajo));
        $deleteForm = $this->createDeleteForm($ordenTrabajo->getId());

        return $this->render('BusetaTallerBundle:OrdenTrabajo:edit.html.twig', array(
            'entity' => $ordenTrabajo,
            'edit_form' => $editForm->createView(),
            'tarea_adicional' => $tarea_adicional->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a OrdenTrabajo entity.
     *
     * @param OrdenTrabajoModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(OrdenTrabajoModel $entity)
    {
        $form = $this->createForm('buseta_tallerbundle_ordentrabajo', $entity, array(
            'action' => $this->generateUrl('ordentrabajo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing OrdenTrabajo entity.
     * @Route("/{id}/update", name="ordentrabajo_update")
     * @Method({"POST", "PUT"})
     * @Breadcrumb(title="Modificar Orden de Trabajo", routeName="ordentrabajo_update", routeParameters={"id"})
     * @Security("is_granted('EDIT', ordenTrabajo)")
     */
    public function updateAction(Request $request, OrdenTrabajo $ordenTrabajo)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $deleteForm = $this->createDeleteForm($ordenTrabajo->getId());
        $editForm = $this->createEditForm($ordenTrabajo);

        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('ordentrabajo_show', array('id' => $ordenTrabajo->getId())));
        }

        return $this->render('BusetaTallerBundle:OrdenTrabajo:edit.html.twig', array(
            'entity' => $ordenTrabajo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a OrdenTrabajo entity.
     *
     * @Security("is_granted("DELETE", ordenTrabajo)")
     */
    public function deleteAction(Request $request, OrdenTrabajo $ordenTrabajo)
    {
        $form = $this->createDeleteForm($ordenTrabajo->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');

            try {
                $em->remove($ordenTrabajo);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success',
                    'Se ha eliminado la Orden de Trabajo de forma satisfactoria.');
            } catch (\Exception $e) {
                $this->get('logger')->critical(sprintf('Ha ocurrido un error eliminando Orden de Trabajo. Detalles: %s.',
                    $e->getMessage()));
                $this->get('session')->getFlashBag()->add('danger',
                    'Ha ocurrido un error eliminando Orden de Trabajo.');
            }
        }

        return $this->redirect($this->generateUrl('ordentrabajo'));
    }

    /**
     * Creates a form to delete a OrdenTrabajo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ordentrabajo_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    /**
     * Updated automatically table Mantenimientos Preventivos when change select Autobus.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function selectAutobusMantenimientoPreventivoAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new \Symfony\Component\HttpFoundation\Response('Acceso Denegado', 403);
        }

        if (!$request->isXmlHttpRequest()) {
            return new \Symfony\Component\HttpFoundation\Response('No es una petición Ajax', 500);
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $autobus_id = $request->query->get('autobus_id');

        $mpreventivos = $em->getRepository('BusetaTallerBundle:MantenimientoPreventivo')->findBy(array(
            'autobus' => $autobus_id,
        ));

        $mpme = new MantenimientoPreventivoManager();

        $json = array();
        foreach ($mpreventivos as $mpreventivo) {
            $porcentaje = $mpme->getPorciento($em, $mpreventivo);

            $json[] = array(
                'id' => $mpreventivo->getId(),
                'grupo' => $mpreventivo->getGrupo()->getValor(),
                'subgrupo' => $mpreventivo->getSubgrupo()->getValor(),
                'tarea' => $mpreventivo->getTarea()->getValor(),
                'kilometraje' => $mpreventivo->getKilometraje(),
                'fecha_inicio' => $mpreventivo->getFechaInicio()->format('d/m/Y'),
                'fecha_final' => $mpreventivo->getFechaFinal()->format('d/m/Y'),
                'porcentage' => $porcentaje['porcentage'],
                'color' => $porcentaje['color'],
            );
        }

        return new \Symfony\Component\HttpFoundation\Response(json_encode($json), 200);
    }

    /**
     * Updated automatically Kilometraja when change select Autobus.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function selectAutobusKilometrajeAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new \Symfony\Component\HttpFoundation\Response('Acceso Denegado', 403);
        }

        if (!$request->isXmlHttpRequest()) {
            return new \Symfony\Component\HttpFoundation\Response('No es una petición Ajax', 500);
        }

        $diagnostico_id = $request->query->get('diagnostico_id');

        $em = $this->get('doctrine.orm.entity_manager');

        $diagnostico = $em->getRepository('BusetaTallerBundle:Diagnostico')->findBy(array(
            'id' => $diagnostico_id,
        ));

        $json = array();
        foreach ($diagnostico as $diag) {
            $json[] = array(
                'id' => $diag->getAutobus()->getId(),
                'kilometraje' => $diag->getAutobus()->getKilometraje(),
            );
        }

        return new \Symfony\Component\HttpFoundation\Response(json_encode($json), 200);
    }

    /**
     * Updated automatically select CentroCosto, Responsable y TipoOT when change select OrdenTrabajo
     *
     */
    public function select_salidabodega_ordentrabajoAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new Response('Acceso Denegado', 403);
        }

        if (!$request->isXmlHttpRequest()) {
            return new Response('No es una petición Ajax', 500);
        }

        $em = $this->get('doctrine.orm.entity_manager');

        $ordenTrabajo = $em->find('BusetaTallerBundle:OrdenTrabajo', $request->query->get('orden_trabajo_id'));

        return new JsonResponse(array(
            'centro_costo' => $ordenTrabajo->getAutobus()->getId(),
            'responsable' => $ordenTrabajo->getRealizadaPor()->getId(),
            'tipo_ot' => $ordenTrabajo->getPrioridad(),
        ), 200);
    }


    /**
     * recibe por get el iddiagnostico y devuelve el autobus al que se le hizo el diagnostico
     */
    public function select_diagnostico_autobusAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            return new \Symfony\Component\HttpFoundation\Response('No es una petición Ajax', 500);
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $diagnostico_id = $request->query->get('diagnostico_id');

        $diagnostico = $em->getRepository('BusetaTallerBundle:Reporte')->findBy(array(
            'id' => $diagnostico_id,
        ));

        $json = array();
        foreach ($diagnostico as $diag) {
            $json[] = array(
                'id' => $diag->getAutobus()->getId(),
                'matricula' => $diag->getAutobus()->getMatricula(),
            );
        }


        return new \Symfony\Component\HttpFoundation\Response(json_encode($json), 200);
    }


    public function selectdiagdiagporAction(Request $request)
    {

        if (!$request->isXmlHttpRequest()) {
            return new \Symfony\Component\HttpFoundation\Response('No es una petición Ajax', 500);
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $diagnostico_id = $request->query->get('diagnostico_id');

        $diagnostico = $em->getRepository('BusetaTallerBundle:Diagnostico')->findBy(array(
            'id' => $diagnostico_id,
        ));

        $json = array();
        foreach ($diagnostico as $diag) {
            $json[] = array(
                'id' => $diag->getReporte()->getReporta()->getId(),
                'nombre' => $diag->getReporte()->getReporta()->getNombres(),
            );
        }


        return new \Symfony\Component\HttpFoundation\Response(json_encode($json), 200);
    }


    public function cancelarOrdenAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $ordenTrabajo = $em->getRepository('BusetaTallerBundle:OrdenTrabajo')->find($id);

        if (!$ordenTrabajo) {
            throw $this->createNotFoundException('Unable to find OrdenTrabajo entity.');
        }

        $report = $ordenTrabajo->getDiagnostico()->getReporte();

        //Se llama al EventDispatcher
        $eventDispatcher = $this->get('event_dispatcher');

        //Crear Eventos para el EventDispatcher

        $evento = new FilterReporteEvent($report);
        $evento->setReporte($report);

        $ordenTrabajoEvent = new FilterOrdenTrabajoEvent($ordenTrabajo);
        $ordenTrabajoEvent->setOrden($ordenTrabajo);


        //Lanzo los Eventos donde se pasa la solicitud que esta en pendiente a completada
        // y el evento que pasa la orden de trabajo a completada


        $eventDispatcher->dispatch(ReporteEvents::CAMBIAR_ESTADO_COMPLETADO, $evento);
        $eventDispatcher->dispatch(OrdenTrabajoEvents::CAMBIAR_ESTADO_CERRADO, $ordenTrabajoEvent);
        $eventDispatcher->dispatch(OrdenTrabajoEvents::CAMBIAR_CANCELADO, $ordenTrabajoEvent);

        return $this->redirect($this->generateUrl('ordentrabajo'));
    }

    public function procesarOrdenAction(OrdenTrabajo $ordenTrabajo)
    {
        $em = $this->getDoctrine()->getManager();

        //Se llama al EventDispatcher
        $eventDispatcher = $this->get('event_dispatcher');

        if ($ordenTrabajo->getDiagnostico() !== null && $ordenTrabajo->getDiagnostico()->getReporte() !== null) {
            $report = $ordenTrabajo->getDiagnostico()->getReporte();
        } else {
            $report = null;
        }

        $necesidadProductos = $em->getRepository('BusetaTallerBundle:OrdenNecesidadProducto')->findByOrdentrabajo($ordenTrabajo);
        if(count($necesidadProductos) > 0) {
            $salidaBodega = new SalidaBodega();
            $salidaBodega->setCentroCosto($ordenTrabajo->getAutobus());
            $salidaBodega->setTipoOT($ordenTrabajo->getPrioridad());
            $salidaBodega->setOrdenTrabajo($ordenTrabajo);
            $salidaBodega->setFecha(new \DateTime());
            $salidaBodega->setControlEntregaMaterial("Entrega por orden " + $ordenTrabajo->getNumero());
            $salidaBodega->setResponsable($ordenTrabajo->getRealizadaPor());
            foreach ($necesidadProductos as $necesidadProducto) {
                $salidaBodegaProducto = new SalidaBodegaProducto();
                $salidaBodegaProducto->setProducto($necesidadProducto->getProducto());
                $salidaBodegaProducto->setCantidad($necesidadProducto->getCantidad());
                $salidaBodegaProducto->setSeriales($necesidadProducto->getSeriales());
                $salidaBodegaProducto->setSalida($salidaBodega);
                $necesidadProducto->setSalidaBodegaProducto($salidaBodegaProducto);
                $em->persist($salidaBodegaProducto);
                $em->persist($necesidadProducto);
            }
            $em->persist($salidaBodega);
        }

        if ($report === null) {
            if($ordenTrabajo->getEstado()== 'DRAFT') {
                $ordenTrabajoEvent = new FilterOrdenTrabajoEvent($ordenTrabajo);
                $ordenTrabajoEvent->setOrden($ordenTrabajo);
                $eventDispatcher->dispatch(OrdenTrabajoEvents::CAMBIAR_ESTADO_PROCESADO, $ordenTrabajoEvent);
            } else {
                $ordenTrabajoEvent = new FilterOrdenTrabajoEvent($ordenTrabajo);
                $ordenTrabajoEvent->setOrden($ordenTrabajo);
                $eventDispatcher->dispatch(OrdenTrabajoEvents::CAMBIAR_ESTADO_COMPLETADO, $ordenTrabajoEvent);
            }
        } else {
            if($ordenTrabajo->getEstado()== 'BO') {
                //Crear Eventos para el EventDispatcher
                $reporteEvent = new FilterReporteEvent($report);
                $reporteEvent->setReporte($report);

                $ordenTrabajoEvent = new FilterOrdenTrabajoEvent($ordenTrabajo);
                $ordenTrabajoEvent->setOrden($ordenTrabajo);

                $eventDispatcher->dispatch(OrdenTrabajoEvents::CAMBIAR_ESTADO_PROCESADO, $ordenTrabajoEvent);
            } else {
                //Crear Eventos para el EventDispatcher
                $reporteEvent = new FilterReporteEvent($report);
                $reporteEvent->setReporte($report);

                $ordenTrabajoEvent = new FilterOrdenTrabajoEvent($ordenTrabajo);
                $ordenTrabajoEvent->setOrden($ordenTrabajo);

                $eventDispatcher->dispatch(ReporteEvents::CAMBIAR_ESTADO_COMPLETADO, $reporteEvent);
                $eventDispatcher->dispatch(OrdenTrabajoEvents::CAMBIAR_ESTADO_COMPLETADO, $ordenTrabajoEvent);
            }
        }

        return $this->redirect($this->generateUrl('ordentrabajo'));
    }

    public function cambiarEstadoOrdenAction(OrdenTrabajo $ordenTrabajo)
    {
        $em = $this->getDoctrine()->getManager();
        $eventDispatcher = $this->get('event_dispatcher');
        if ($ordenTrabajo->getDiagnostico() !== null && $ordenTrabajo->getDiagnostico()->getReporte() !== null) {
            $report = $ordenTrabajo->getDiagnostico()->getReporte();
        } else {
            $report = null;
        }

        if($ordenTrabajo->getEstado() == 'DRAFT') {
            $necesidadProductos = $em->getRepository('BusetaTallerBundle:OrdenNecesidadProducto')->findByOrdentrabajo($ordenTrabajo);
            if(count($necesidadProductos) > 0) {
                $salidaBodega = new SalidaBodega();
                $salidaBodega->setCentroCosto($ordenTrabajo->getAutobus());
                $salidaBodega->setTipoOT($ordenTrabajo->getPrioridad());
                $salidaBodega->setOrdenTrabajo($ordenTrabajo);
                $salidaBodega->setFecha(new \DateTime());
                $salidaBodega->setControlEntregaMaterial("Entrega por orden " + $ordenTrabajo->getNumero());
                $salidaBodega->setResponsable($ordenTrabajo->getRealizadaPor());
                foreach ($necesidadProductos as $necesidadProducto) {
                    $salidaBodegaProducto = new SalidaBodegaProducto();
                    $salidaBodegaProducto->setProducto($necesidadProducto->getProducto());
                    $salidaBodegaProducto->setCantidad($necesidadProducto->getCantidad());
                    $salidaBodegaProducto->setSeriales($necesidadProducto->getSeriales());
                    $salidaBodegaProducto->setSalida($salidaBodega);
                    $necesidadProducto->setSalidaBodegaProducto($salidaBodegaProducto);
                    $em->persist($salidaBodegaProducto);
                    $em->persist($necesidadProducto);
                    $em->flush();
                }
                $em->persist($salidaBodega);
                $em->flush();
            }
            $ordenTrabajo->setEstado('PROCESS');
        } else if($ordenTrabajo->getEstado()== 'PROCESS') {
            $ordenTrabajo->setEstado('POSTED');
        } else if($ordenTrabajo->getEstado()== 'POSTED') {
            $ordenTrabajo->setEstado('COMPLETE');
            if ($report != null) {
                $reporteEvent = new FilterReporteEvent($report);
                $reporteEvent->setReporte($report);

                $ordenTrabajoEvent = new FilterOrdenTrabajoEvent($ordenTrabajo);
                $ordenTrabajoEvent->setOrden($ordenTrabajo);

                $eventDispatcher->dispatch(ReporteEvents::CAMBIAR_ESTADO_COMPLETADO, $reporteEvent);
            }
        }

        $em->persist($ordenTrabajo);
        $em->flush();

        return $this->redirect($this->generateUrl('ordentrabajo'));
    }

    public function completarOrdenAction(OrdenTrabajo $ordenTrabajo)
    {
        $em = $this->getDoctrine()->getManager();

        try {
            if ($ordenTrabajo->getDiagnostico() !== null && $ordenTrabajo->getDiagnostico()->getReporte() !== null) {
                $report = $ordenTrabajo->getDiagnostico()->getReporte();
                $report->setEstado('CO');
            }

            $ordenTrabajo->setEstado('CO');

            $em->persist($ordenTrabajo);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Se ha completado la Orden de Trabajo correctamente.');

            return $this->redirect($this->generateUrl('ordentrabajo'));
        } catch (\Exception $e) {
            $this->get('logger')->critical(sprintf(
                'Ha ocurrido un error al completar la Orden de Trabajo. Detalles: {message: %s, file: %s, line: %s}',
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));

            $this->get('session')->getFlashBag()->add('danger', 'Ha ocurrido un error al completar la Orden de Trabajo.');
        }

        return $this->redirect($this->generateUrl('ordentrabajo_show', array('id' => $ordenTrabajo->getId())));
    }

}
