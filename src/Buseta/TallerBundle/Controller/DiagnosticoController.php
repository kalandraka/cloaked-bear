<?php

namespace Buseta\TallerBundle\Controller;

use Buseta\TallerBundle\Entity\OrdenTrabajo;
use Buseta\TallerBundle\Entity\TareaAdicional;
use Buseta\TallerBundle\Event\FilterDiagnosticoEvent;
use Buseta\TallerBundle\Event\FilterReporteEvent;
use Buseta\TallerBundle\Event\ReporteEvents;
use Buseta\TallerBundle\Event\DiagnosticoEvents;
use Buseta\TallerBundle\Entity\Diagnostico;
use Buseta\TallerBundle\Entity\TareaDiagnostico;
use Buseta\TallerBundle\Form\Model\DiagnosticoModel;
use Buseta\TallerBundle\Form\Model\OrdenTrabajoModel;
use Buseta\TallerBundle\Form\Type\ObservacionDiagnosticoType;
use Buseta\TallerBundle\Form\Type\TareaDiagnosticoType;
use Doctrine\ORM\AbstractQuery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Util\ClassUtils;
use Buseta\TallerBundle\Form\Type\DiagnosticoType;
use Buseta\TallerBundle\Form\Model\DiagnosticoFilterModel;
use Buseta\TallerBundle\Form\Filter\DiagnosticoFilter;
use Symfony\Component\HttpFoundation\Response;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
/**
 * Diagnostico controller.
 *
 * @Route("/diagnostico")
 *
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Taller", routeName="taller_principal")
 *
 */
class DiagnosticoController extends Controller
{

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function procesarDiagnosticoAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $diagnostico = $em->getRepository('BusetaTallerBundle:Diagnostico')->find($id);


        if (!$diagnostico) {
            throw $this->createNotFoundException('Unable to find Diagnostico entity.');
        }

        //Se llama al EventDispatcher
        $eventDispatcher = $this->get('event_dispatcher');

        //Crear Eventos para el EventDispatcher
        $evento = new FilterDiagnosticoEvent($diagnostico);
        $evento->setDiagnostico($diagnostico);

        //Lanzo los Evento donde se crea el diagnostico y
        //cambio el estado de la solicitud de Abierto a Pendiente
        $eventDispatcher->dispatch(DiagnosticoEvents::PROCESAR_DIAGNOSTICO, $evento);
        $eventDispatcher->dispatch(DiagnosticoEvents::CAMBIAR_ESTADO_PR, $evento);
        //$eventDispatcher->dispatch(  OrdenTrabajoEvents::CAMBIAR_ESTADO_ABIERTO , $evento );

