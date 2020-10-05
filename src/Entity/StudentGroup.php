<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Groups;


/**
 * StudentGroup
 *
 * @ORM\Table(name="student_group")
 * @ORM\Entity(repositoryClass="App\Repository\StudentGroupRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class StudentGroup
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
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="studentgroup")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", onDelete="CASCADE")
     * @Groups({"student_group", "future_ambassador"})
     */
    private $student; 

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Groupe", inversedBy="studentsgroup")
     * @ORM\JoinColumn(name="groupe_id", referencedColumnName="id", onDelete="CASCADE")
     * @Groups({"future_ambassador", "student_list", "student_group"})
     */
    private $group;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getStudent(): ?User
    {
        return $this->student;
    }

    public function setStudent(?User $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getGroup(): ?Groupe
    {
        return $this->group;
    }

    public function setGroup(?Groupe $group): self
    {
        $this->group = $group;

        return $this;
    }

    
}
