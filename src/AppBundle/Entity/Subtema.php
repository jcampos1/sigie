<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert; 

/**
 * Subtema
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SubtemaRepository")
 */
class Subtema
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
     * @ORM\Column(name="nombre", type="string", length=255, unique=true)
     */
    private $nombre;

    /**
     * @Assert\NotBlank(message = "El campo 'unidad' no puede estar vacio")
     * @ORM\ManyToOne(targetEntity="Unidad", inversedBy="subtemas")
     * @ORM\JoinColumn(name="unidad_id", referencedColumnName="id")
     */
    private $unidad;

    /**
     * @ORM\OneToMany(targetEntity="Ejercicio", mappedBy="subtema")
     */
    protected $ejercicios;

    public function __construct()
    {
        $this->ejercicios = new ArrayCollection();
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
     * @return Subtema
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
     * Set unidad
     *
     * @param \AppBundle\Entity\Unidad $unidad
     *
     * @return Subtema
     */
    public function setUnidad(\AppBundle\Entity\Unidad $unidad = null)
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return \AppBundle\Entity\Unidad
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * Add ejercicio
     *
     * @param \AppBundle\Entity\Ejercicio $ejercicio
     *
     * @return Subtema
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
