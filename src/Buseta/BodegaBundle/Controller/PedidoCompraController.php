<?php

namespace Buseta\BodegaBundle\Controller;

use Buseta\BodegaBundle\BusetaBodegaDocumentStatus;
use Buseta\BodegaBundle\Entity\Albaran;
use Buseta\BodegaBundle\Entity\AlbaranLinea;
use Buseta\BodegaBundle\Entity\PedidoCompraLinea;
use Buseta\BodegaBundle\Entity\Proveedor;
use Buseta\BodegaBundle\Entity\Tercero;
use Buseta\BodegaBundle\Form\Filter\PedidoCompraFilter;
use Buseta\BodegaBundle\Form\Model\AlbaranModel;
use Buseta\BodegaBundle\Form\Model\PedidoCompraFilterModel;
use Buseta\BodegaBundle\Form\Model\PedidoCompraModel;
use Buseta\BodegaBundle\Form\Type\PedidoCompraLineaType;
use Buseta\NomencladorBundle\Entity\Moneda;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Buseta\BodegaBundle\Entity\PedidoCompra;

use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolation;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
/**
 * PedidoCompra controller.
 *
 * @Route("/pedidocompra")
 * @Breadcrumb(title="Inicio", routeName="core_homepage")
 * @Breadcrumb(title="Módulo de Bodegas", routeName="bodega_principal")
 */
class PedidoCompraController extends Controller
{
    /**
     * Lists all PedidoCompra entities.
     *
     * @Route("/", name="pedidocompra")
     * @Method("GET")
     * @Breadcrumb(title="Registros de Compras", routeName="pedidocompra")
     */
    public function indexAction(Request $request)
    {
        $filter = new PedidoCompraFilterModel();

        $form = $this->createForm(
            new PedidoCompraFilter(),
            $filter,
            array(
                'action' => $this->generateUrl('pedidocompra'),
            )
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBodegaBundle:PedidoCompra')->filter($filter);
        } else {
            $entities = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BusetaBodegaBundle:PedidoCompra')->filter();
        }

        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
            $entities,
            $request->query->get('page', 1),
            10
        );

