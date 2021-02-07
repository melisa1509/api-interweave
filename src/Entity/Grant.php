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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=255)
     */
    private $language;

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
    private $administrator;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\GrantAmbassador", mappedBy="ambassador")
    * @Serializer\Exclude()
    */
    private $grantsambassador;

    
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
        $this->grantsambassador = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAdministrator(): ?User
    {
        return $this->administrator;
    }

    public function setAdministrator(?User $administrator): self
    {
        $this->administrator = $administrator;

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

  
}