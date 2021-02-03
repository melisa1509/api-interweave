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
 * Grant
 *
 * @ORM\Table(name="`grant`")
 * @ORM\Entity(repositoryClass="App\Repository\GrantRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Grant
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
     * @ORM\Column(name="participants_number", type="integer") 
     */
    protected $participantsNumber;

    /**
     * @ORM\Column(name="amount", type="integer") 
     */
    protected $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;   

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="grants")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     */
    private $embassador;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\GrantUpdate", mappedBy="grant")
    * @Serializer\Exclude()
    */
    private $grantupdates;

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

    public function getParticipantsNumber(): ?int
    {
        return $this->participantsNumber;
    }

    public function setParticipantsNumber(int $participantsNumber): self
    {
        $this->participantsNumber = $participantsNumber;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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
}