<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;
 
/**
 * Certificate
 *
 * @ORM\Table(name="certificate")
 * @ORM\Entity(repositoryClass="App\Repository\CertificateRepository");
 * @ORM\HasLifecycleCallbacks()
 */

 class Certificate
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     */
    private $country;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="program", type="string", length=255)
     */
    private $program;

    /**
     * @var string
     *
     */
    private $name;





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
     * Set country
     *
     * @param string $country
     *
     * @return Certificate
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return Certificate
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set program
     *
     * @param string $program
     *
     * @return Certificate
     */
    public function setProgram($program)
    {
        $this->program = $program;

        return $this;
    }

    /**
     * Get program
     *
     * @return string
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * Set program
     *
     * @param string $program
     *
     * @return Certificate
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get program
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
