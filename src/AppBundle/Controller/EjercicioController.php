<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Ejercicio;
use AppBundle\Form\EjercicioType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
/**
 * Ejercicio controller.
 *
 * @Route("/ejercicio")
 */
class EjercicioController extends Controller
{
    /**
     * Lists all Ejercicio entities.
     *
     * @Route("/", name="ejercicio")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        //se actualizan los ejercicios cuya privacidad es privada que fueron creados hace 15 dias
        $session = $request->getSession();
        if( !$session->get("actualizacion") ) {
            $session->set("actualizacion", "lista");
            $em = $this->getDoctrine()->getManager();
            $em->getRepository('AppBundle:Ejercicio')->privacityUpdate();
        }

        $user = $this->getUser();
        $repository = $this->getDoctrine()
        ->getRepository('AppBundle:Ejercicio');
        if($this->getRequest()->isXmlHttpRequest()){
            if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
                $query = $repository->createQueryBuilder('e')
                   ->where("(e.id LIKE :texto) or (e.componente LIKE :texto) or (e.asignatura = :texto2)")
                   ->setParameter('texto2', $request->request->get("texto"))
                   ->setParameter('texto', '%'.$request->request->get("texto").'%')
                   ->getQuery();
                $entities = $query->getResult();
            }else{
               $query = $repository->createQueryBuilder('e')
                       ->where("((e.id LIKE :texto) or (e.componente LIKE :texto or e.asignatura = :texto2)) and ((e.visibilidad = 'publico') or (e.visibilidad = 'privado' and e.usuario = :usuario) )")
                       ->setParameter('usuario', $user->getId())
                       ->setParameter('texto2', $request->request->get("texto"))
                       ->setParameter('texto', '%'.$request->request->get("texto").'%')
                       ->getQuery();
                $entities = $query->getResult();
            }
        }else{
            if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
                $query = $repository->createQueryBuilder('e')
                    ->getQuery();
                $entities = $query->getResult();
            }else{
                $query = $repository->createQueryBuilder('e')
                        ->where("e.visibilidad = 'publico' or (e.visibilidad = 'privado' and e.usuario = :usuario)")
                        ->setParameter('usuario', $user->getId())
                        ->getQuery();
                $entities = $query->getResult();
            }
        }

        // Añadimos el paginador (En este caso el parámetro "1" es la página actual, y parámetro "10" es el número de páginas a mostrar)
        $paginator  = $this->get('knp_paginator');
        if(!$request->request->get("page")){
            $pagination = $paginator->paginate(
                $entities,
                $this->get('request')->query->getInt('page', 1),7
            );
        }else{
            $pagination = $paginator->paginate(
                $entities,
                $request->request->getInt('page', 1),7
            );
        }

        if($this->getRequest()->isXmlHttpRequest()){
            return $this->render(
                'AppBundle:Ejercicio:filas_ejercicios.html.twig',
                array('pagination' => $pagination,)
            );
        }else{
            return $this->render(
                'AppBundle:Ejercicio:index.html.twig',
                array('pagination' => $pagination,)
            );

        }
    }
    /**
     * Creates a new Ejercicio entity.
     *
     * @Route("/new/procesa", name="ejercicio_create")
     * @Method("POST")
     * @Template("AppBundle:Ejercicio:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Ejercicio();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
        	$asignatura = $entity->getUnidad()->getAsignatura();
            $entity->setAsignatura($asignatura);
            $today = new \DateTime();
	        $user = $this->getUser();
	        $entity->setFechaCreacion($today);
	        $entity->setUsuario($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();


            $this->addFlash('mensaje', 'El ejercicio ha sido creado correctamente');

            return $this->redirect($this->generateUrl('ejercicio_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Ejercicio entity.
     *
     * @param Ejercicio $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Ejercicio $entity)
    {
        $form = $this->createForm(new EjercicioType(), $entity, array(
            'action' => $this->generateUrl('ejercicio_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Ejercicio entity.
     *
     * @Route("/new", name="ejercicio_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {	
        $entity = new Ejercicio();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Ejercicio entity.
     *
     * @Route("/{id}", name="ejercicio_show")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function showAction($id)
    {
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Ejercicio')->find($id);
        }else{
            $user = $this->getUser();
            $repository = $this->getDoctrine()->getRepository('AppBundle:Ejercicio');
            $query = $repository->createQueryBuilder('e')
                    ->where("e.id = :id and (e.visibilidad = 'publico' or (e.visibilidad = 'privado' and e.usuario = :usuario))")
                    ->setParameter('usuario', $user->getId())
                    ->setParameter('id', $id)
                    ->getQuery();
            $entity = $query->getResult();
            if($entity){
                $entity = $entity[0];
            }
             
        }

        if (!$entity) {
            throw $this->createNotFoundException('El ejercicio no se encuentra');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Ejercicio entity.
     *
     * @Route("/{id}/edit", name="ejercicio_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Ejercicio')->find($id);
        }else{
            $user = $this->getUser();
            $repository = $this->getDoctrine()->getRepository('AppBundle:Ejercicio');
            $query = $repository->createQueryBuilder('e')
                    ->where("e.id = :id and (e.visibilidad = 'publico' or (e.visibilidad = 'privado' and e.usuario = :usuario))")
                    ->setParameter('usuario', $user->getId())
                    ->setParameter('id', $id)
                    ->getQuery();
            $entity = $query->getResult();
            if($entity){
                $entity = $entity[0];
            }
             
        }

        if (!$entity) {
            throw $this->createNotFoundException('El ejercicio no se encuentra');
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
    * Creates a form to edit a Ejercicio entity.
    *
    * @param Ejercicio $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Ejercicio $entity)
    {
        $form = $this->createForm(new EjercicioType(), $entity, array(
            'action' => $this->generateUrl('ejercicio_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Ejercicio entity.
     *
     * @Route("/{id}", name="ejercicio_update")
     * @Method("PUT")
     * @Template("AppBundle:Ejercicio:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Ejercicio')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('No se encuentra el ejercicio');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $asignatura = $entity->getUnidad()->getAsignatura();
            $entity->setAsignatura($asignatura);
            $em->flush();

            $this->addFlash('mensaje', 'El ejercicio ha sido actualizado correctamente');
            return $this->redirect($this->generateUrl('ejercicio_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Ejercicio entity.
     *
     * @Route("/{id}", name="ejercicio_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Ejercicio')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('No se encuentra el ejercicio a eliminar.');
            }

            $em->remove($entity);
            $em->flush();
            $this->addFlash('mensaje', 'La eliminación fue realizada correctamente');
        }

        return $this->redirect($this->generateUrl('ejercicio'));
    }

    /**
     * Creates a form to delete a Ejercicio entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ejercicio_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete' ))
            ->getForm()
        ;
    }
}
