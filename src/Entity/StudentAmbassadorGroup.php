<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Groups;

/**
 * StudentAmbassadorGroup
 *
 * @ORM\Table(name="student_ambassador_group")
 * @ORM\Entity(repositoryClass="App\Repository\StudentAmbassadorGroupRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class StudentAmbassadorGroup
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
     * @ORM\Column(name="type", type="string", nullable=true,  length=255)
     */
    private $type;


    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="studentambassadorgroup")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", onDelete="CASCADE")
     * @Groups({"student_group", "future_ambassador"})
     */
    private $student;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Groupe", inversedBy="studentsambassadorgroup")
     * @ORM\JoinColumn(name="groupe_id", referencedColumnName="id", onDelete="CASCADE")
     * @Groups({"future_ambassador", "student_list"})
     */
    private $group;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
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
