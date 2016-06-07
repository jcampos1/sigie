<?php

namespace MensajeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MensajeBundle\Entity\Mensaje;
use MensajeBundle\Form\MensajeType;

/**
 * Mensaje controller.
 *
 * @Route("/mensaje")
 */
class MensajeController extends Controller
{

    /**
     * Lists all Mensaje entities.
     *
     * @Route("/recibidos", name="mensaje")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        $repository = $this->getDoctrine()
            ->getRepository('MensajeBundle:Mensaje');
        
        $query = $repository->createQueryBuilder('m')
                ->where("m.destinatario = :destinatario and 
                    m.tipo =:tipo")
            ->orderBy('m.estado', 'ASC')
            ->setParameter('destinatario', $user->getId())
            ->setParameter('tipo', true)
            ->getQuery();
            $entities = $query->getResult();

        // Añadimos el paginador (En este caso el parámetro "1" es la página actual, y parámetro "10" es el número de páginas a mostrar)
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->getInt('page', 1),10
        );

        $session = $request->getSession();
        $session->set('buzon', "entrada");

        /*$message = \Swift_Message::newInstance()
        ->setSubject('Hello Email')
        ->setFrom('jcampos@ea-asc.com')
        ->setTo('jkmasterweb@gmail.com')
        ->setBody('You should see me from the profiler!')
    ;

    $this->get('mailer')->send($message);*/

        return array(
            'pagination' => $pagination,
            "buzon" => "entrada",
        );
    }

    /**
     * Lists all Mensaje entities.
     *
     * @Route("/enviados", name="mensajes_enviados")
     * @Method("GET")
     * @Template("MensajeBundle:Mensaje:index.html.twig")
     */
    public function enviadosAction(Request $request)
    {
        $user = $this->getUser();
        $repository = $this->getDoctrine()
            ->getRepository('MensajeBundle:Mensaje');
        
        $query = $repository->createQueryBuilder('m')
                ->where("m.remitente = :remitente and 
                    m.tipo = :tipo")
            ->orderBy('m.estado', 'ASC')
            ->setParameter('remitente', $user->getId())
            ->setParameter('tipo', false)
            ->getQuery();
            $entities = $query->getResult();

        // Añadimos el paginador (En este caso el parámetro "1" es la página actual, y parámetro "10" es el número de páginas a mostrar)
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->getInt('page', 1),10
        );

        $session = $request->getSession();

        $buzon = $session->set('buzon', "salida");

        return array(
            'pagination' => $pagination,
            "buzon" => "salida", 
        );
    }

    /**
     * Creates a new Mensaje entity.
     *
     * @Route("/", name="mensaje_create")
     * @Method("POST")
     * @Template("MensajeBundle:Mensaje:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Mensaje();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $this->getUser();
            $today = new \DateTime();
            $asunto = $entity->getAsunto();
            $desc = $entity->getDescripcion();
            //se castea a array para tabajar el objeto de destinatarios como array asociativo
            $array_ids = json_decode($request->request->get('destinatarios'));
            $em = $this->getDoctrine()->getManager();
            $emUser = $this->getDoctrine()->getManager();
            foreach ($array_ids as $clave=>$dest) {
                $mensajeEnviado = new Mensaje();
                $mensajeRecibido = new Mensaje();
                $destino = $emUser->getRepository('UserBundle:User')->find($dest);

                $mensajeEnviado->setAsunto($asunto);
                $mensajeEnviado->setDescripcion($desc);
                $mensajeEnviado->setEstado(false);
                $mensajeEnviado->setTipo(false);
                $mensajeEnviado->setFechaEnvio($today);
                $mensajeEnviado->setDestinatario($destino);
                $mensajeEnviado->setRemitente($user); 

                $mensajeRecibido->setAsunto($asunto);
                $mensajeRecibido->setDescripcion($desc);
                $mensajeRecibido->setEstado(false);
                $mensajeRecibido->setTipo(true);
                $mensajeRecibido->setFechaEnvio($today);
                $mensajeRecibido->setDestinatario($destino);
                $mensajeRecibido->setRemitente($user); 
                
                $em->persist($mensajeEnviado);
                $em->persist($mensajeRecibido);

                $message = \Swift_Message::newInstance()
               ->setSubject($asunto)
               ->setFrom($user->getEmail())
               ->setBody($desc, 'text/html')
               ->setTo($destino->getEmail());
               $this->get('mailer')->send($message);
            }
            
            $em->flush();
            $this->addFlash('mensaje', 'El mensaje fue enviado con éxito');

            return $this->redirect($this->generateUrl('mensaje'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Mensaje entity.
     *
     * @param Mensaje $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Mensaje $entity)
    {
        $form = $this->createForm(new MensajeType(), $entity, array(
            'action' => $this->generateUrl('mensaje_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Mensaje entity.
     *
     * @Route("/new", name="mensaje_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Mensaje();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Mensaje entity.
     *
     * @Route("/{id}", name="mensaje_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        $session = $request->getSession();
        $buzon = $session->get('buzon');

        $user = $this->getUser();
        $repository = $this->getDoctrine()->getRepository('MensajeBundle:Mensaje');
        
        if( $buzon == "entrada" ){
            $query = $repository->createQueryBuilder('m')
                    ->where("m.id = :id and m.destinatario = :usuario")
                    ->setParameter('usuario', $user->getId())
                    ->setParameter('id', $id)
                    ->getQuery();
            $entity = $query->getResult();
        }else{
            $query = $repository->createQueryBuilder('m')
                    ->where("m.id = :id and m.remitente = :usuario")
                    ->setParameter('usuario', $user->getId())
                    ->setParameter('id', $id)
                    ->getQuery();
            $entity = $query->getResult();
        }


        if($entity){
            $entity = $entity[0];
        }

        if (!$entity) {
            throw $this->createNotFoundException('El mensaje no se encuentra');
        }

        if( $buzon == "entrada" ){
            $em = $this->getDoctrine()->getManager();
            $entity->setEstado(true);
            $em->flush();
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            "buzon" => $buzon,
        );
    }

    
    /**
     * Deletes a Mensaje entity.
     *
     * @Route("/{id}", name="mensaje_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('MensajeBundle:Mensaje')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Mensaje entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->addFlash('mensaje', 'La eliminación fue realizada correctamente');
        }

        $session = $request->getSession();
        $buzon = $session->get('buzon');
        if( $buzon == "entrada" ){
            return $this->redirect($this->generateUrl('mensaje'));
        }else{
            return $this->redirect($this->generateUrl('mensajes_enviados'));
        }
    }

    /**
     * Deletes a Mensaje entity.
     *
     * @Route("/eliminar", name="eliminar_mensajes")
     * @Method("POST")
     */
    public function eliminarAction(Request $request)
    {
        $myObj =  json_decode($request->request->get("mensajes"));
        $em = $this->getDoctrine()->getManager();
        $i = 1;
        foreach ($myObj as $msj) {
            $entity = $em->getRepository('MensajeBundle:Mensaje')->find($msj);
            $em->remove($entity);
            $em->flush();
            $i++;
        }

        return $this->redirect($this->generateUrl('mensaje'));
    }

    /**
     * Creates a form to delete a Mensaje entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mensaje_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
