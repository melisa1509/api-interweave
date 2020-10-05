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
 * Evaluation
 *
 * @ORM\Table(name="evaluation")
 * @ORM\Entity(repositoryClass="App\Repository\EvaluationRepository");
 * @ORM\HasLifecycleCallbacks()
 */
class Evaluation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"student_group"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="question1", type="string", length=255)
     * @Groups({"student_group"})
     */
    private $question1;

    /**
     * @var string
     *
     * @ORM\Column(name="question2", type="string", length=255)
     * @Groups({"student_group"})
     */
    private $question2;

    /**
     * @var string
     *
     * @ORM\Column(name="question3", type="string", length=255)
     * @Groups({"student_group"})
     */
    private $question3;

    /**
     * @var string
     *
     * @ORM\Column(name="question4", type="string", length=255)
     * @Groups({"student_group"})
     */
    private $question4;

    /**
     * @var string
     *
     * @ORM\Column(name="question5", type="string", length=255)
     * @Groups({"student_group"})
     */
    private $question5;

    /**
     * @var string
     *
     * @ORM\Column(name="question6", type="string", length=255)
     * @Groups({"student_group"})
     */
    private $question6;

    /**
     * @var string
     *
     * @ORM\Column(name="question7", type="string", length=255)
     * @Groups({"student_group"})
     */
    private $question7;

    /**
     * @var string
     *
     * @ORM\Column(name="postquestion1", nullable=true, type="string", length=255)
     * @Groups({"student_group"})
     */
    private $postquestion1;

    /**
     * @var string
     *
     * @ORM\Column(name="postquestion2", nullable=true, type="string", length=255)
     * @Groups({"student_group"})
     */
    private $postquestion2;

    /**
     * @var string
     *
     * @ORM\Column(name="postquestion3", nullable=true, type="string", length=255)
     * @Groups({"student_group"})
     */
    private $postquestion3;

    /**
     * @var string
     *
     * @ORM\Column(name="postquestion4", nullable=true, type="string", length=255)
     * @Groups({"student_group"})
     */
    private $postquestion4;

    /**
     * @var string
     *
     * @ORM\Column(name="postquestion5", nullable=true, type="string", length=255)
     * @Groups({"student_group"})
     */
    private $postquestion5;

    /**
     * @var string
     *
     * @ORM\Column(name="postquestion6", nullable=true, type="string", length=255)
     * @Groups({"student_group"})
     */
    private $postquestion6;

    /**
     * @var string
     *
     * @ORM\Column(name="postquestion7", nullable=true, type="string", length=255)
     * @Groups({"student_group"})
     */
    private $postquestion7;

    /** 
     * @var string
     *
     * @ORM\Column(name="postquestion8", nullable=true, type="string", length=255)
     * @Groups({"student_group"})
     */
    private $postquestion8;

    /**
     * @var string
     *
     * @ORM\Column(name="postquestion9", nullable=true, type="string", length=255)
     * @Groups({"student_group"})
     */
    private $postquestion9;

    /**
     * @var string
     *
     * @ORM\Column(name="postquestion10", nullable=true, type="string", length=255)
     * @Groups({"student_group"})
     */
    private $postquestion10;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="evaluation")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", onDelete="CASCADE")
     * @Serializer\Exclude()
     */     
    private $student;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion1(): ?string
    {
        return $this->question1;
    }

    public function setQuestion1(string $question1): self
    {
        $this->question1 = $question1;

        return $this;
    }

    public function getQuestion2(): ?string
    {
        return $this->question2;
    }

    public function setQuestion2(string $question2): self
    {
        $this->question2 = $question2;

        return $this;
    }

    public function getQuestion3(): ?string
    {
        return $this->question3;
    }

    public function setQuestion3(string $question3): self
    {
        $this->question3 = $question3;

        return $this;
    }

    public function getQuestion4(): ?string
    {
        return $this->question4;
    }

    public function setQuestion4(string $question4): self
    {
        $this->question4 = $question4;

        return $this;
    }

    public function getQuestion5(): ?string
    {
        return $this->question5;
    }

    public function setQuestion5(string $question5): self
    {
        $this->question5 = $question5;

        return $this;
    }

    public function getQuestion6(): ?string
    {
        return $this->question6;
    }

    public function setQuestion6(string $question6): self
    {
        $this->question6 = $question6;

        return $this;
    }

    public function getQuestion7(): ?string
    {
        return $this->question7;
    }

    public function setQuestion7(string $question7): self
    {
        $this->question7 = $question7;

        return $this;
    }

    public function getPostquestion1(): ?string
    {
        return $this->postquestion1;
    }

    public function setPostquestion1(?string $postquestion1): self
    {
        $this->postquestion1 = $postquestion1;

        return $this;
    }

    public function getPostquestion2(): ?string
    {
        return $this->postquestion2;
    }

    public function setPostquestion2(?string $postquestion2): self
    {
        $this->postquestion2 = $postquestion2;

        return $this;
    }

    public function getPostquestion3(): ?string
    {
        return $this->postquestion3;
    }

    public function setPostquestion3(?string $postquestion3): self
    {
        $this->postquestion3 = $postquestion3;

        return $this;
    }

    public function getPostquestion4(): ?string
    {
        return $this->postquestion4;
    }

    public function setPostquestion4(?string $postquestion4): self
    {
        $this->postquestion4 = $postquestion4;

        return $this;
    }

    public function getPostquestion5(): ?string
    {
        return $this->postquestion5;
    }

    public function setPostquestion5(?string $postquestion5): self
    {
        $this->postquestion5 = $postquestion5;

        return $this;
    }

    public function getPostquestion6(): ?string
    {
        return $this->postquestion6;
    }

    public function setPostquestion6(?string $postquestion6): self
    {
        $this->postquestion6 = $postquestion6;

        return $this;
    }

    public function getPostquestion7(): ?string
    {
        return $this->postquestion7;
    }

    public function setPostquestion7(?string $postquestion7): self
    {
        $this->postquestion7 = $postquestion7;

        return $this;
    }

    public function getPostquestion8(): ?string
    {
        return $this->postquestion8;
    }

    public function setPostquestion8(?string $postquestion8): self
    {
        $this->postquestion8 = $postquestion8;

        return $this;
    }

    public function getPostquestion9(): ?string
    {
        return $this->postquestion9;
    }

    public function setPostquestion9(?string $postquestion9): self
    {
        $this->postquestion9 = $postquestion9;

        return $this;
    }

    public function getPostquestion10(): ?string
    {
        return $this->postquestion10;
    }

    public function setPostquestion10(?string $postquestion10): self
    {
        $this->postquestion10 = $postquestion10;

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


   
}
