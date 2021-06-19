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
 * User
 *
 * @ORM\Table(name="user");
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository");
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
{
    /**
     * @ORM\Column(name="id", type="integer") 
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"student_list", "future_ambassador", "student_group"})
     */
    protected $id;


    /**
     * @ORM\Column(name="username", type="string", length=255)
     * @Groups({"student_list", "future_ambassador"})
     */
    protected $username;

    protected $salt;

    /**
     * @ORM\Column(name="password", type="string", length=255)
     * @Serializer\Exclude()
     */
    protected $password;

    /**
     * @var string
     */
    protected $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=255)
     * @Groups({"student_list"})
     * 
     */
    private $language;

     /**
     * @var string
     *
     * @ORM\Column(name="language_grader", type="json_array", nullable=true)
     */
    private $languageGrader = array();

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="json_array", nullable=true)
     */
    private $message = array();

     /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     * @Groups({"student_list", "future_ambassador", "student_group", "group_list", "grant_ambassador_list"})
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     * @Groups({"student_list", "future_ambassador", "student_group", "group_list", "grant_ambassador_list" })
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", nullable=true, length=255)
     * @Groups({"student_list", "student_group"})
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", nullable=true,  length=255)
     * @Groups({"student_list"})
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", nullable=true,  length=255)
     */
    private $picture;

    /**
     * @var string
     *
     * @ORM\Column(name="whatsapp", nullable=true, type="string",  length=255)
     * @Groups({"student_list"})
     */
    private $whatsapp;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json_array")
     * @Groups({"student_list"})
     */
    protected $roles = [];

    /**
     * @var string
     *
     * @ORM\Column(name="code", nullable=true, type="string", length=255 )
     * @Groups({"student_list"})
     */
    private $code;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\Groupe", mappedBy="embassador")
    * @Serializer\Exclude()
    */
   private $groupes;

   /**
    * @ORM\OneToMany(targetEntity="App\Entity\GrantUpdate", mappedBy="user")
    * @Serializer\Exclude()
    */
    private $grantupdates;

   /**
    * @ORM\OneToMany(targetEntity="App\Entity\Grant", mappedBy="administrator")
    * @Serializer\Exclude()
    */
    private $grants;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\GrantAmbassador", mappedBy="ambassador")
    * @Serializer\Exclude()
    */
    private $grantsambassador;

   /**
    * @ORM\OneToOne(targetEntity="App\Entity\StudentGroup", mappedBy="student")
    * @Groups({"future_ambassador", "student_list", "student_group"})
    */
   private $studentgroup;

   /**
    * @ORM\OneToOne(targetEntity="App\Entity\StudentAmbassadorGroup", mappedBy="student")
    * @Groups({"student_list"})
    */
   private $studentambassadorgroup;

   /** 
    * @ORM\OneToOne(targetEntity="App\Entity\Evaluation", mappedBy="student")
    * @Groups({"student_list", "student_group"})
    */
   private $evaluation;

   /**
    * @ORM\OneToOne(targetEntity="App\Entity\ProgramMbs", mappedBy="student")
    * @Groups({"student_list", "student_group", "future_ambassador"})
    */
   private $programmbs;

   /**
    * @ORM\OneToOne(targetEntity="App\Entity\ProgramSa", mappedBy="student")
    * @Groups({"student_list", "student_group", "future_ambassador"})
    */
   private $programsa;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     * @Groups({"student_list"})
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     * @Groups({"student_list"})
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Board", mappedBy="user")
     */
    protected $boards;

    public function __construct()
    {
        $this->boards = new ArrayCollection();
        $this->groupes = new ArrayCollection();
        $this->grants = new ArrayCollection();
        $this->grantsambassador = new ArrayCollection();
        $this->grantupdates = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

   
    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        $this->password = null;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function getSalt() {}

    public function eraseCredentials() {}

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $dateTimeNow = new DateTime('now');
        $this->setUpdatedAt($dateTimeNow);
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt($dateTimeNow);
        }
    }

    /**
     * @return mixed
     */
    public function getBoards()
    {
        return $this->boards->toArray();
    }

    public function addBoard(Board $board): self
    {
        if (!$this->boards->contains($board)) {
            $this->boards[] = $board;
            $board->setUser($this);
        }

        return $this;
    }

    public function removeBoard(Board $board): self
    {
        if ($this->boards->contains($board)) {
            $this->boards->removeElement($board);
            // set the owning side to null (unless already changed)
            if ($board->getUser() === $this) {
                $board->setUser(null);
            }
        }

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getLanguageGrader(): ?array
    {
        return $this->languageGrader;
    }

    public function setLanguageGrader(array $languageGrader): self
    {
        $this->languageGrader = $languageGrader;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getWhatsapp(): ?string
    {
        return $this->whatsapp;
    }

    public function setWhatsapp(?string $whatsapp): self
    {
        $this->whatsapp = $whatsapp;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection|Groupe[]
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupe $groupe): self
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes[] = $groupe;
            $groupe->setEmbassador($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): self
    {
        if ($this->groupes->contains($groupe)) {
            $this->groupes->removeElement($groupe);
            // set the owning side to null (unless already changed)
            if ($groupe->getEmbassador() === $this) {
                $groupe->setEmbassador(null);
            }
        }

        return $this;
    }

    public function getStudentgroup(): ?StudentGroup
    {
        return $this->studentgroup;
    }

    public function setStudentgroup(?StudentGroup $studentgroup): self
    {
        $this->studentgroup = $studentgroup;

        // set (or unset) the owning side of the relation if necessary
        $newStudent = null === $studentgroup ? null : $this;
        if ($studentgroup->getStudent() !== $newStudent) {
            $studentgroup->setStudent($newStudent);
        }

        return $this;
    }

    public function getStudentambassadorgroup(): ?StudentAmbassadorGroup
    {
        return $this->studentambassadorgroup;
    }

    public function setStudentambassadorgroup(?StudentAmbassadorGroup $studentambassadorgroup): self
    {
        $this->studentambassadorgroup = $studentambassadorgroup;

        // set (or unset) the owning side of the relation if necessary
        $newStudent = null === $studentambassadorgroup ? null : $this;
        if ($studentambassadorgroup->getStudent() !== $newStudent) {
            $studentambassadorgroup->setStudent($newStudent);
        }

        return $this;
    }

    public function getEvaluation(): ?Evaluation
    {
        return $this->evaluation;
    }

    public function setEvaluation(?Evaluation $evaluation): self
    {
        $this->evaluation = $evaluation;

        // set (or unset) the owning side of the relation if necessary
        $newStudent = null === $evaluation ? null : $this;
        if ($evaluation->getStudent() !== $newStudent) {
            $evaluation->setStudent($newStudent);
        }

        return $this;
    }

    public function getProgrammbs(): ?ProgramMbs
    {
        return $this->programmbs;
    }

    public function setProgrammbs(?ProgramMbs $programmbs): self
    {
        $this->programmbs = $programmbs;

        // set (or unset) the owning side of the relation if necessary
        $newStudent = null === $programmbs ? null : $this;
        if ($programmbs->getStudent() !== $newStudent) {
            $programmbs->setStudent($newStudent);
        }

        return $this;
    }

    public function getProgramsa(): ?ProgramSa
    {
        return $this->programsa;
    }

    public function setProgramsa(?ProgramSa $programsa): self
    {
        $this->programsa = $programsa;

        // set (or unset) the owning side of the relation if necessary
        $newStudent = null === $programsa ? null : $this;
        if ($programsa->getStudent() !== $newStudent) {
            $programsa->setStudent($newStudent);
        }

        return $this;
    }

    /**
     * @return Collection|Grant[]
     */
    public function getGrants(): Collection
    {
        return $this->grants;
    }

    public function addGrant(Grant $grant): self
    {
        if (!$this->grants->contains($grant)) {
            $this->grants[] = $grant;
            $grant->setEmbassador($this);
        }

        return $this;
    }

    public function removeGrant(Grant $grant): self
    {
        if ($this->grants->contains($grant)) {
            $this->grants->removeElement($grant);
            // set the owning side to null (unless already changed)
            if ($grant->getEmbassador() === $this) {
                $grant->setEmbassador(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GrantAmbassador[]
     */
    public function getGrantsambassador(): Collection
    {
        return $this->grantsambassador;
    }

    public function addGrantsambassador(GrantAmbassador $grantsambassador): self
    {
        if (!$this->grantsambassador->contains($grantsambassador)) {
            $this->grantsambassador[] = $grantsambassador;
            $grantsambassador->setAmbassador($this);
        }

        return $this;
    }

    public function removeGrantsambassador(GrantAmbassador $grantsambassador): self
    {
        if ($this->grantsambassador->contains($grantsambassador)) {
            $this->grantsambassador->removeElement($grantsambassador);
            // set the owning side to null (unless already changed)
            if ($grantsambassador->getAmbassador() === $this) {
                $grantsambassador->setAmbassador(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GrantUpdate[]
     */
    public function getGrantupdates(): Collection
    {
        return $this->grantupdates;
    }

    public function addGrantupdate(GrantUpdate $grantupdate): self
    {
        if (!$this->grantupdates->contains($grantupdate)) {
            $this->grantupdates[] = $grantupdate;
            $grantupdate->setUser($this);
        }

        return $this;
    }

    public function removeGrantupdate(GrantUpdate $grantupdate): self
    {
        if ($this->grantupdates->contains($grantupdate)) {
            $this->grantupdates->removeElement($grantupdate);
            // set the owning side to null (unless already changed)
            if ($grantupdate->getUser() === $this) {
                $grantupdate->setUser(null);
            }
        }

        return $this;
    }

    public function getMessage(): ?array
    {
        return $this->message;
    }

    public function setMessage(?array $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    

}
