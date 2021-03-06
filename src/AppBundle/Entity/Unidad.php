<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Unidad
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UnidadRepository")
 */
class Unidad
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
     * @var string
     * @Assert\NotBlank(message = "El campo 'asignatura' no puede estar vacio")
     * @Assert\Choice(choices = {"EDI", "EDII"}, message = "Debe seleccionar la asignatura")
     * @ORM\Column(name="asignatura", type="string", length=255)
     */
    private $asignatura;

    /**
     * @ORM\OneToMany(targetEntity="Subtema", mappedBy="unidad")
     */
    private $subtemas;

    /**
     * @ORM\OneToMany(targetEntity="Ejercicio", mappedBy="unidad")
     */
    private $ejercicios;
    // ...

    public function __construct() {
        $this->subtemas = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Unidad
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
     * Set asignatura
     *
     * @param string $asignatura
     *
     * @return Unidad
     */
    public function setAsignatura($asignatura)
    {
        $this->asignatura = $asignatura;

        return $this;
    }

    /**
     * Get asignatura
     *
     * @return string
     */
    public function getAsignatura()
    {
        return $this->asignatura;
    }

    /**
     * Add subtema
     *
     * @param \AppBundle\Entity\Subtema $subtema
     *
     * @return Unidad
     */
    public function addSubtema(\AppBundle\Entity\Subtema $subtema)
    {
        $this->subtemas[] = $subtema;

        return $this;
    }

    /**
     * Remove subtema
     *
     * @param \AppBundle\Entity\Subtema $subtema
     */
    public function removeSubtema(\AppBundle\Entity\Subtema $subtema)
    {
        $this->subtemas->removeElement($subtema);
    }

    /**
     * Get subtemas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubtemas()
    {
        return $this->subtemas;
    }

    public function __toString()
    {
        return strval($this->nombre);
    }

    /**
     * Add ejercicio
     *
     * @param \AppBundle\Entity\Ejercicio $ejercicio
     *
     * @return Unidad
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
}
