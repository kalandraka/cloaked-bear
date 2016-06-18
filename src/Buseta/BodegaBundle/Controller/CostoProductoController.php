<?php

namespace Buseta\BodegaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Buseta\BodegaBundle\Entity\CostoProducto;
use Buseta\BodegaBundle\Form\Type\CostoProductoType;
use Buseta\BodegaBundle\Entity\Producto;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;


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
                10
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
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
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
     *
     * @Route("/{id}/delete", name="producto_costo_delete", options={"expose": true})
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, CostoProducto $costoProducto)
    {
        $form = $this->createDeleteForm($costoProducto->getId());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');

            try {
                $em->remove($costoProducto);
                $em->flush();

                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => $this->get('translator')->trans('messages.delete.success', array(), 'BusetaBodegaBundle'),
                    ), 202);
                }
            } catch (\Exception $e) {
                $error = sprintf('Ha ocurrido un error eliminando Costo de Producto. Detalles: %s', $e->getMessage());
                $this->get('logger')->addCritical($error);

                if($request->isXmlHttpRequest()) {
                    return new JsonResponse(array(
                        'message' => 'Ha ocurrido un error eliminando Costo de Producto.',
                    ), 500);
                }
            }
        }

        $view = $this->renderView('@BusetaBodega/Producto/Costo/delete_modal.html.twig', array(
            'form' => $form->createView()
        ));

        return new JsonResponse(array('view' => $view));
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
            ->setAction($this->generateUrl('producto_costo_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }


    /**
     * @Route("/update/{id}/from-registro-compra", name="producto_costo_update_from_registro_compra", options={"expose": true})
     * @Method("PUT")
     */
    public function updateCostoFromRegistroCompraAction(Request $request, CostoProducto $costo)
    {
        if(!$request->isXmlHttpRequest()) {
            throw new \Exception('Debe ser una petición Ajax.');
        }

        $newValue = $request->request->get('costo', -1);
        if ($newValue !== -1) {
            $em = $this->get('doctrine.orm.entity_manager');

            try {
                $costo->setCosto($newValue);

                $em->persist($costo);
                $em->flush();

                return new Response('ok', 202);
            } catch (\Exception $e) {
                $error = sprintf('Ha ocurrido un error actualizando el costo con id "%d" del producto "%s". Detalles: %s',
                    $costo->getId(),
                    $costo->getProducto()->getCodigo(),
                    $e->getMessage()
                );
                $this->get('logger')->addCritical($error);

                return new Response('Ha ocurrido un error.', 500);
            }
        }

        return new Response('ok', 200);
    }

    /**
     *
     * @Route("/modal_list/{producto}", name="producto_costos_modal_list", methods={"GET", "POST"}, options={"expose":true})
     * @ParamConverter("producto", options={"mapping":{"producto":"id"}})
     */
    public function modalListAction(Producto $producto, Request $request)
    {
        $entities = $this->get('doctrine.orm.entity_manager')
            ->getRepository('BusetaBodegaBundle:CostoProducto')
            ->findAllByProductoId($producto->getId());

        $entities = $this->get('knp_paginator')
            ->paginate(
                $entities,
                $request->query->get('page', 1),
                10
            );

        $renderView =  $this->renderView('@BusetaBodega/Producto/Costo/modal_costo.html.twig', array(
            'entities' => $entities,
            'producto' => $producto,
        ));

        return new JsonResponse(array('view' => $renderView), 202);
    }
}
