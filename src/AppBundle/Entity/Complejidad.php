<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Complejidad
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ComplejidadRepository")
 */
class Complejidad
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank(message = "El campo 'nombre' no puede estar vacio")
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @Assert\NotBlank(message = "Debe seleccionar al menos una complejidad")
     * @ORM\ManyToMany(targetEntity="Ejercicio", mappedBy="complejidades")
     */
    private $ejercicios;

    public function __construct() {
        $this->ejercicios = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Complejidad
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Add ejercicio
     *
     * @param \AppBundle\Entity\Ejercicio $ejercicio
     *
     * @return Complejidad
     */
    public function addEjercicio(\AppBundle\Entity\Ejercicio $ejercicio)
    {
        $this->ejercicios[] = $ejercicio;

        return $this;
    }

    /**
     * Remove ejercicio
     *
     * @param \AppBundle\Entity\Ejercicio $ejercicio
     */
    public function removeEjercicio(\AppBundle\Entity\Ejercicio $ejercicio)
    {
        $this->ejercicios->removeElement($ejercicio);
    }

    /**
     * Get ejercicios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEjercicios()
    {
        return $this->ejercicios;
    }

    public function __toString()
    {
        return strval($this->id);
    }
}
