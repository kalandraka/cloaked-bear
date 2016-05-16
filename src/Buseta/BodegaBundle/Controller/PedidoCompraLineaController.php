<?php

namespace Buseta\BodegaBundle\Controller;

use Buseta\BodegaBundle\Entity\PedidoCompra;
use Buseta\BodegaBundle\Entity\Producto;
use Buseta\NomencladorBundle\Entity\UOM;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Buseta\BodegaBundle\Entity\PedidoCompraLinea;
use Buseta\BodegaBundle\Form\Type\PedidoCompraLineaType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityRepository;

/**
 * Class PedidoCompraLineaController
 * @package Buseta\BodegaBundle\Controller
 *
 * @Route("/pedidocompra_linea")
 */
class PedidoCompraLineaController extends Controller
{
    /**
     * @param PedidoCompra $pedidocompra
     * @return Response
     *
     * @Route("/list/{pedidocompra}", name="pedidocompra_lineas_list", methods={"GET"}, options={"expose":true})
     * @ParamConverter("pedidocompra", options={"mapping":{"pedidocompra":"id"}})
     */
    public function listAction(PedidoCompra $pedidocompra, Request $request)
    {
        $entities = $this->get('doctrine.orm.entity_manager')
            ->getRepository('BusetaBodegaBundle:PedidoCompraLinea')
            ->findAllByPedidoCompraId($pedidocompra->getId());

        $entities = $this->get('knp_paginator')
            ->paginate(
                $entities,
                $request->query->get('page', 1),
                10
            );

        return $this->render('@BusetaBodega/PedidoCompra/Linea/list_template.html.twig', array(
            'entities' => $entities,
            'pedidocompra' => $pedidocompra,
        ));
    }

