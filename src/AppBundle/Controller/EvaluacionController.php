<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Evaluacion;
use AppBundle\Form\EvaluacionType;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Font;

/**
 * Evaluacion controller.
 *
 * @Route("/evaluacion")
 */
class EvaluacionController extends Controller
{

    /**
     * Lists all Evaluacion entities.
     *
     * @Route("/", name="evaluacion")
     * @Method("GET")
     * @Template()
     */
    /*public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Evaluacion')->findAll();

        return array(
            'entities' => $entities,
        );
    }*/

    /**
     * obtiene un ejercicio con formato para insertarlo en tabla.
     *
     * @Route("/fila_seleccionada", name="fila_seleccionada")
     * @Method("POST")
     * @Template("AppBundle:Evaluacion:fila_seleccionada.html.twig")
     */
    public function filasAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $entity = $em->getRepository('AppBundle:Ejercicio')->find($id);

        return array(
            'ejercicio' => $entity,
        );
    }

    /**
     *
     * @Route("/unidad_de_componente", name="unidad_de_componente")
     * @Method("POST")
     * @Template("AppBundle:Evaluacion:unidad_de_componente.html.twig")
     */
    public function unidadesAction(Request $request)
    {
        $componente = $request->request->get('componente');
        $asignatura = $request->request->get('asignatura');

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Unidad');
        $query = $repository->createQueryBuilder('u')
            ->where('u.asignatura = :asignatura')
            ->setParameter('asignatura', $asignatura)
            ->getQuery();
            $unidades = $query->getResult();
        
        return array(
            'unidades' => $unidades,
            'componente' => $componente,
            'asignatura' => $asignatura
        );
    }

    /**
     *
     * @Route("/subtemas_de_unidad", name="subtemas_de_unidad")
     * @Method("POST")
     * @Template("AppBundle:Evaluacion:subtemas_de_unidad.html.twig")
     */
    public function subtemasAction(Request $request)
    {
        $unidad = $request->request->get('unidad');
        $componente = $request->request->get('componente');
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT s FROM AppBundle:Subtema s WHERE s.unidad = :unidad'
        )->setParameter('unidad', $unidad);
        
        $session = $request->getSession();
        // obtener la modalidad seleccionada
        $modalidad = $session->get('modalidad');

        $subtemas = $query->getResult();
        return array(
            'modalidad' => $modalidad,
            'subtemas' => $subtemas,
            'componente' => $componente,
        );
    }

    /**
     *
     * @Route("/evaluacion_download", name="generarWord")
     * @Method("POST")
     */
    public function generarAction(Request $request)
    {
        $contenido = $request->request->get('contenido_evaluacion');
        $tipo = $request->request->get('tipo_evaluacion');
        $titulo = $request->request->get('titulo_evaluacion');
        $fecha = $request->request->get('fecha_evaluacion');

        $ids_ejer = $request->request->get('ids_ejer');
        $repository = $this->getDoctrine()->getRepository('AppBundle:Ejercicio');

        $session = $request->getSession();
        // obtener la modalidad seleccionada
        $modalidad = $session->get('modalidad');

        if ( $modalidad == "manual" ) {
            $i=1;
            //se castea a array para tabajar el objeto de ids como array asociativo
            $array_ids = json_decode($ids_ejer);
            foreach ($array_ids as $clave=>$ejer) {
                if ( $i == 1) {
                    $ids_ejer = "e.id = '".$ejer."' ";
                }else{
                    $ids_ejer = $ids_ejer." or e.id = '".$ejer."' ";
                }
                $i++;
            }
        }

        //seleccion de todos los objetos a ser actualizados
        $query = $repository->createQueryBuilder('e')
            ->where($ids_ejer)
            ->getQuery();
            $ejercicios = $query->getResult();

        //actualización de objeto por objeto
        $today = new \DateTime();
        foreach ($ejercicios as $ejer) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Ejercicio')->findOneById($ejer->getId());

            $entity->setFechaUltimoUso($today);

            $em->flush();
        }
    
        //se define que contenido a generar es de tipo .docx
        header("Content-Disposition: attachment; filename=Documento1.docx; charset=utf-8");
        return $this->render(
            'AppBundle:Evaluacion:generarWord.html.twig',
            array(
            'contenido_evaluacion' => $contenido,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'fecha' => $fecha,
        )
        );
    }

    /**
     *
     * @Route("/solucion_download", name="solucionWord")
     * @Method("POST")
     */
    public function solucionAction(Request $request)
    {
        $session = $request->getSession();
        $modalidad = $session->get('modalidad');
        //la diferencia es que en modalidad automatica, los ids son obtenidos de la sesion
        if($modalidad != "manual"){
            $ids_ejer = $session->get('ids_auto');
        }else{
            $ids_ejer = $request->request->get('ids');
        }

        //se castea a array para tabajar el objeto de ids como array asociativo
        $array_ids = json_decode($ids_ejer);
        $repository = $this->getDoctrine()->getRepository('AppBundle:Ejercicio');
        
        $i=1;
        foreach ($array_ids as $clave=>$ejer) {
            if ( $i == 1) {
                $ids_ejer = "e.id = '".$ejer."' ";
            }else{
                $ids_ejer = $ids_ejer." or e.id = '".$ejer."' ";
            }
            $i++;
        }
        //seleccion de todos los ejercicios que tienen solucion
        $query = $repository->createQueryBuilder('e')
            ->where("(".$ids_ejer.") and e.solucion != '\o' ")
            ->getQuery();
            $ejercicios = $query->getResult();
    
        //se define que contenido a generar es de tipo .docx
        header("Content-Disposition: attachment; filename=Documento1.docx; charset=utf-8");
        return $this->render(
            'AppBundle:Evaluacion:solucionWord.html.twig',
            array(
            'pagination' => $ejercicios,
        )
        );
    }

    /**
     * Creates a new Evaluacion entity.
     *
     * @Route("/", name="evaluacion_create")
     * @Method("POST")
     * @Template("AppBundle:Evaluacion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Evaluacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('evaluacion_show', array('id' => $entity->getId())));
        }

        $session = $request->getSession();
        if( !$session->get("actualizacion") ) {
            $session->set("actualizacion", "lista");
            $em = $this->getDoctrine()->getManager();
            $em->getRepository('AppBundle:Ejercicio')->privacityUpdate();
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            
        );
    }

    /**
     * Creates a form to create a Evaluacion entity.
     *
     * @param Evaluacion $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Evaluacion $entity)
    {
        $form = $this->createForm(new EvaluacionType(), $entity, array(
            'action' => $this->generateUrl('evaluacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Evaluacion entity.
     *
     * @Route("/new", name="evaluacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        //si no consigue el parametro modalidad, se asume modalidad automatica por defecto
        $modalidad = $request->query->get("modalidad", "auto");
        $session = $request->getSession();
        // guarda un atributo para reutilizarlo durante una
        // petición posterior del usuario
        $session->set('modalidad', $modalidad);

        $entity = new Evaluacion();
        $form   = $this->createCreateForm($entity);

        return array(
            'modalidad' => $modalidad,
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Evaluacion entity.
     *
     * @Route("/{id}", name="evaluacion_show")
     * @Method("GET")
     * @Template()
     */
    /*public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Evaluacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Evaluacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }*/

    /**
     * Displays a form to edit an existing Evaluacion entity.
     *
     * @Route("/{id}/edit", name="evaluacion_edit")
     * @Method("GET")
     * @Template()
     */
    /*public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Evaluacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Evaluacion entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }*/

    /**
    * Creates a form to edit a Evaluacion entity.
    *
    * @param Evaluacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    /*private function createEditForm(Evaluacion $entity)
    {
        $form = $this->createForm(new EvaluacionType(), $entity, array(
            'action' => $this->generateUrl('evaluacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }*/
    /**
     * Edits an existing Evaluacion entity.
     *
     * @Route("/{id}", name="evaluacion_update")
     * @Method("PUT")
     * @Template("AppBundle:Evaluacion:edit.html.twig")
     */
    /*public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Evaluacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Evaluacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('evaluacion_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }*/
    /**
     * Deletes a Evaluacion entity.
     *
     * @Route("/{id}", name="evaluacion_delete")
     * @Method("DELETE")
     */
    /*public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Evaluacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Evaluacion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('evaluacion'));
    }*/



    /**
     *
     * @Route("/obtener_ejercicios", name="obt_ejercicios")
     * @Method("POST")
     * @Template("AppBundle:Evaluacion:ejercicios.html.twig")
     */
    public function ejerciciosAction(Request $request)
    {
        $myObj =  json_decode($request->request->get("datos"));
        $myObj2 = json_decode($request->request->get("complejidades"));
        $pagePost = $request->request->get("page");
        
        if($pagePost == ""){
            $page = 1;
        }else{
            $page = (int) $pagePost;
        }
        $session = $request->getSession();
        // obtener la modalidad seleccionada
        $modalidad = $session->get('modalidad');

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Ejercicio');
        
        $i=1;
        $cadena ="";
        foreach ($myObj2 as $complejidad) {
            if ( $i == 1) {
                $cadena = "c.id = '".$complejidad."'";
            }else{
                $cadena = $cadena." or c.id = '".$complejidad."'";
            }
            $i++;
        }

        $ejercicios = array();
        $user = $this->getUser();
        foreach ($myObj as $subtema) {
            if( $modalidad != "manual" ){
                if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
                    $query = $repository->createQueryBuilder('e')
                    ->setMaxResults($subtema->cantidad)
                    ->where("e.componente = :comp and
                        e.subtema =:subt")
                        ->innerJoin('e.complejidades', 'c')
                        ->andWhere($cadena)
                    ->orderBy('e.fechaUltimoUso', 'ASC')
                    ->setParameter('comp', $subtema->componente)
                    ->setParameter('subt', $subtema->subtema)
                    ->getQuery();
                }else{
                    $query = $repository->createQueryBuilder('e')
                    ->setMaxResults($subtema->cantidad)
                    ->where("e.componente = :comp and
                        e.subtema =:subt and
                        ( (e.visibilidad = :visibilidad) or (e.visibilidad = 'privado' and e.usuario = :usuario) )")
                        ->innerJoin('e.complejidades', 'c')
                        ->andWhere($cadena)
                    ->orderBy('e.fechaUltimoUso', 'ASC')
                    ->setParameter('comp', $subtema->componente)
                    ->setParameter('subt', $subtema->subtema)
                    ->setParameter('usuario', $user->getId())
                    ->setParameter('visibilidad', 'publico')
                    ->getQuery();
                }
            }else{
                if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
                    $query = $repository->createQueryBuilder('e')
                    ->where("e.componente = :comp and
                        e.subtema =:subt")
                        ->innerJoin('e.complejidades', 'c')
                        ->andWhere($cadena)
                    ->orderBy('e.fechaUltimoUso', 'ASC')
                    ->setParameter('comp', $subtema->componente)
                    ->setParameter('subt', $subtema->subtema)
                    ->getQuery();
                }else{
                    $query = $repository->createQueryBuilder('e')
                    ->where("e.componente = :comp and
                        e.subtema =:subt and
                        ( (e.visibilidad = :visibilidad) or (e.visibilidad = 'privado' and e.usuario = :usuario))")
                        ->innerJoin('e.complejidades', 'c')
                        ->andWhere($cadena)
                    ->orderBy('e.fechaUltimoUso', 'ASC')
                    ->setParameter('comp', $subtema->componente)
                    ->setParameter('subt', $subtema->subtema)
                    ->setParameter('usuario', $user->getId())
                    ->setParameter('visibilidad', 'publico')
                    ->getQuery();
                }
            }
            $resultado = $query->getResult();
            $ejercicios = array_merge($ejercicios, $resultado);   
        } 

        $ids_ejer = "";
        $ids = array();
        if($modalidad != "manual"){
            $i=1;
            foreach ($ejercicios as $ejer) {
                $ids[$ejer->getId()] = $ejer->getId();
                if ( $i == 1) {
                    $ids_ejer = "e.id = '".$ejer->getId()."' ";
                }else{
                    $ids_ejer = $ids_ejer." or e.id = '".$ejer->getId()."' ";
                }
                $i++;
            }
        }
        $session = $request->getSession();
        $session->set('ids_auto', json_encode($ids));

        // Añadimos el paginador (En este caso el parámetro "1" es la página actual, y parámetro "10" es el número de páginas a mostrar)
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $ejercicios,
            $page,7
        );

        return array(
            'modalidad' => $modalidad,
            'pagination' => $pagination,
            'ids_ejer' => $ids_ejer,
        );
    }

/**
     *
     * @Route("/buscar_ejercicios", name="buscar_ejercicios")
     * @Method("POST")
     * @Template("AppBundle:Evaluacion:ejercicios2.html.twig")
     */
    public function buscarAction(Request $request)
    {
        $repository = $this->getDoctrine()
        ->getRepository('AppBundle:Ejercicio');
           $query = $repository->createQueryBuilder('e')
                   ->where("((e.id LIKE :texto) or (e.componente LIKE :texto) or (e.asignatura = :texto2)) and e.visibilidad = :visibilidad")
                   ->setParameter('visibilidad', "publico")
                   ->setParameter('texto2', $request->request->get("texto"))
                   ->setParameter('texto', '%'.$request->request->get("texto").'%')
                   ->getQuery();
            $ejercicios = $query->getResult();

        // Añadimos el paginador (En este caso el parámetro "1" es la página actual, y parámetro "10" es el número de páginas a mostrar)
        $paginator  = $this->get('knp_paginator');
        if(!$this->get('request')->query->get('page') && !$request->request->get("page")){
                $page = 1;
            }else{
                if($this->get('request')->query->get('page')){
                    $page = $this->get('request')->query->getInt('page');
                }else{
                    $page = $request->request->getInt("page");
                }
            }
        $pagination = $paginator->paginate(
            $ejercicios,
            $page,7
        );
        $session = $request->getSession();
        // obtener la modalidad seleccionada
        $modalidad = $session->get('modalidad');
        $ids_ejer = "";
        return array(
            'modalidad' => $modalidad,
            'pagination' => $pagination,
            'ids_ejer' => $ids_ejer,
        );
    }

    /**
     * Creates a form to delete a Evaluacion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /*private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('evaluacion_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }*/
}