        return $this->render(
            'BusetaBodegaBundle:PedidoCompra:index.html.twig',
            array(
                'entities' => $entities,
                'filter_form' => $form->createView(),
            )
        );
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Route("/{id}/procesarRegistro", name="procesarRegistro")
     * @Method("GET")
     */
    public function procesarRegistroAction(PedidoCompra $pedidoCompra)
    {
        $manager = $this->get('buseta.bodega.pedidocompra.manager');
        if (true === $result = $manager->procesar($pedidoCompra)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                'Se ha procesado el Registro de Compra de forma correcta.'
            );

            return $this->redirect($this->generateUrl('pedidocompra_show', array('id' => $pedidoCompra->getId())));
        } else {
            $this->get('session')->getFlashBag()->add(
                'danger',
                'Ha ocurrido un error al procesar el Registro de Compra.'
            );

            return $this->redirect($this->generateUrl('pedidocompra_show', array('id' => $pedidoCompra->getId())));
        }
    }

    /**
     * @param PedidoCompra $pedidoCompra
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @internal param $id
     * @Route("/{id}/completarRegistro", name="completarRegistro")
     * @Method("GET")
     */
    public function completarRegistroAction(PedidoCompra $pedidoCompra)
    {
        $session = $this->get('session');
        $error = false;

        $manager = $this->get('buseta.bodega.pedidocompra.manager');

        if (true === $result = $manager->completar($pedidoCompra)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                'Se ha completado el Pedido Compra de forma correcta.'
            );
            if (!$error) {
                $albaranManager = $this->get('buseta.bodega.albaran.manager');

                $albaranModel = new AlbaranModel();
                $albaranModel->setAlmacen($pedidoCompra->getAlmacen());
                $albaranModel->setTercero($pedidoCompra->getTercero());
                $albaranModel->setPedidoCompra($pedidoCompra);

                foreach ($pedidoCompra->getPedidoCompraLineas() as $linea) {
                    /** @var \Buseta\BodegaBundle\Entity\PedidoCompraLinea $linea */
                    $albaranLinea = new AlbaranLinea();
                    $albaranLinea->setAlmacen($pedidoCompra->getAlmacen());
                    $albaranLinea->setProducto($linea->getProducto());
                    $albaranLinea->setCantidadMovida($linea->getCantidadPedido());
                    $albaranLinea->setUom($linea->getUom());

                    $albaranModel->addAlbaranLinea($albaranLinea);
                }

                //registro los datos del nuevo albarán que se crear al procesar el pedido
                if ($albaran = $albaranManager->crear($albaranModel)) {
                    $session->getFlashBag()->add(
                        'success',
                        sprintf(
                            'Se ha creado la Orden de Entrada "%s" para el Registro de Compra "%s".',
                            $albaran->getNumeroDocumento(),
                            $pedidoCompra->getNumeroDocumento()
                        )
                    );

                } else {
                    $session->getFlashBag()->add(
                        'danger',
                        sprintf(
                            'Ha ocurrido un error intentando crear la Orden de Entrada para Registro de Compra "%s".',
                            $pedidoCompra->getNumeroDocumento()
                        )
                    );

                }

                return $this->redirect($this->generateUrl('pedidocompra_show', array('id' => $pedidoCompra->getId())));

            } else {
                $this->get('session')->getFlashBag()->add(
                    'danger',
                    sprintf('Ha ocurrido un error al completar el Pedido Compra.')
                );

               }
            }

            return $this->redirect($this->generateUrl('pedidocompra_show', array('id' => $pedidoCompra->getId())));
    }

    /**
     * Creates a new PedidoCompra entity.
     *
     * @Route("/create", name="pedidocompra_create", options={"expose": true})
     * @Security("is_granted('create', 'Buseta\\BodegaBundle\\Entity\\PedidoCompra')")
     * @Method("POST")
     * @Breadcrumb(title="Crear Nuevo Registro de Compra", routeName="pedidocompra_create")
     */
    public function createAction(Request $request)
    {
        $pedidoCompraModel = new PedidoCompraModel();
        $form = $this->createCreateForm($pedidoCompraModel);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trans  = $this->get('translator');
            $logger = $this->get('logger');
            $pedidoCompraManager = $this->get('buseta.bodega.pedidocompra.manager');

            if ($pedidoCompra = $pedidoCompraManager->crear($pedidoCompraModel)) {
                // Creando nuevamente el formulario con los datos actualizados de la entidad
                $form = $this->createEditForm(new PedidoCompraModel($pedidoCompra));
                $renderView = $this->renderView('@BusetaBodega/PedidoCompra/form_template.html.twig', array(
                    'form'   => $form->createView(),
                ));

                return new JsonResponse(array(
                    'view' => $renderView,
                    'message' => $trans->trans('messages.create.success', array(), 'BusetaBodegaBundle')
                ), 201);
            } else {
                return new JsonResponse(array(
                    'message' => $trans->trans('messages.create.error.%key%', array('key' => 'Pedido Compra'), 'BusetaBodegaBundle')
                ), 500);
            }
        }

        $renderView = $this->renderView('@BusetaBodega/PedidoCompra/form_template.html.twig', array(
            'form'     => $form->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Creates a form to create a PedidoCompra entity.
     *
     * @param PedidoCompraModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PedidoCompraModel $entity)
    {
        $form = $this->createForm('bodega_pedido_compra', $entity, array(
            'action' => $this->generateUrl('pedidocompra_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new PedidoCompra entity.
     *
     * @Route("/new", name="pedidocompra_new")
     * @Security("is_granted('create', 'Buseta\\BodegaBundle\\Entity\\PedidoCompra')")
     * @Method("GET")
     * @Breadcrumb(title="Crear Nuevo Registro de Compra", routeName="pedidocompra_new")
     */
    public function newAction()
    {
        $form   = $this->createCreateForm(new PedidoCompraModel());

        $em = $this->get('doctrine.orm.entity_manager');
        $productos = $em->getRepository('BusetaBodegaBundle:Producto')
            ->createQueryBuilder('p')
            ->select('p,c')
            ->innerJoin('p.costoProducto', 'c')
            ->getQuery()
            ->getResult();

        $json = array();
        $costoSalida = 0;

        foreach ($productos as $p) {
            foreach ($p->getCostoProducto() as $costo) {
                if ($costo->getActivo()) {
                    $costoSalida = ($costo->getCosto());
                }
            }

            $json[$p->getId()] = array(
                'nombre' => $p->getNombre(),
                'precio_salida' => $costoSalida,
            );
        }

        return $this->render('@BusetaBodega/PedidoCompra/new.html.twig', array(
            'form'   => $form->createView(),
            'json'   => json_encode($json),
        ));
    }

    /**
     * Finds and displays a PedidoCompra entity.
     *
     * @Route("/{id}/show", name="pedidocompra_show")
     * @Security("is_granted('show', pedidocompra)")
     * @Method("GET")
     * @Breadcrumb(title="Ver Datos de Registro de Compra", routeName="pedidocompra_show", routeParameters={"id"})
     */
    public function showAction(PedidoCompra $pedidocompra)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:PedidoCompra')->find($pedidocompra);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PedidoCompra entity.');
        }

        $deleteForm = $this->createDeleteForm($pedidocompra);

        return $this->render('BusetaBodegaBundle:PedidoCompra:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing PedidoCompra entity.
     *
     * @Route("/{id}/edit", name="pedidocompra_edit", options={"expose": true})
     * @Security("is_granted('edit', pedidocompra)")
     * @Method("GET")
     * @Breadcrumb(title="Modificar Registro de Compra", routeName="pedidocompra_edit", routeParameters={"id"})
     */
    public function editAction(PedidoCompra $pedidocompra)
    {
        if ($pedidocompra->getEstadoDocumento() !== BusetaBodegaDocumentStatus::DOCUMENT_STATUS_DRAFT) {
            throw $this->createAccessDeniedException(
                'No se puede modificar el Registro de Compra, pues ya ha sido Procesado.'
            );
        }

        $editForm = $this->createEditForm(new PedidoCompraModel($pedidocompra));
        $deleteForm = $this->createDeleteForm($pedidocompra->getId());

        return $this->render('BusetaBodegaBundle:PedidoCompra:edit.html.twig', array(
            'entity'        => $pedidocompra,
            'edit_form'     => $editForm->createView(),
            'delete_form'   => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a PedidoCompra entity.
     *
     * @param PedidoCompraModel $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PedidoCompraModel $entity)
    {
        $form = $this->createForm('bodega_pedido_compra', $entity, array(
            'action' => $this->generateUrl('pedidocompra_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing PedidoCompra entity.
     *
     * @Route("/{id}/update", name="pedidocompra_update", options={"expose": true})
     * @Method({"POST", "PUT"})
     * @Breadcrumb(title="Modificar Registro de Compra", routeName="pedidocompra_update", routeParameters={"id"})
     */
    public function updateAction(Request $request, PedidoCompra $pedidocompra)
    {
        $pedidocompraModel = new PedidoCompraModel($pedidocompra);
        $editForm = $this->createEditForm($pedidocompraModel);

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em     = $this->get('doctrine.orm.entity_manager');
            $trans  = $this->get('translator');
            $logger = $this->get('logger');

            try {
                $pedidocompra->setModelData($pedidocompraModel);
                $em->flush();

                $editForm = $this->createEditForm(new PedidoCompraModel($pedidocompra));
                $renderView = $this->renderView('@BusetaBodega/PedidoCompra/form_template.html.twig', array(
                    'form'     => $editForm->createView(),
                ));

                return new JsonResponse(array(
                    'view' => $renderView,
                    'message' => $trans->trans('messages.update.success', array(), 'BusetaBodegaBundle')
                ), 202);
            } catch (\Exception $e) {
                $logger->addCritical(sprintf(
                    $trans->trans('messages.update.success', array(), 'BusetaBodegaBundle'). '. Detalles: %s',
                    $e->getMessage()
                ));

                new JsonResponse(array(
                    'message' => $trans->trans('messages.update.error.%key%', array('key' => 'Registro de Compra'), 'BusetaBodegaBundle')
                ), 500);
            }
        }

        $renderView = $this->renderView('@BusetaBodega/PedidoCompra/form_template.html.twig', array(
            'form'     => $editForm->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Deletes a PedidoCompra entity.
     *
     * @Route("/{id}/delete", name="pedidocompra_delete", options={"expose": true})
     * @Method({"DELETE", "GET", "POST"})
     */
    public function deleteAction(PedidoCompra $pedidocompra, Request $request)
    {
        $trans = $this->get('translator');
        $deleteForm = $this->createDeleteForm($pedidocompra->getId());

        $deleteForm->handleRequest($request);
        if($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            try {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->remove($pedidocompra);
                $em->flush();

                $message = $trans->trans('messages.delete.success', array(), 'BusetaTallerBundle');

                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $message,
                    ), 202);
                }
                else {
                    $this->get('session')->getFlashBag()->add('success', $message);
                }
            } catch (\Exception $e) {
                $message = $trans->trans('messages.delete.error.%key%', array('key' => 'Pedido Compra'), 'BusetaTallerBundle');
                $this->get('logger')->addCritical(sprintf($message.' Detalles: %s', $e->getMessage()));

                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $message,
                    ), 500);
                }
            }
        }

        $renderView =  $this->renderView('@BusetaBodega/PedidoCompra/delete_modal.html.twig', array(
            'entity' => $pedidocompra,
            'form' => $deleteForm->createView(),
        ));

        if($request->isXmlHttpRequest()) {
            return new JsonResponse(array('view' => $renderView));
        }
        return $this->redirect($this->generateUrl('pedidocompra'));
    }

    /**
     * Creates a form to delete a PedidoCompra entity by id.
     *
     * @param mixed $id The entity id
     *º
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pedidocompra_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @Route("/{id}/get_moneda_proveedor", name="pedidocompra_get_moneda_by_proveedor", options={"expose": true})
     * @Method("GET")
     *
     * @param Tercero $tercero
     * @return JsonResponse
     */
    public function getMonedaAction(Tercero $tercero)
    {
        if (!$tercero->getProveedor()) {
            return new JsonResponse(array('error' => 'No existe el proveedor seleccionado.'), 404);
        }

        $moneda = $tercero->getProveedor()->getMoneda();
        if (!$moneda) {
            return new JsonResponse(array('error' => 'El proveedor no tiene definido una moneda activa.'), 404);
        }

        $value = array(
            'id' => $moneda->getId(),
            'valor' => $moneda->getValor(),
            'simbolo' => $moneda->getSimbolo()
        );

        return new JsonResponse($value);
    }
}
