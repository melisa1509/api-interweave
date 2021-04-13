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
 * GrantAmbassador
 *
 * @ORM\Table(name="`grant_ambassador`")
 * @ORM\Entity(repositoryClass="App\Repository\GrantAmbassadorRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class GrantAmbassador
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"grant_ambassador_list"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255)
     * @Groups({"grant_ambassador_list"})
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="grantsambassador")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * @Groups({"grant_ambassador_list"})
     * 
     */
    private $ambassador;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Grant", inversedBy="grantsambassador")
     * @ORM\JoinColumn(name="grant_id", referencedColumnName="id", onDelete="CASCADE")
     * @Groups({"grant_ambassador_list"})
     * 
     */
    private $grant;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\GrantUpdate", mappedBy="grant")
    * @Serializer\Exclude()
    */
    private $grantupdates;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\GrantGroup", mappedBy="grantambassador")
    * @Serializer\Exclude()
    */
    private $grantgroups;

    /**
     * @ORM\Column(name="amount", type="integer") 
     */
    protected $amount;

    /**
     * @ORM\Column(name="number", type="integer") 
     */
    protected $number;
   
    /**
     * @var string
     *
     * @ORM\Column(name="question1", type="text", nullable=true)
     */
    private $question1;

    /**
     * @var string
     *
     * @ORM\Column(name="question2", type="text", nullable=true)
     */
    private $question2;

    /**
     * @var string
     *
     * @ORM\Column(name="question3", type="text", nullable=true)
     */
    private $question3;

    /**
     * @var string
     *
     * @ORM\Column(name="question4", type="text", nullable=true)
     */
    private $question4;

    /**
     * @var string
     *
     * @ORM\Column(name="question5", type="text", nullable=true)
     */
    private $question5;

    /**
     * @var string
     *
     * @ORM\Column(name="question6", type="text", nullable=true)
     */
    private $question6;

    /**
     * @var string
     *
     * @ORM\Column(name="question7", type="text", nullable=true)
     */
    private $question7;

    /**
     * @var string
     *
     * @ORM\Column(name="question8", type="text", nullable=true)
     */
    private $question8;

    /**
     * @var string
     *
     * @ORM\Column(name="question9", type="text", nullable=true)
     */
    private $question9;

    /**
     * @var string
     *
     * @ORM\Column(name="question10", type="text", nullable=true)
     */
    private $question10;

    /**
     * @var string
     *
     * @ORM\Column(name="question11", type="text", nullable=true)
     */
    private $question11;

    /**
     * @var string
     *
     * @ORM\Column(name="question12", type="text", nullable=true)
     */
    private $question12;

    /**
     * @var string
     *
     * @ORM\Column(name="question13", type="text", nullable=true)
     */
    private $question13;

    /**
     * @var string
     *
     * @ORM\Column(name="question14", type="text", nullable=true)
     */
    private $question14;

    /**
     * @var string
     *
     * @ORM\Column(name="question15", type="text", nullable=true)
     */
    private $question15;

    /**
     * @var string
     *
     * @ORM\Column(name="correction", type="text", nullable=true)
     */
    private $correction;
    
    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=255)
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="file2", type="string", length=255)
     */
    private $file2;

    /**
     * @var string
     *
     * @ORM\Column(name="file3", type="string", length=255)
     */
    private $file3;

    /**
     * @var string
     *
     * @ORM\Column(name="file4", type="string", length=255)
     */
    private $file4;

    /**
     * @var string
     *
     * @ORM\Column(name="file5", type="string", length=255)
     */
    private $file5;

    /**
     * @var string
     *
     * @ORM\Column(name="file6", type="string", length=255)
     */
    private $file6;

    /**
     * @var string
     *
     * @ORM\Column(name="file7", type="string", length=255)
     */
    private $file7;
    

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->grantupdates = new ArrayCollection();
        $this->grantgroups = new ArrayCollection();
    }


    public function __toString(){
            return $this->name;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getQuestion1(): ?string
    {
        return $this->question1;
    }

    public function setQuestion1(?string $question1): self
    {
        $this->question1 = $question1;

        return $this;
    }

    public function getQuestion2(): ?string
    {
        return $this->question2;
    }

    public function setQuestion2(?string $question2): self
    {
        $this->question2 = $question2;

        return $this;
    }

    public function getQuestion3(): ?string
    {
        return $this->question3;
    }

    public function setQuestion3(?string $question3): self
    {
        $this->question3 = $question3;

        return $this;
    }

    public function getQuestion4(): ?string
    {
        return $this->question4;
    }

    public function setQuestion4(?string $question4): self
    {
        $this->question4 = $question4;

        return $this;
    }

    public function getQuestion5(): ?string
    {
        return $this->question5;
    }

    public function setQuestion5(?string $question5): self
    {
        $this->question5 = $question5;

        return $this;
    }

    public function getQuestion6(): ?string
    {
        return $this->question6;
    }

    public function setQuestion6(?string $question6): self
    {
        $this->question6 = $question6;

        return $this;
    }

    public function getQuestion7(): ?string
    {
        return $this->question7;
    }

    public function setQuestion7(?string $question7): self
    {
        $this->question7 = $question7;

        return $this;
    }

    public function getCorrection(): ?string
    {
        return $this->correction;
    }

    public function setCorrection(?string $correction): self
    {
        $this->correction = $correction;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getAmbassador(): ?User
    {
        return $this->ambassador;
    }

    public function setAmbassador(?User $ambassador): self
    {
        $this->ambassador = $ambassador;

        return $this;
    }

    public function getGrant(): ?Grant
    {
        return $this->grant;
    }

    public function setGrant(?Grant $grant): self
    {
        $this->grant = $grant;

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
            $grantupdate->setGrant($this);
        }

        return $this;
    }

    public function removeGrantupdate(GrantUpdate $grantupdate): self
    {
        if ($this->grantupdates->contains($grantupdate)) {
            $this->grantupdates->removeElement($grantupdate);
            // set the owning side to null (unless already changed)
            if ($grantupdate->getGrant() === $this) {
                $grantupdate->setGrant(null);
            }
        }

        return $this;
    }

    public function getFile2(): ?string
    {
        return $this->file2;
    }

    public function setFile2(string $file2): self
    {
        $this->file2 = $file2;

        return $this;
    }

    /**
     * @return Collection|GrantGroup[]
     */
    public function getGrantgroups(): Collection
    {
        return $this->grantgroups;
    }

    public function addGrantgroup(GrantGroup $grantgroup): self
    {
        if (!$this->grantgroups->contains($grantgroup)) {
            $this->grantgroups[] = $grantgroup;
            $grantgroup->setGrantambassador($this);
        }

        return $this;
    }

    public function removeGrantgroup(GrantGroup $grantgroup): self
    {
        if ($this->grantgroups->contains($grantgroup)) {
            $this->grantgroups->removeElement($grantgroup);
            // set the owning side to null (unless already changed)
            if ($grantgroup->getGrantambassador() === $this) {
                $grantgroup->setGrantambassador(null);
            }
        }

        return $this;
    }

    public function getQuestion8(): ?string
    {
        return $this->question8;
    }

    public function setQuestion8(?string $question8): self
    {
        $this->question8 = $question8;

        return $this;
    }

    public function getQuestion9(): ?string
    {
        return $this->question9;
    }

    public function setQuestion9(?string $question9): self
    {
        $this->question9 = $question9;

        return $this;
    }

    public function getQuestion10(): ?string
    {
        return $this->question10;
    }

    public function setQuestion10(?string $question10): self
    {
        $this->question10 = $question10;

        return $this;
    }

    public function getQuestion11(): ?string
    {
        return $this->question11;
    }

    public function setQuestion11(?string $question11): self
    {
        $this->question11 = $question11;

        return $this;
    }

    public function getQuestion12(): ?string
    {
        return $this->question12;
    }

    public function setQuestion12(?string $question12): self
    {
        $this->question12 = $question12;

        return $this;
    }

    public function getQuestion13(): ?string
    {
        return $this->question13;
    }

    public function setQuestion13(?string $question13): self
    {
        $this->question13 = $question13;

        return $this;
    }

    public function getQuestion14(): ?string
    {
        return $this->question14;
    }

    public function setQuestion14(?string $question14): self
    {
        $this->question14 = $question14;

        return $this;
    }

    public function getQuestion15(): ?string
    {
        return $this->question15;
    }

    public function setQuestion15(?string $question15): self
    {
        $this->question15 = $question15;

        return $this;
    }

    public function getFile3(): ?string
    {
        return $this->file3;
    }

    public function setFile3(string $file3): self
    {
        $this->file3 = $file3;

        return $this;
    }

    public function getFile4(): ?string
    {
        return $this->file4;
    }

    public function setFile4(string $file4): self
    {
        $this->file4 = $file4;

        return $this;
    }

    public function getFile5(): ?string
    {
        return $this->file5;
    }

    public function setFile5(string $file5): self
    {
        $this->file5 = $file5;

        return $this;
    }

    public function getFile6(): ?string
    {
        return $this->file6;
    }

    public function setFile6(string $file6): self
    {
        $this->file6 = $file6;

        return $this;
    }

    public function getFile7(): ?string
    {
        return $this->file7;
    }

    public function setFile7(string $file7): self
    {
        $this->file7 = $file7;

        return $this;
    }

   
}