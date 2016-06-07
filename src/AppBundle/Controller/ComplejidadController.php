<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Complejidad;
use AppBundle\Form\ComplejidadType;

/**
 * Complejidad controller.
 *
 * @Route("/admin/complejidad")
 */
class ComplejidadController extends Controller
{

    /**
     * Lists all Complejidad entities.
     *
     * @Route("/", name="complejidad")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Complejidad')->findAll();

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
     * Creates a new Complejidad entity.
     *
     * @Route("/", name="complejidad_create")
     * @Method("POST")
     * @Template("AppBundle:Complejidad:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Complejidad();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('mensaje', 'La complejidad ha sido creada correctamente');
            return $this->redirect($this->generateUrl('complejidad_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Complejidad entity.
     *
     * @param Complejidad $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Complejidad $entity)
    {
        $form = $this->createForm(new ComplejidadType(), $entity, array(
            'action' => $this->generateUrl('complejidad_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Complejidad entity.
     *
     * @Route("/new", name="complejidad_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Complejidad();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Complejidad entity.
     *
     * @Route("/{id}", name="complejidad_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Complejidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Complejidad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Complejidad entity.
     *
     * @Route("/{id}/edit", name="complejidad_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Complejidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Complejidad entity.');
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
    * Creates a form to edit a Complejidad entity.
    *
    * @param Complejidad $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Complejidad $entity)
    {
        $form = $this->createForm(new ComplejidadType(), $entity, array(
            'action' => $this->generateUrl('complejidad_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Complejidad entity.
     *
     * @Route("/{id}", name="complejidad_update")
     * @Method("PUT")
     * @Template("AppBundle:Complejidad:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Complejidad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Complejidad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->addFlash('mensaje', 'La complejidad ha sido actualizada correctamente');
            return $this->redirect($this->generateUrl('complejidad_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Complejidad entity.
     *
     * @Route("/{id}", name="complejidad_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Complejidad')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Complejidad entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->addFlash('mensaje', 'La eliminación fue realizada correctamente');
        }

        return $this->redirect($this->generateUrl('complejidad'));
    }

    /**
     * Creates a form to delete a Complejidad entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('complejidad_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
