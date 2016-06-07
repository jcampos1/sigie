<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Unidad;
use AppBundle\Form\UnidadType;

/**
 * Unidad controller.
 *
 * @Route("/admin/unidad")
 */
class UnidadController extends Controller
{

    /**
     * Lists all Unidad entities.
     *
     * @Route("/", name="unidad")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Unidad')->findAll();

        // Añadimos el paginador (En este caso el parámetro "1" es la página actual, y parámetro "10" es el número de páginas a mostrar)
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->getInt('page', 1),10
        );

        return array(
            'pagination' => $pagination,
        );
    }
    /**
     * Creates a new Unidad entity.
     *
     * @Route("/", name="unidad_create")
     * @Method("POST")
     * @Template("AppBundle:Unidad:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Unidad();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('mensaje', 'La unidad ha sido creada correctamente');

            return $this->redirect($this->generateUrl('unidad_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Unidad entity.
     *
     * @param Unidad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Unidad $entity)
    {
        $form = $this->createForm(new UnidadType(), $entity, array(
            'action' => $this->generateUrl('unidad_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Unidad entity.
     *
     * @Route("/new", name="unidad_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Unidad();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Unidad entity.
     *
     * @Route("/{id}", name="unidad_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Unidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encuentra la unidad.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Unidad entity.
     *
     * @Route("/{id}/edit", name="unidad_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Unidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encuentra la unidad.');
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
    * Creates a form to edit a Unidad entity.
    *
    * @param Unidad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Unidad $entity)
    {
        $form = $this->createForm(new UnidadType(), $entity, array(
            'action' => $this->generateUrl('unidad_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Unidad entity.
     *
     * @Route("/{id}", name="unidad_update")
     * @Method("PUT")
     * @Template("AppBundle:Unidad:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Unidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encuentra la unidad.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->addFlash('mensaje', 'La unidad ha sido actualizada correctamente');
            return $this->redirect($this->generateUrl('unidad_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Unidad entity.
     *
     * @Route("/{id}", name="unidad_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Unidad')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se encuentra la unidad.');
            }

            $em->remove($entity);
            $em->flush();
            $this->addFlash('mensaje', 'La eliminación fue realizada correctamente');
        }

        return $this->redirect($this->generateUrl('unidad'));
    }

    /**
     * Creates a form to delete a Unidad entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('unidad_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
