<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * Unit
 *
 * @ORM\Table(name="unit");
 * @ORM\Entity(repositoryClass="App\Repository\UnitRepository");
 * @ORM\HasLifecycleCallbacks()
 */
class Unit
{
    /**
     * @ORM\Column(name="id", type="integer") 
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", length=150)
     */
    protected $name;

   /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Course", inversedBy="units")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     * @Serializer\Exclude()
     */
    protected $course;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="text", nullable=true)
     */
    private $state;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sheet", mappedBy="unit")
     */
    protected $sheets;

    public function __construct()
    {
        $this->sheets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    /**
     * @return Collection|Sheet[]
     */
    public function getSheets(): Collection
    {
        return $this->sheets;
    }

    public function addSheet(Sheet $sheet): self
    {
        if (!$this->sheets->contains($sheet)) {
            $this->sheets[] = $sheet;
            $sheet->setUnit($this);
        }

        return $this;
    }

    public function removeSheet(Sheet $sheet): self
    {
        if ($this->sheets->contains($sheet)) {
            $this->sheets->removeElement($sheet);
            // set the owning side to null (unless already changed)
            if ($sheet->getUnit() === $this) {
                $sheet->setUnit(null);
            }
        }

        return $this;
    }

}