        return $this->redirect($this->generateUrl('diagnostico'));
    }

    public function cancelarDiagnosticoAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $diagnostico = $em->getRepository('BusetaTallerBundle:Diagnostico')->find($id);

        if (!$diagnostico) {
            throw $this->createNotFoundException('Unable to find Diagnostico entity.');
        }

        $report = $diagnostico->getReporte();

        //Se llama al EventDispatcher
        $eventDispatcher = $this->get('event_dispatcher');

        //Crear Eventos para el EventDispatcher


        $diagEvent = new FilterDiagnosticoEvent($diagnostico);
        $diagEvent->setDiagnostico($diagnostico);


        //Lanzo los Eventos donde se pasa la solicitud que esta en pendiente a completada
        // y el evento que pasa la orden de trabajo a completada

        if (!($report === null)) {
            $evento = new FilterReporteEvent($report);
            $evento->setReporte($report);
            $eventDispatcher->dispatch(ReporteEvents::CAMBIAR_ESTADO_CANCELADO, $evento);
        }

        $eventDispatcher->dispatch(DiagnosticoEvents::CAMBIAR_ESTADO_CO, $diagEvent);
        $eventDispatcher->dispatch(DiagnosticoEvents::CAMBIAR_CANCELADO, $diagEvent);

        return $this->redirect($this->generateUrl('diagnostico'));
    }

    /**
     * Lists all Diagnostico entities.
     *
     * @Route("/diagnostico", name="diagnostico")
     *
     * @Breadcrumb(title="Listado de Diagnósticos", routeName="diagnostico")
     */
    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
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
            10
        );

        $resumentotal = $em->getRepository('BusetaTallerBundle:Diagnostico')->findTotalAtrasadasFilter($entities);

        return $this->render('BusetaTallerBundle:Diagnostico:index.html.twig', array(
            'entities' => $entities,
            'filter_form' => $form->createView(),
            'resumentotal' => $resumentotal
        ));
    }

    /**
     * Finds and displays a Diagnostico entity.
     *
     * @Security("is_granted('SHOW', diagnostico)")
     *
     * @Route("/{id}/show", name="diagnostico_show")
     *
     * @Breadcrumb(title="Ver Datos de Diagnóstico", routeName="diagnostico_show", routeParameters={"id"})
     */
    public function showAction(Diagnostico $diagnostico)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        if ($diagnostico->getReporte()) {
            $reportes = $em->getRepository('BusetaTallerBundle:Reporte')->find($diagnostico->getReporte()->getId());
        } else {
            $reportes = null;
        }

        return $this->render('BusetaTallerBundle:Diagnostico:show.html.twig', array(
            'entity' => $diagnostico,
            'reportes' => $reportes,
            'id' => $diagnostico->getId(),
        ));
    }

    /**
     * Displays a form to edit an existing Diagnostico entity.
     *
     * @Route("/{id}/edit", name="diagnostico_edit")
     *
     * @Breadcrumb(title="Modificar Diagnóstico", routeName="diagnostico_edit", routeParameters={"id"})
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaTallerBundle:Diagnostico')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Diagnostico entity.');
        }

        $editForm = $this->createEditForm(new DiagnosticoModel());
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BusetaTallerBundle:Diagnostico:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Creates a form to edit a Diagnostico entity.
     *
     * @param DiagnosticoModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(DiagnosticoModel $entity)
    {
        $form = $this->createForm(new DiagnosticoType(), $entity, array(
            'action' => $this->generateUrl('diagnostico_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Creates a form to delete a Diagnostico entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('diagnostico_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Deletes a TareaDiagnostico entity.
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BusetaTallerBundle:Diagnostico')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Diagnostico entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('diagnostico'));
    }

    /**
     * Edits an existing Usuario entity.
     *
     * @Route("/{id}/update", name="diagnostico_update")
     *
     * @Method({"POST", "PUT"})
     *
     * @Breadcrumb(title="Modificar Diagnóstico", routeName="diagnostico_update", routeParameters={"id"})
     */
    public function updateAction(Request $request, Diagnostico $diagnostico)
    {
        $em = $this->getDoctrine()->getManager();

        $deleteForm = $this->createDeleteForm($diagnostico->getId());

        $tareasD = array();

        foreach ($diagnostico->getTareaDiagnostico() as $tarea) {
            $tareasD[] = $tarea;
        }

        $editForm = $this->createEditForm($diagnostico);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            // filtra $originalTags para que contenga las etiquetas
            // que ya no están presentes
            foreach ($diagnostico->getTareaDiagnostico() as $tarea) {
                foreach ($tareasD as $key => $toDel) {
                    if ($toDel->getId() === $tarea->getId()) {
                        unset($tareasD[$key]);
                    }
                }
            }

            // Elimina la relación entre la etiqueta y la Tarea
            foreach ($tareasD as $tarea) {
                // Si deseas eliminar la etiqueta completamente, también lo puedes hacer
                $em->remove($tarea);
            }

            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Se han editado los datos satisfactoriamente.');

            return $this->redirect($this->generateUrl('diagnostico_edit', array('id' => $diagnostico->getId())));
        }

        return $this->render('BusetaTallerBundle:Diagnostico:edit.html.twig', array(
            'entity' => $diagnostico,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to create a new Diagnostico entity.
     *
     * @Security("is_granted('CREATE', 'Buseta\\TallerBundle\\Entity\\Diagnostico')")
     *
     * @Route("/new", name="diagnostico_new")
     *
     * @Breadcrumb(title="Crear Nuevo Diagnóstico", routeName="diagnostico_new")
     */
    public function newAction()
    {
        $entity = new Diagnostico();
        $observacion = $this->createForm(new ObservacionDiagnosticoType());
        $tareadiagno = $this->createForm(new TareaDiagnosticoType());

        $form = $this->createCreateForm(new DiagnosticoModel());

        return $this->render('BusetaTallerBundle:Diagnostico:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'observacion' => $observacion->createView(),
            'tareaDiagnostico' => $tareadiagno->createView(),
        ));
    }

    /**
     * Creates a form to create a Diagnostico entity.
     *
     * @param DiagnosticoModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DiagnosticoModel $entity)
    {
        $form = $this->createForm('buseta_tallerbundle_diagnostico', $entity, array(
            'action' => $this->generateUrl('diagnostico_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Creates a new Diagnostico entity.
     * @Route("/create", name="diagnostico_create")
     *
     * @Method("POST")
     *
     * @Security("is_granted('CREATE', 'Buseta\\TallerBundle\\Entity\\Diagnostico')")
     *
     * @Breadcrumb(title="Crear Nuevo Diagnóstico", routeName="diagnostico_create")
     */
    public function createAction(Request $request)
    {
        $diagnosticoModel = new DiagnosticoModel();
        $form = $this->createCreateForm($diagnosticoModel);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trans  = $this->get('translator');

            $diagnosticoManager = $this->get('buseta.taller.diagnostico.manager');

            if ($diagnostico = $diagnosticoManager->crear($diagnosticoModel)) {
                $this->get('session')->getFlashBag()->add('success',
                    $trans->trans(
                        'messages.create.success',
                        array(),
                        'BusetaTallerBundle'
                    )
                );

                return $this->redirect($this->generateUrl('diagnostico_show', array('id' => $diagnostico->getId())));
            } else {
                $this->get('session')->getFlashBag()->add('danger', 'Ha ocurrido un error al crear Diagnostico');
            }
        }

        return $this->render('@BusetaTaller/Diagnostico/new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Updated automatically select Autobus when change select Reporte
     *
     */
    public function select_reporte_autobusAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new Response('Acceso Denegado', 403);
        }

        if (!$request->isXmlHttpRequest()) {
            return new Response('No es una petición Ajax', 500);
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $reportes = $em->find('BusetaTallerBundle:Reporte', $request->query->get('reporte_id'));

        $numero = $reportes->getNumero();
        $medio = $reportes->getMedioReporte()->getValor();
        //$bus = $reportes->getAutobus()->getMatricula().'-'.$reportes->getAutobus()->getNumero();

        if ($reportes->getReporta()) {
            $reporta = $reportes->getReporta();
        } else {
            $reporta = '-';
        }

        if ($reportes->getEsUsuario()) {
            $esUsuario = 'Sí';
            $nombrePersona = $reportes->getNombrePersona();
            $emailPersona = $reportes->getEmailPersona();
            $telefonoPersona = $reportes->getTelefonoPersona();
        } else {
            $esUsuario = 'No';
            $nombrePersona = '-';
            $emailPersona = '-';
            $telefonoPersona = '-';
        }

        return new JsonResponse(array(
            'autobus' => $reportes->getAutobus()->getId(),
            //'bus' => $bus,
            'reporta' => $reporta,
            'numero' => $numero,
            'medio' => $medio,
            'esUsuario' => $esUsuario,
            'nombrePersona' => $nombrePersona,
            'emailPersona' => $emailPersona,
            'telefonoPersona' => $telefonoPersona,
        ), 200);
    }

    /**
     * Updated automatically select Autobus y DiagnosticadoPor when change select Diagnostico
     *
     */
    public function select_diagnostico_ordentrabajoAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new Response('Acceso Denegado', 403);
        }

        if (!$request->isXmlHttpRequest()) {
            return new Response('No es una petición Ajax', 500);
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $diagnostico_id = $request->query->get('diagnostico_id');

        $diagnostico = $em->getRepository('BusetaTallerBundle:Diagnostico')->findBy(array(
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

    /**
     * @param Diagnostico $diagnostico
     *
     * @return RedirectResponse
     * @internal param $id
     * @Route("/{id}/crearOrden", name="crearOrden")
     * @Method("GET")
     */
    public function crearOrdenAction(Diagnostico $diagnostico)
    {
        $em         = $this->getDoctrine()->getManager();
        $logger     = $this->get('logger');
        $session    = $this->get('session');
        $error      = false;

        try {
            $em->persist($diagnostico);
            $em->flush();
        } catch (\Exception $e) {
            $logger->addCritical(sprintf('Ha ocurrido un error actualizando el estado del documento. Detalles: %s', $e->getMessage()));
            $session->getFlashBag()->add('danger', 'Ha ocurrido un error actualizando el estado del documento.');

            $error = true;
        }

        if (!$error) {
            $ordenTrabajoManager = $this->get('buseta.taller.ordentrabajo.manager');

            $ordenTrabajoModel = new OrdenTrabajoModel();
            $ordenTrabajoModel->setAutobus($diagnostico->getAutobus());
            $ordenTrabajoModel->setDiagnostico($diagnostico);

            foreach ($diagnostico->getTareaDiagnostico() as $tareaDiagnostico) {
                /** @var \Buseta\TallerBundle\Entity\TareaDiagnostico $tareaDignostico */
                $tareasAdicionales = new TareaAdicional();
                $tareasAdicionales->setGrupo($tareasAdicionales->getGrupo());
                $tareasAdicionales->setSubgrupo($tareasAdicionales->getSubgrupo());

                $ordenTrabajoModel->addTareasAdicionales($tareasAdicionales);
            }

            //registro los datos del nueva OT que se crea al procesar el pedido
            if ($ordenTrabajo = $ordenTrabajoManager->crear($ordenTrabajoModel)) {
                $session->getFlashBag()->add('success', sprintf('Se ha creado la Orden de Trabajo "%s" para el Diagnóstico "%s".',
                    $ordenTrabajo->getNumero(),
                    $diagnostico->getNumero()
                ));
            } else {
                $session->getFlashBag()->add('danger', sprintf('Ha ocurrido un error intentando crear la Orden de Trabajo para Diagnóstico "%s".',
                    $diagnostico->getNumero()
                ));
            }
        }

        return $this->redirect($this->generateUrl('diagnostico_show', array('id' => $diagnostico->getId())));
    }

}
