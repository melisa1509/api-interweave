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
 * GrantGroup
 *
 * @ORM\Table(name="`grant_group`")
 * @ORM\Entity(repositoryClass="App\Repository\GrantGroupRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class GrantGroup
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
     * @ORM\ManyToOne(targetEntity="App\Entity\GrantAmbassador", inversedBy="grantgroups")
     * @ORM\JoinColumn(name="grant_ambassador_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     */
    private $grantambassador;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Groupe", inversedBy="grantgroups")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     * 
     */
    private $group;

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

    public function getGrantambassador(): ?GrantAmbassador
    {
        return $this->grantambassador;
    }

    public function setGrantambassador(?GrantAmbassador $grantambassador): self
    {
        $this->grantambassador = $grantambassador;

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