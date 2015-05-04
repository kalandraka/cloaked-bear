<?php

namespace Buseta\BodegaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Buseta\BodegaBundle\Entity\CostoProducto;
use Buseta\BodegaBundle\Form\Type\CostoProductoType;
use Buseta\BodegaBundle\Entity\Producto;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * CostoProducto controller.
 *
 * @Route("/costoproducto")
 */
class CostoProductoController extends Controller
{
    /**
     * @param Producto $producto
     * @return Response
     *
     * @Route("/list/{producto}", name="producto_costos_list", methods={"GET"}, options={"expose":true})
     * @ParamConverter("producto", options={"mapping":{"producto":"id"}})
     */
    public function listAction(Producto $producto, Request $request)
    {
        $entities = $this->get('doctrine.orm.entity_manager')
            ->getRepository('BusetaBodegaBundle:CostoProducto')
            ->findAllByProductoId($producto->getId());

        $entities = $this->get('knp_paginator')
            ->paginate(
                $entities,
                $request->query->get('page', 1),
                5
            );

        return $this->render('@BusetaBodega/Producto/Costo/list_template.html.twig', array(
            'entities' => $entities,
            'producto' => $producto,
        ));
    }

    /**
     * @param Producto $producto
     * @param Request $request
     *
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/new/modal/{producto}", name="producto_costos_new_modal", methods={"GET","POST"}, options={"expose":true})
     * @ParamConverter("producto", options={"mapping":{"producto":"id"}})
     */
    public function newModalAction(Producto $producto, Request $request)
    {
        $trans = $this->get('translator');
        $handler = $this->get('buseta_producto.costo.handler');
        $handler->bindData($producto);

        $handler->setRequest($request);

        if($handler->handle()) {
            $renderView = $this->renderView('@BusetaBodega/Producto/Costo/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.create.success', array(), 'BusetaBodegaBundle')
            ), 201);
        }

        if($handler->getError()) {
            $renderView = $this->renderView('@BusetaBodega/Producto/Costo/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.create.error.%key%', array('key' => 'Costo'), 'BusetaBodegaBundle')
            ), 500);
        }

        $renderView = $this->renderView('@BusetaBodega/Producto/Costo/modal_form.html.twig', array(
            'form' => $handler->getForm()->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * @param Producto $producto
     * @param Request $request
     *
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/edit/{id}/modal/{producto}", name="producto_costos_edit_modal", methods={"GET","PUT"}, options={"expose":true})
     * @ParamConverter("producto", options={"mapping":{"producto":"id"}})
     */
    public function editModalAction(CostoProducto $costo, Producto $producto, Request $request)
    {
        $trans = $this->get('translator');
        $handler = $this->get('buseta_producto.costo.handler');
        $handler->bindData($producto, $costo);

        $handler->setRequest($request);

        if($handler->handle()) {
            $renderView = $this->renderView('@BusetaBodega/Producto/Costo/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.update.success', array(), 'BusetaBodegaBundle')
            ), 202);
        }

        if($handler->getError()) {
            $renderView = $this->renderView('@BusetaBodega/Producto/Costo/modal_form.html.twig', array(
                'form' => $handler->getForm()->createView(),
            ));

            return new JsonResponse(array(
                'view' => $renderView,
                'message' => $trans->trans('messages.update.error.%key%', array('key' => 'Costo'), 'BusetaBodegaBundle')
            ), 500);
        }

        $renderView = $this->renderView('@BusetaBodega/Producto/Costo/modal_form.html.twig', array(
            'form' => $handler->getForm()->createView(),
        ));

        return new JsonResponse(array('view' => $renderView));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @deprecated
     */
    public function comprobarCostoAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new \Symfony\Component\HttpFoundation\Response('Acceso Denegado', 403);
        }

        if (!$request->isXmlHttpRequest()) {
            return new \Symfony\Component\HttpFoundation\Response('No es una petición Ajax', 500);
        }

        $em = $this->getDoctrine()->getManager();

        $error = "Sin errores";

        if ($request->query->get('nombre')) {
            $nombre = $request->query->get('nombre');
        } else {
            $error = "error";
        }

        if ($request->query->get('codigo')) {
            $codigo = $request->query->get('codigo');
        } else {
            $error = "error";
        }

        if ($request->query->get('uom')) {
            $uom = $request->query->get('uom');
        } else {
            $error = "error";
        }

        if ($request->query->get('bodega')) {
            $bodega = $request->query->get('bodega');
        } else {
            $error = "error";
        }

        if ($request->query->get('minimo_bodega') != null) {
            $minimo_bodega = $request->query->get('minimo_bodega');
        } else {
            $error = "error";
        }

        if ($request->query->get('maximo_bodega') != null) {
            $maximo_bodega = $request->query->get('maximo_bodega');
        } else {
            $error = "error";
        }

        if ($request->query->get('grupos')) {
            $grupos = $request->query->get('grupos');
        } else {
            $error = "error";
        }

        if ($request->query->get('subgrupos')) {
            $subgrupos = $request->query->get('subgrupos');
        } else {
            $error = "error";
        }

        if ($request->query->get('condicion')) {
            $condicion = $request->query->get('condicion');
        } else {
            $error = "error";
        }

        if ($request->query->get('activo')) {
            $activo = $request->query->get('activo');
        } else {
            $error = "error";
        }

        $json = array(
            'error' => $error,
        );

        return new \Symfony\Component\HttpFoundation\Response(json_encode($json), 200);
    }

    /**
     * Edits an existing CostoProducto entity.
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BusetaBodegaBundle:CostoProducto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CostoProducto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('linea_edit', array('id' => $id)));
        }

        return $this->render('BusetaBodegaBundle:CostoProducto:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a CostoProducto entity.
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BusetaBodegaBundle:CostoProducto')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CostoProducto entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('linea'));
    }

    /**
     * Creates a form to delete a CostoProducto entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('linea_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
