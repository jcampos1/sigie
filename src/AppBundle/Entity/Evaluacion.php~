<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evaluacion
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\EvaluacionRepository")
 */
class Evaluacion
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
     *
     * @ORM\Column(name="asignatura", type="string", length=255)
     */
    private $asignatura;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=255)
     */
    private $tipo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_creacion", type="date")
     */
    private $fechaCreacion;

    /**
     * @ORM\ManyToMany(targetEntity="Ejercicio", inversedBy="evaluaciones")
     * @ORM\JoinTable(name="evaluacion_ejercicio",
     *      joinColumns={@ORM\JoinColumn(name="evaluacion_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="ejercicio_id", referencedColumnName="id")}
     *      )
     */
    private $ejercicios;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="evaluaciones")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    protected $usuario;

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
     * Set asignatura
     *
     * @param string $asignatura
     *
     * @return Evaluacion
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
     * Set tipo
     *
     * @param string $tipo
     *
     * @return Evaluacion
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Evaluacion
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set usuario
     *
     * @param \UserBundle\Entity\User $usuario
     *
     * @return Evaluacion
     */
    public function setUsuario(\UserBundle\Entity\User $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \UserBundle\Entity\User
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ejercicios = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add ejercicio
     *
     * @param \AppBundle\Entity\Ejercicio $ejercicio
     *
     * @return Evaluacion
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
