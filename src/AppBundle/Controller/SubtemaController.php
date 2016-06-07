<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Subtema;
use AppBundle\Form\SubtemaType;

/**
 * Subtema controller.
 *
 * @Route("/admin/subtema")
 */
class SubtemaController extends Controller
{

    /**
     * Lists all Subtema entities.
     *
     * @Route("/", name="subtema")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Subtema');
 
        $query = $repository->createQueryBuilder('s')
            ->orderBy('s.unidad', 'ASC')
            ->getQuery();
         
        $entities = $query->getResult();

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
     * Creates a new Subtema entity.
     *
     * @Route("/", name="subtema_create")
     * @Method("POST")
     * @Template("AppBundle:Subtema:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Subtema();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->addFlash('mensaje', 'El subtema ha sido creado correctamente');
            return $this->redirect($this->generateUrl('subtema_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Subtema entity.
     *
     * @param Subtema $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Subtema $entity)
    {
        $form = $this->createForm(new SubtemaType(), $entity, array(
            'action' => $this->generateUrl('subtema_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Subtema entity.
     *
     * @Route("/new", name="subtema_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Subtema();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Subtema entity.
     *
     * @Route("/{id}", name="subtema_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Subtema')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encuentra el subtema.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Subtema entity.
     *
     * @Route("/{id}/edit", name="subtema_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Subtema')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encuentra el subtema.');
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
    * Creates a form to edit a Subtema entity.
    *
    * @param Subtema $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Subtema $entity)
    {
        $form = $this->createForm(new SubtemaType(), $entity, array(
            'action' => $this->generateUrl('subtema_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Subtema entity.
     *
     * @Route("/{id}", name="subtema_update")
     * @Method("PUT")
     * @Template("AppBundle:Subtema:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Subtema')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encuentra el subtema.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->addFlash('mensaje', 'El subtema ha sido actualizada correctamente');
            return $this->redirect($this->generateUrl('subtema_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Subtema entity.
     *
     * @Route("/{id}", name="subtema_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Subtema')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se encuentra el subtema.');
            }

            $em->remove($entity);
            $em->flush();
            $this->addFlash('mensaje', 'La eliminación fue realizada correctamente');
        }

        return $this->redirect($this->generateUrl('subtema'));
    }

    /**
     * Creates a form to delete a Subtema entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('subtema_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
