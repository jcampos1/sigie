<?php
//src/UserBundle/Entity/User.php

namespace UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "El campo 'Nombre' no puede estar vacio")
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "El campo 'Apellido' no puede estar vacio")
     * @ORM\Column(name="lastName", type="string", length=255, nullable=false)
     */
    private $lastName;

    /**
     * @var int
     *
     * @Assert\NotBlank(message = "El campo 'ci' no puede estar vacio")
     * @ORM\Column(name="ci", type="integer", nullable=false)
     */
    private $ci;

    /**
     * @var string
     *
     * @Assert\NotBlank(message = "El campo 'Teléfono' no puede estar vacio")
     * @ORM\Column(name="phone", type="string", length=255, nullable=false)
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Ejercicio", mappedBy="usuario")
     */
    protected $ejercicios;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Evaluacion", mappedBy="usuario")
     */
    protected $evaluaciones;

    /**
     * Agrega un rol al usuario.
     * @throws Exception
     * @param Rol $rol 
     */
    public function addRole( $rol )
    {
        switch ($rol) {
            //para usuarios preparadores
            case 1:
                array_push($this->roles, 'ROLE_USER');
                break;
            //para usuarios profesores
            case 2:
                array_push($this->roles, 'ROLE_ADMIN');
                break;
            //para usuarios profesores
            case 3:
                array_push($this->roles, 'ROLE_SUPER_ADMIN');
                break;
        }
    }


    public function __construct()
    {
        parent::__construct();
        $this->ejercicios = new ArrayCollection();
        $this->evaluaciones = new ArrayCollection();
        // your own logic
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set ci
     *
     * @param integer $ci
     *
     * @return User
     */
    public function setCi($ci)
    {
        $this->ci = $ci;

        return $this;
    }

    /**
     * Get ci
     *
     * @return integer
     */
    public function getCi()
    {
        return $this->ci;
    }

    /**
     * Add ejercicio
     *
     * @param \AppBundle\Entity\Ejercicio $ejercicio
     *
     * @return User
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

    /**
     * Add evaluacione
     *
     * @param \AppBundle\Entity\Evaluacion $evaluacione
     *
     * @return User
     */
    public function addEvaluacione(\AppBundle\Entity\Evaluacion $evaluacione)
    {
        $this->evaluaciones[] = $evaluacione;

        return $this;
    }

    /**
     * Remove evaluacione
     *
     * @param \AppBundle\Entity\Evaluacion $evaluacione
     */
    public function removeEvaluacione(\AppBundle\Entity\Evaluacion $evaluacione)
    {
        $this->evaluaciones->removeElement($evaluacione);
    }

    /**
     * Get evaluaciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvaluaciones()
    {
        return $this->evaluaciones;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}
