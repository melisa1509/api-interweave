<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Groups;

/**
 * Groupe
 *
 * @ORM\Table(name="groupe")
 * @ORM\Entity(repositoryClass="App\Repository\GroupeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Groupe
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"future_ambassador", "group_list"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"future_ambassador", "student_list", "student_group", "group_list"})
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="date")
     * @Groups({"group_list"})
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="final_date", type="date")
     * @Groups({"group_list"})
     */
    private $finalDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="graduation_date", type="date")
     * @Groups({"group_list"})
     */
    private $graduationDate;

    /**
     * @var int
     *
     * @ORM\Column(name="number_students", type="integer")
     */
    private $numberStudents;

    /**
     * @var int
     *
     * @ORM\Column(name="number_students_graduated", nullable=true, type="integer")
     */
    private $numberStudentsGraduated;

    /**
     * @var string
     *
     * @ORM\Column(name="modality", type="string", length=255)
     * @Groups({"group_list"})
     */
    private $modality;

    /**
     * @var string
     *
     * @ORM\Column(name="program", type="string", nullable=true, length=255)
     * @Groups({"future_ambassador", "student_list", "student_group", "group_list"})
     */
    private $program;

    /**
     * @var string
     *
     * @ORM\Column(name="interweave_local", type="string", nullable=true, length=255)
     */
    private $interweaveLocal;

    /**
     * @var string
     *
     * @ORM\Column(name="authorization_code", type="string", nullable=true, length=255)
     */
    private $authorizationCode;

    /**
     * @var string
     *
     * @ORM\Column(name="name_image", type="string", nullable=true, length=255)
     */
    private $nameImage;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="groupes")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * @Groups({"future_ambassador", "group_list", "student_list", "student_group"})
     * 
     */
    private $embassador;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StudentGroup", mappedBy="group")
     * @Serializer\Exclude()
     */
    private $studentsgroup;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StudentAmbassadorGroup", mappedBy="group")
     * @Serializer\Exclude()
     */
    private $studentsambassadorgroup;

    /**
     * param Graduates
     *
     */

    private $graduates;

    public function __construct()
    {
        $this->studentsgroup = new ArrayCollection();
        $this->studentsambassadorgroup = new ArrayCollection();
    }


    public function __toString(){
            return $this->name;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Groupe
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
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Groupe
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set finalDate
     *
     * @param \DateTime $finalDate
     *
     * @return Groupe
     */
    public function setFinalDate($finalDate)
    {
        $this->finalDate = $finalDate;

        return $this;
    }

    /**
     * Get finalDate
     *
     * @return \DateTime
     */
    public function getFinalDate()
    {
        return $this->finalDate;
    }

    /**
     * Set numberStudents
     *
     * @param integer $numberStudents
     *
     * @return Groupe
     */
    public function setNumberStudents($numberStudents)
    {
        $this->numberStudents = $numberStudents;

        return $this;
    }

    /**
     * Get numberStudents
     *
     * @return int
     */
    public function getNumberStudents()
    {
        return $this->numberStudents;
    }

    public function getGraduationDate(): ?\DateTimeInterface
    {
        return $this->graduationDate;
    }

    public function setGraduationDate(\DateTimeInterface $graduationDate): self
    {
        $this->graduationDate = $graduationDate;

        return $this;
    }

    public function getNumberStudentsGraduated(): ?int
    {
        return $this->numberStudentsGraduated;
    }

    public function setNumberStudentsGraduated(?int $numberStudentsGraduated): self
    {
        $this->numberStudentsGraduated = $numberStudentsGraduated;

        return $this;
    }

    public function getModality(): ?string
    {
        return $this->modality;
    }

    public function setModality(string $modality): self
    {
        $this->modality = $modality;

        return $this;
    }

    public function getProgram(): ?string
    {
        return $this->program;
    }

    public function setProgram(?string $program): self
    {
        $this->program = $program;

        return $this;
    }

    public function getInterweaveLocal(): ?string
    {
        return $this->interweaveLocal;
    }

    public function setInterweaveLocal(?string $interweaveLocal): self
    {
        $this->interweaveLocal = $interweaveLocal;

        return $this;
    }

    public function getAuthorizationCode(): ?string
    {
        return $this->authorizationCode;
    }

    public function setAuthorizationCode(?string $authorizationCode): self
    {
        $this->authorizationCode = $authorizationCode;

        return $this;
    }

    public function getNameImage(): ?string
    {
        return $this->nameImage;
    }

    public function setNameImage(?string $nameImage): self
    {
        $this->nameImage = $nameImage;

        return $this;
    }

    public function getEmbassador(): ?User
    {
        return $this->embassador;
    }

    public function setEmbassador(?User $embassador): self
    {
        $this->embassador = $embassador;

        return $this;
    }

    /**
     * @return Collection|StudentGroup[]
     */
    public function getStudentsgroup(): Collection
    {
        return $this->studentsgroup;
    }

    public function addStudentsgroup(StudentGroup $studentsgroup): self
    {
        if (!$this->studentsgroup->contains($studentsgroup)) {
            $this->studentsgroup[] = $studentsgroup;
            $studentsgroup->setGroup($this);
        }

        return $this;
    }

    public function removeStudentsgroup(StudentGroup $studentsgroup): self
    {
        if ($this->studentsgroup->contains($studentsgroup)) {
            $this->studentsgroup->removeElement($studentsgroup);
            // set the owning side to null (unless already changed)
            if ($studentsgroup->getGroup() === $this) {
                $studentsgroup->setGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|StudentAmbassadorGroup[]
     */
    public function getStudentsambassadorgroup(): Collection
    {
        return $this->studentsambassadorgroup;
    }

    public function addStudentsambassadorgroup(StudentAmbassadorGroup $studentsambassadorgroup): self
    {
        if (!$this->studentsambassadorgroup->contains($studentsambassadorgroup)) {
            $this->studentsambassadorgroup[] = $studentsambassadorgroup;
            $studentsambassadorgroup->setGroup($this);
        }

        return $this;
    }

    public function removeStudentsambassadorgroup(StudentAmbassadorGroup $studentsambassadorgroup): self
    {
        if ($this->studentsambassadorgroup->contains($studentsambassadorgroup)) {
            $this->studentsambassadorgroup->removeElement($studentsambassadorgroup);
            // set the owning side to null (unless already changed)
            if ($studentsambassadorgroup->getGroup() === $this) {
                $studentsambassadorgroup->setGroup(null);
            }
        }

        return $this;
    }



   
}