    /**
     * @param PedidoCompra $pedidocompra
     * @param Request $request
     *
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/new/{pedidocompra}", name="pedidocompra_lineas_new_modal", options={"expose":true})
     * @Method({"GET","POST"})
     * @ParamConverter("pedidocompra", options={"mapping":{"pedidocompra":"id"}})
     */
    public function newModalAction(PedidoCompra $pedidocompra, Request $request)
    {
        $trans = $this->get('translator');
        $handler = $this->get('buseta_pedidocompra.linea.handler');
        $handler->bindData($pedidocompra);

        $handler->setRequest($request);

        if ($handler->handle()) {
            $renderView = $this->renderView('@BusetaBodega/PedidoCompra/Linea/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.create.success', array(), 'BusetaBodegaBundle')
            ), 201);
        }

        if ($handler->getError()) {
            $renderView = $this->renderView('@BusetaBodega/PedidoCompra/Linea/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.create.error.%key%', array('key' => 'Línea'), 'BusetaBodegaBundle')
            ), 500);
        }

        $renderView = $this->renderView('@BusetaBodega/PedidoCompra/Linea/modal_form.html.twig', array(
            'form' => $handler->getForm()->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * @param PedidoCompra $pedidocompra
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     *
     * @Route("/{id}/edit/{pedidocompra}", name="pedidocompra_lineas_edit_modal", options={"expose":true})
     * @Method({"GET","PUT"})
     * @ParamConverter("pedidocompra", options={"mapping":{"pedidocompra":"id"}})
     */
    public function editModalAction(PedidoCompraLinea $pedidoCompraLinea, PedidoCompra $pedidocompra, Request $request)
    {
        $trans = $this->get('translator');
        $handler = $this->get('buseta_pedidocompra.linea.handler');
        $handler->bindData($pedidocompra, $pedidoCompraLinea);

        $handler->setRequest($request);

        if ($handler->handle()) {
            $renderView = $this->renderView('@BusetaBodega/PedidoCompra/Linea/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.create.success', array(), 'BusetaBodegaBundle')
            ), 201);
        }

        if ($handler->getError()) {
            $renderView = $this->renderView('@BusetaBodega/PedidoCompra/Linea/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.create.error.%key%', array('key' => 'Línea'), 'BusetaBodegaBundle')
            ), 500);
        }

        $form = $handler->getForm();
        $id = $pedidoCompraLinea->getProducto()->getId();
        $form->add('producto', 'entity', array(
            'class' => 'BusetaBodegaBundle:Producto',
            'placeholder' => '---Filtrar por código o nombre---',
            'required' => true,
            'attr' => array(
                'class' => 'form-control',
            ),
            'query_builder' => function (EntityRepository $repository) use ($id) {
                $qb = $repository->createQueryBuilder('p');
                $qb->Where($qb->expr()->eq(':id', 'p.id'))
                    ->setParameter('id', $id);

                return $qb;
            },
        ));
        $renderView = $this->renderView(
            '@BusetaBodega/PedidoCompra/Linea/modal_form.html.twig',
            array(
                'form' => $form->createView(),
            )
        );

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * Lists all PedidoCompraLinea entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BusetaBodegaBundle:PedidoCompraLinea')->findAll();

        return $this->render('BusetaBodegaBundle:PedidoCompraLinea:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Creates a new PedidoCompraLinea entity.
     */
    public function createAction(Request $request)
    {
        $entity = new PedidoCompraLinea();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('linea_show', array('id' => $entity->getId())));
        }

        return $this->render('BusetaBodegaBundle:PedidoCompraLinea:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    public function create_compraAction(Request $request)
    {
        $entity = new PedidoCompraLinea();
        $form = $this->createCreateCompraForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
        }
    }

    /**
     * Creates a form to create a PedidoCompraLinea entity.
     *
     * @param PedidoCompraLinea $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PedidoCompraLinea $entity)
    {
        $form = $this->createForm(new PedidoCompraLineaType(), $entity, array(
            'action' => $this->generateUrl('linea_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Creates a form to create a PedidoCompraLinea entity.
     *
     * @param PedidoCompraLinea $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateCompraForm(PedidoCompraLinea $entity)
    {
        $form = $this->createForm(new PedidoCompraLineaType(), $entity, array(
            'action' => $this->generateUrl('linea_compra_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new PedidoCompraLinea entity.
     */
    public function newAction()
    {
        $entity = new PedidoCompraLinea();
        $form = $this->createCreateForm($entity);

        return $this->render('BusetaBodegaBundle:PedidoCompraLinea:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PedidoCompraLinea entity.
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:PedidoCompraLinea')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PedidoCompraLinea entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BusetaBodegaBundle:PedidoCompraLinea:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),));
    }

    /**
     * Displays a form to edit an existing PedidoCompraLinea entity.
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:PedidoCompraLinea')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PedidoCompraLinea entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BusetaBodegaBundle:PedidoCompraLinea:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a PedidoCompraLinea entity.
     *
     * @param PedidoCompraLinea $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PedidoCompraLinea $entity)
    {
        $form = $this->createForm(new PedidoCompraLineaType(), $entity, array(
            'action' => $this->generateUrl('linea_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing PedidoCompraLinea entity.
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:PedidoCompraLinea')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PedidoCompraLinea entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('linea_edit', array('id' => $id)));
        }

        return $this->render('BusetaBodegaBundle:PedidoCompraLinea:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a PedidoCompraLinea entity.
     * @Route("/{id}/delete", name="pedidocompra_lineas_delete", options={"expose": true})
     * @Method({"DELETE", "GET"})
     */
    public function deleteAction(Request $request, PedidoCompraLinea $pedidoCompraLinea)
    {
        $form = $this->createDeleteForm($pedidoCompraLinea->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            try {
                $em->remove($pedidoCompraLinea);
                $em->flush();

                $message = $this->get('translator')->trans('messages.delete.success', array(), 'BusetaBodegaBundle');
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(array('message' => $message), 202);
                }
            } catch (\Exception $e) {
                $message = $this->get('translator')->trans('messages.delete.error.%key%', array('key' => 'Linea Registro de Compra'), 'BusetaBodegaBundle');
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(array('message' => $message), 500);
                }
            }
        }

        if ($request->isXmlHttpRequest()) {
            $view = $this->renderView('@BusetaBodega/PedidoCompra/Linea/delete_modal.html.twig', array(
                'entity' => $pedidoCompraLinea,
                'form' => $form->createView(),
            ));

            return new JsonResponse(array(
                'view' => $view
            ), 200);
        }

        return $this->redirect($this->generateUrl('linea'));
    }

    /**
     * Creates a form to delete a PedidoCompraLinea entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pedidocompra_lineas_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Route("/{id}/get_uom_product", name="pedidocompra_get_uom_by_producto", options={"expose": true})
     * @Method("GET")
     *
     * @param Producto $producto
     * @return JsonResponse
     */
    public function getUOMByProductoAction(Producto $producto)
    {
        $uom = $producto->getUom();
        if (!$uom) {
            return new JsonResponse(array('error' => 'El producto no tiene definido la unidad de medida activa.'), 404);
        }

        $value = array(
            'id' => $uom->getId(),
            'valor' => $uom->getValor(),
        );

        return new JsonResponse($value);
    }
}
