<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }

    /**
     * Muestra la vista de selección de modalidad: Automatico y Manual
     *
     * @Route("/seleccion", name="seleccion")
     * @Template("AppBundle:Default:seleccion.html.twig")
     */
    public function seleccionAction()
    {
    	return $this->render(
            'AppBundle:Default:seleccion.html.twig',
            array(
        	)
        );
    }

    /**
     * Muestra la vista de administrador
     *
     * @Route("/admin", name="admin")
     * @Template("AppBundle:Default:admin.html.twig")
     */
    public function adminAction()
    {
        return $this->render(
            'AppBundle:Default:admin.html.twig',
            array(
            )
        );
    }
}
