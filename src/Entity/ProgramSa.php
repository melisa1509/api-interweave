<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Groups;

/**
 * ProgramSa
 *
 * @ORM\Table(name="program_sa")
 * @ORM\Entity(repositoryClass="App\Repository\ProgramSaRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ProgramSa
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"student_list", "future_ambassador", "student_group"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="plan1", type="text", nullable=true)
     */
    private $plan1;

    /**
     * @var string
     *
     * @ORM\Column(name="plan2", type="text", nullable=true)
     */
    private $plan2;

    /**
     * @var string
     *
     * @ORM\Column(name="product1", type="text", nullable=true)
     */
    private $product1;

    /**
     * @var string
     *
     * @ORM\Column(name="product2", type="text", nullable=true)
     */
    private $product2;

    /**
     * @var string
     *
     * @ORM\Column(name="product3", type="text", nullable=true)
     */
    private $product3;

    /**
     * @var string
     *
     * @ORM\Column(name="product4", type="text", nullable=true)
     */
    private $product4;

    /**
     * @var string
     *
     * @ORM\Column(name="product5", type="text", nullable=true)
     */
    private $product5;

    /**
     * @var string
     *
     * @ORM\Column(name="product6", type="text", nullable=true)
     */
    private $product6;

    /**
     * @var string
     *
     * @ORM\Column(name="product7", type="text", nullable=true)
     */
    private $product7;

    /**
     * @var string
     *
     * @ORM\Column(name="process1", type="json_array")
     */
    private $process1;

    /**
     * @var string
     *
     * @ORM\Column(name="process2", type="text", nullable=true)
     */
    private $process2;

    /**
     * @var string
     *
     * @ORM\Column(name="process3", type="text", nullable=true)
     */
    private $process3;

    /**
     * @var string
     *
     * @ORM\Column(name="process4", type="text", nullable=true)
     */
    private $process4;

    /**
     * @var string
     *
     * @ORM\Column(name="price1", type="text", nullable=true)
     */
    private $price1;

    /**
     * @var string
     *
     * @ORM\Column(name="price2", type="text", nullable=true)
     */
    private $price2;

    /**
     * @var string
     *
     * @ORM\Column(name="price3", type="text", nullable=true)
     */
    private $price3;

    /**
     * @var string
     *
     * @ORM\Column(name="price4", type="text", nullable=true)
     */
    private $price4;

    /**
     * @var string
     *
     * @ORM\Column(name="promotion1", type="text", nullable=true)
     */
    private $promotion1;

    /**
     * @var string
     *
     * @ORM\Column(name="promotion2", type="text", nullable=true)
     */
    private $promotion2;

    /**
     * @var string
     *
     * @ORM\Column(name="promotion3", type="text", nullable=true)
     */
    private $promotion3;

    /**
     * @var string
     *
     * @ORM\Column(name="promotion4", type="text", nullable=true)
     */
    private $promotion4;

    /**
     * @var string
     *
     * @ORM\Column(name="promotion5", type="text", nullable=true)
     */
    private $promotion5;

    /**
     * @var string
     *
     * @ORM\Column(name="paperwork1", type="text", nullable=true)
     */
    private $paperwork1;

    /**
     * @var string
     *
     * @ORM\Column(name="paperwork2", type="text", nullable=true, length=255)
     */
    private $paperwork2;

    /**
     * @var string
     *
     * @ORM\Column(name="paperwork3", type="json_array")
     */
    private $paperwork3;

    /**
     * @var string
     *
     * @ORM\Column(name="paperwork4", type="json_array")
     */
    private $paperwork4 = array();

    /**
     * @var string
     *
     * @ORM\Column(name="paperwork5", type="json_array")
     */
    private $paperwork5 = array();

    /**
     * @var string
     *
     * @ORM\Column(name="paperwork6", type="json_array")
     */
    private $paperwork6 = array();

    /**
     * @var string
     *
     * @ORM\Column(name="paperwork7", type="json_array")
     */
    private $paperwork7 = array();

    /**
     * @var string
     *
     * @ORM\Column(name="paperwork8", type="json_array")
     */
    private $paperwork8 = array();


    /**
     * @var string
     *
     * @ORM\Column(name="qualityp1", type="string", nullable=true, length=255)
     */
    private $qualityP1;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityp2", type="string", nullable=true, length=255)
     */
    private $qualityP2;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityp3", type="string", nullable=true, length=255)
     */
    private $qualityP3;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityp4", type="string", nullable=true, length=255)
     */
    private $qualityP4;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityp5", type="string", nullable=true, length=255)
     */
    private $qualityP5;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityp6", type="string", nullable=true, length=255)
     */
    private $qualityP6;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityp7", type="string", nullable=true, length=255)
     */
    private $qualityP7;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityp8", type="string", nullable=true, length=255)
     */
    private $qualityP8;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityq1", type="string", nullable=true, length=255)
     */
    private $qualityQ1;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityq2", type="string", nullable=true, length=255)
     */
    private $qualityQ2;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityq3", type="string", nullable=true, length=255)
     */
    private $qualityQ3;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityq4", type="string", nullable=true, length=255)
     */
    private $qualityQ4;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityq5", type="string", nullable=true, length=255)
     */
    private $qualityQ5;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityq6", type="string", nullable=true, length=255)
     */
    private $qualityQ6;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityq7", type="string", nullable=true, length=255)
     */
    private $qualityQ7;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityq8", type="string", nullable=true, length=255)
     */
    private $qualityQ8;


    /**
     * @var string
     *
     * @ORM\Column(name="qualityg1", type="text", nullable=true)
     */
    private $qualityG1;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityg2", type="text", nullable=true)
     */
    private $qualityG2;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityg3", type="text", nullable=true)
     */
    private $qualityG3;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityg4", type="text", nullable=true)
     */
    private $qualityG4;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityg5", type="text", nullable=true)
     */
    private $qualityG5;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityg6", type="text", nullable=true)
     */
    private $qualityG6;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityg7", type="text", nullable=true)
     */
    private $qualityG7;

    /**
     * @var string
     *
     * @ORM\Column(name="qualityg8", type="text", nullable=true)
     */
    private $qualityg8;

    /**
     * @var string
     *
     * @ORM\Column(name="service1", type="text", nullable=true)
     */
    private $service1;

    /**
     * @var string
     *
     * @ORM\Column(name="service2", type="text", nullable=true)
     */
    private $service2;

    /**
     * @var string
     *
     * @ORM\Column(name="service3", type="text", nullable=true)
     */
    private $service3;

    /**
     * @var string
     *
     * @ORM\Column(name="service4", type="text", nullable=true)
     */
    private $service4;

    /**
     * @var string
     *
     * @ORM\Column(name="service5", type="text", nullable=true)
     */
    private $service5;

    /**
     * @var string
     *
     * @ORM\Column(name="service6", type="string", nullable=true, length=255)
     */
    private $service6;

    /**
     * @var string
     *
     * @ORM\Column(name="mision1", type="text", nullable=true)
     */
    private $mision1;

    /**
     * @var string
     *
     * @ORM\Column(name="mision2", type="text", nullable=true)
     */
    private $mision2;

    /**
     * @var string
     *
     * @ORM\Column(name="mision3", type="text", nullable=true)
     */
    private $mision3;

    /**
     * @var string
     *
     * @ORM\Column(name="mision4", type="text", nullable=true)
     */
    private $mision4;

    /**
     * @var string
     *
     * @ORM\Column(name="generateGroups1", type="json_array")
     */
    private $generateGroups1 = array();

    /**
     * @var string
     *
     * @ORM\Column(name="generateGroups2", type="text", nullable=true)
     */
    private $generateGroups2;

    /**
     * @var string
     *
     * @ORM\Column(name="generateGroups3", type="json_array")
     */
    private $generateGroups3 = array();

    /**
     * @var string
     *
     * @ORM\Column(name="generateGroups4", type="text", nullable=true)
     */
    private $generateGroups4;

    /**
     * @var string
     *
     * @ORM\Column(name="generateGroups5", type="json_array")
     */
    private $generateGroups5 = array();

    /**
     * @var string
     *
     * @ORM\Column(name="generateGroups6", type="text", nullable=true)
     */
    private $generateGroups6;

    /**
     * @var string
     *
     * @ORM\Column(name="generateGroups7", type="text", nullable=true)
     */
    private $generateGroups7;

    /**
     * @var string
     *
     * @ORM\Column(name="rule1", type="text", nullable=true)
     */
    private $rule1;

    /**
     * @var string
     *
     * @ORM\Column(name="rule2", type="text", nullable=true)
     */
    private $rule2;

    /**
     * @var string
     *
     * @ORM\Column(name="rule3", type="text", nullable=true)
     */
    private $rule3;

    /**
     * @var string
     *
     * @ORM\Column(name="rule4", type="text", nullable=true)
     */
    private $rule4;

    /**
     * @var string
     *
     * @ORM\Column(name="rule5", type="text", nullable=true)
     */
    private $rule5;

    /**
     * @var string
     *
     * @ORM\Column(name="rule6", type="text", nullable=true)
     */
    private $rule6;

    /**
     * @var string
     *
     * @ORM\Column(name="rule7", type="text", nullable=true)
     */
    private $rule7;

    /**
     * @var string
     *
     * @ORM\Column(name="rule8", type="text", nullable=true)
     */
    private $rule8;


    /**
     * @var string
     *
     * @ORM\Column(name="rule9", type="json_array")
     */
    private $rule9 = array();

    /**
     * @var string
     *
     * @ORM\Column(name="rule10", type="text", nullable=true)
     */
    private $rule10;

    /**
     * @var string
     *
     * @ORM\Column(name="graduate1", type="text", nullable=true)
     */
    private $graduate1;

    /**
     * @var string
     *
     * @ORM\Column(name="graduate2", type="text", nullable=true)
     */
    private $graduate2;

    /**
     * @var string
     *
     * @ORM\Column(name="graduate3", type="text", nullable=true)
     */
    private $graduate3;

    /**
     * @var string
     *
     * @ORM\Column(name="graduate4", type="text", nullable=true)
     */
    private $graduate4;

    /**
     * @var string
     *
     * @ORM\Column(name="graduate5", type="json_array", nullable=true)
     */
    private $graduate5 = array();

    /**
     * @var string
     *
     * @ORM\Column(name="support1", type="text", nullable=true)
     */
    private $support1;

    /**
     * @var string
     *
     * @ORM\Column(name="support2", type="text", nullable=true)
     */
    private $support2;

    /**
     * @var string
     *
     * @ORM\Column(name="support3", type="text", nullable=true)
     */
    private $support3;



    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="programsa")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id", onDelete="CASCADE")
     * @Groups({"student_list"})
     */
    private $student;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", nullable=true, length=255)
     * @Groups({"student_list", "student_group", "future_ambassador"})
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="revisionplan", type="text", nullable=true)
     */
    private $revisionplan;

    /**
     * @var string
     *
     * @ORM\Column(name="revisionproduct", type="text", nullable=true)
     */
    private $revisionproduct;

    /**
     * @var string
     *
     * @ORM\Column(name="revisionprocess", type="text", nullable=true)
     */
    private $revisionprocess;

    /**
     * @var string
     *
     * @ORM\Column(name="revisionpromotion", type="text", nullable=true)
     */
    private $revisionpromotion;

    /**
     * @var string
     *
     * @ORM\Column(name="revisionprice", type="text", nullable=true)
     */
    private $revisionprice;

    /**
     * @var string
     *
     * @ORM\Column(name="revisionpaperwork", type="text", nullable=true)
     */
    private $revisionpaperwork;

    /**
     * @var string
     *
     * @ORM\Column(name="revisionquality", type="text", nullable=true)
     */
    private $revisionquality;

    /**
     * @var string
     *
     * @ORM\Column(name="revisionservice", type="text", nullable=true)
     */
    private $revisionservice;

    /**
     * @var string
     *
     * @ORM\Column(name="revisionmision", type="text", nullable=true)
     */
    private $revisionmision;

    /**
     * @var string
     *
     * @ORM\Column(name="revisiongenerategroups", type="text", nullable=true)
     */
    private $revisiongenerategroups;

    /**
     * @var string
     *
     * @ORM\Column(name="revisionrule", type="text", nullable=true)
     */
    private $revisionrule;

    /**
     * @var string
     *
     * @ORM\Column(name="revisiongraduate", type="text", nullable=true)
     */
    private $revisiongraduate;

    /**
     * @var string
     *
     * @ORM\Column(name="revisionsupport", type="text", nullable=true)
     */
    private $revisionsupport;

    /**
     * @var string
     *
     * @ORM\Column(name="statusplan", type="string", nullable=true, length=255)
     */
    private $statusplan;

    /**
     * @var string
     *
     * @ORM\Column(name="statusproduct", type="string", nullable=true, length=255)
     */
    private $statusproduct;

    /**
     * @var string
     *
     * @ORM\Column(name="statusprice", type="string", nullable=true, length=255)
     */
    private $statusprice;

    /**
     * @var string
     *
     * @ORM\Column(name="statuspromotion", type="string", nullable=true, length=255)
     */
    private $statuspromotion;

    /**
     * @var string
     *
     * @ORM\Column(name="statuspaperwork", type="string", nullable=true, length=255)
     */
    private $statuspaperwork;

    /**
     * @var string
     *
     * @ORM\Column(name="statusprocess", type="string", nullable=true, length=255)
     */
    private $statusprocess;

    /**
     * @var string
     *
     * @ORM\Column(name="statusquality", type="string", nullable=true, length=255)
     */
    private $statusquality;

    /**
     * @var string
     *
     * @ORM\Column(name="statusservice", type="string", nullable=true, length=255)
     */
    private $statusservice;

    /**
     * @var string
     *
     * @ORM\Column(name="statusmision", type="text", nullable=true)
     */
    private $statusmision;

    /**
     * @var string
     *
     * @ORM\Column(name="statusgenerategroups", type="text", nullable=true)
     */
    private $statusgenerategroups;

    /**
     * @var string
     *
     * @ORM\Column(name="statusrule", type="text", nullable=true)
     */
    private $statusrule;

    /**
     * @var string
     *
     * @ORM\Column(name="statusgraduate", type="text", nullable=true)
     */
    private $statusgraduate;

    /**
     * @var string
     *
     * @ORM\Column(name="statussupport", type="text", nullable=true)
     */
    private $statussupport;


    /**
     * @var string
     *
     * @ORM\Column(name="filestudent", type="string", nullable=true, length=255)
     */
    private $filestudent;

    /**
     * @var string
     *
     * @ORM\Column(name="fileambassador", type="string", nullable=true, length=255)
     */
    private $fileambassador;

    /**
     * @var string
     *
     * @ORM\Column(name="modality", type="string", nullable=true, length=255)
     * @Groups({"student_list", "future_ambassador"})
     */
    private $modality;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", nullable=true, length=255)
     */
    private $code;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="student_file_upload_date", nullable=true, type="datetime")
     */
    private $uploadDateStudent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="admin_file_upload_date", nullable=true, type="datetime")
     */
    private $uploadDateAdmin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="approval_date", nullable=true, type="date")
     */
    private $approvalDate;

    /**
     * @var string
     *
     * @ORM\Column(name="product_name", type="string", nullable=true, length=255)
     */
    private $productName;

    /**
     * @var string
     *
     * @ORM\Column(name="product_description", type="string", nullable=true, length=255)
     */
    private $productDescription;


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
     * Set plan1
     *
     * @param string $plan1
     *
     * @return ProgramSa
     */
    public function setPlan1($plan1)
    {
        $this->plan1 = $plan1;

        return $this;
    }

    /**
     * Get plan1
     *
     * @return string
     */
    public function getPlan1()
    {
        return $this->plan1;
    }

    /**
     * Set plan2
     *
     * @param string $plan2
     *
     * @return ProgramSa
     */
    public function setPlan2($plan2)
    {
        $this->plan2 = $plan2;

        return $this;
    }

    /**
     * Get plan2
     *
     * @return string
     */
    public function getPlan2()
    {
        return $this->plan2;
    }

    /**
     * Set product1
     *
     * @param string $product1
     *
     * @return ProgramSa
     */
    public function setProduct1($product1)
    {
        $this->product1 = $product1;

        return $this;
    }

    /**
     * Get product1
     *
     * @return string
     */
    public function getProduct1()
    {
        return $this->product1;
    }

    /**
     * Set product2
     *
     * @param string $product2
     *
     * @return ProgramSa
     */
    public function setProduct2($product2)
    {
        $this->product2 = $product2;

        return $this;
    }

    /**
     * Get product2
     *
     * @return string
     */
    public function getProduct2()
    {
        return $this->product2;
    }

    /**
     * Set product3
     *
     * @param string $product3
     *
     * @return ProgramSa
     */
    public function setProduct3($product3)
    {
        $this->product3 = $product3;

        return $this;
    }

    /**
     * Get product3
     *
     * @return string
     */
    public function getProduct3()
    {
        return $this->product3;
    }

    /**
     * Set product4
     *
     * @param string $product4
     *
     * @return ProgramSa
     */
    public function setProduct4($product4)
    {
        $this->product4 = $product4;

        return $this;
    }

    /**
     * Get product4
     *
     * @return string
     */
    public function getProduct4()
    {
        return $this->product4;
    }

    /**
     * Set product5
     *
     * @param string $product5
     *
     * @return ProgramSa
     */
    public function setProduct5($product5)
    {
        $this->product5 = $product5;

        return $this;
    }

    /**
     * Get product5
     *
     * @return string
     */
    public function getProduct5()
    {
        return $this->product5;
    }

    /**
     * Set product6
     *
     * @param string $product6
     *
     * @return ProgramSa
     */
    public function setProduct6($product6)
    {
        $this->product6 = $product6;

        return $this;
    }

    /**
     * Get product6
     *
     * @return string
     */
    public function getProduct6()
    {
        return $this->product6;
    }

    /**
     * Set product7
     *
     * @param string $product7
     *
     * @return ProgramSa
     */
    public function setProduct7($product7)
    {
        $this->product7 = $product7;

        return $this;
    }

    /**
     * Get product7
     *
     * @return string
     */
    public function getProduct7()
    {
        return $this->product7;
    }

    /**
     * Set process1
     *
     * @param string $process1
     *
     * @return ProgramSa
     */
    public function setProcess1($process1)
    {
        $this->process1 = $process1;

        return $this;
    }

    /**
     * Get process1
     *
     * @return string
     */
    public function getProcess1()
    {
        return $this->process1;
    }

    /**
     * Set process2
     *
     * @param string $process2
     *
     * @return ProgramSa
     */
    public function setProcess2($process2)
    {
        $this->process2 = $process2;

        return $this;
    }

    /**
     * Get process2
     *
     * @return string
     */
    public function getProcess2()
    {
        return $this->process2;
    }

    /**
     * Set process3
     *
     * @param string $process3
     *
     * @return ProgramSa
     */
    public function setProcess3($process3)
    {
        $this->process3 = $process3;

        return $this;
    }

    /**
     * Get process3
     *
     * @return string
     */
    public function getProcess3()
    {
        return $this->process3;
    }

    /**
     * Set process4
     *
     * @param string $process4
     *
     * @return ProgramSa
     */
    public function setProcess4($process4)
    {
        $this->process4 = $process4;

        return $this;
    }

    /**
     * Get process4
     *
     * @return string
     */
    public function getProcess4()
    {
        return $this->process4;
    }

    /**
     * Set price1
     *
     * @param string $price1
     *
     * @return ProgramSa
     */
    public function setPrice1($price1)
    {
        $this->price1 = $price1;

        return $this;
    }

    /**
     * Get price1
     *
     * @return string
     */
    public function getPrice1()
    {
        return $this->price1;
    }

    /**
     * Set price2
     *
     * @param string $price2
     *
     * @return ProgramSa
     */
    public function setPrice2($price2)
    {
        $this->price2 = $price2;

        return $this;
    }

    /**
     * Get price2
     *
     * @return string
     */
    public function getPrice2()
    {
        return $this->price2;
    }

    /**
     * Set price3
     *
     * @param string $price3
     *
     * @return ProgramSa
     */
    public function setPrice3($price3)
    {
        $this->price3 = $price3;

        return $this;
    }

    /**
     * Get price3
     *
     * @return string
     */
    public function getPrice3()
    {
        return $this->price3;
    }

    /**
     * Set price4
     *
     * @param string $price4
     *
     * @return ProgramSa
     */
    public function setPrice4($price4)
    {
        $this->price4 = $price4;

        return $this;
    }

    /**
     * Get price4
     *
     * @return string
     */
    public function getPrice4()
    {
        return $this->price4;
    }

    /**
     * Set promotion1
     *
     * @param string $promotion1
     *
     * @return ProgramSa
     */
    public function setPromotion1($promotion1)
    {
        $this->promotion1 = $promotion1;

        return $this;
    }

    /**
     * Get promotion1
     *
     * @return string
     */
    public function getPromotion1()
    {
        return $this->promotion1;
    }

    /**
     * Set promotion2
     *
     * @param string $promotion2
     *
     * @return ProgramSa
     */
    public function setPromotion2($promotion2)
    {
        $this->promotion2 = $promotion2;

        return $this;
    }

    /**
     * Get promotion2
     *
     * @return string
     */
    public function getPromotion2()
    {
        return $this->promotion2;
    }

    /**
     * Set promotion3
     *
     * @param string $promotion3
     *
     * @return ProgramSa
     */
    public function setPromotion3($promotion3)
    {
        $this->promotion3 = $promotion3;

        return $this;
    }

    /**
     * Get promotion3
     *
     * @return string
     */
    public function getPromotion3()
    {
        return $this->promotion3;
    }

    /**
     * Set promotion4
     *
     * @param string $promotion4
     *
     * @return ProgramSa
     */
    public function setPromotion4($promotion4)
    {
        $this->promotion4 = $promotion4;

        return $this;
    }

    /**
     * Get promotion4
     *
     * @return string
     */
    public function getPromotion4()
    {
        return $this->promotion4;
    }

    /**
     * Set promotion5
     *
     * @param string $promotion5
     *
     * @return ProgramSa
     */
    public function setPromotion5($promotion5)
    {
        $this->promotion5 = $promotion5;

        return $this;
    }

    /**
     * Get promotion5
     *
     * @return string
     */
    public function getPromotion5()
    {
        return $this->promotion5;
    }

    /**
     * Set paperwork1
     *
     * @param string $paperwork1
     *
     * @return ProgramSa
     */
    public function setPaperwork1($paperwork1)
    {
        $this->paperwork1 = $paperwork1;

        return $this;
    }

    /**
     * Get paperwork1
     *
     * @return string
     */
    public function getPaperwork1()
    {
        return $this->paperwork1;
    }

    /**
     * Set student
     *
     * @param \App\Entity\User $student
     *
     * @return ProgramSa
     */
    public function setStudent(\App\Entity\User $student = null)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Get student
     *
     * @return \App\Entity\User
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * Set paperwork2
     *
     * @param string $paperwork2
     *
     * @return ProgramSa
     */
    public function setPaperwork2($paperwork2)
    {
        $this->paperwork2 = $paperwork2;

        return $this;
    }

    /**
     * Get paperwork2
     *
     * @return string
     */
    public function getPaperwork2()
    {
        return $this->paperwork2;
    }

    /**
     * Set paperwork3
     *
     * @param string $paperwork3
     *
     * @return ProgramSa
     */
    public function setPaperwork3($paperwork3)
    {
        $this->paperwork3 = $paperwork3;

        return $this;
    }

    /**
     * Get paperwork3
     *
     * @return string
     */
    public function getPaperwork3()
    {
        return $this->paperwork3;
    }

    /**
     * Set paperwork4
     *
     * @param array $paperwork4
     *
     * @return ProgramSa
     */
    public function setPaperwork4($paperwork4)
    {
        $this->paperwork4 = $paperwork4;

        return $this;
    }

    /**
     * Get paperwork4
     *
     * @return array
     */
    public function getPaperwork4()
    {
        return $this->paperwork4;
    }

    /**
     * Set qualityP1
     *
     * @param string $qualityP1
     *
     * @return ProgramSa
     */
    public function setQualityP1($qualityP1)
    {
        $this->qualityP1 = $qualityP1;

        return $this;
    }

    /**
     * Get qualityP1
     *
     * @return string
     */
    public function getQualityP1()
    {
        return $this->qualityP1;
    }

    /**
     * Set qualityP2
     *
     * @param string $qualityP2
     *
     * @return ProgramSa
     */
    public function setQualityP2($qualityP2)
    {
        $this->qualityP2 = $qualityP2;

        return $this;
    }

    /**
     * Get qualityP2
     *
     * @return string
     */
    public function getQualityP2()
    {
        return $this->qualityP2;
    }

    /**
     * Set qualityP3
     *
     * @param string $qualityP3
     *
     * @return ProgramSa
     */
    public function setQualityP3($qualityP3)
    {
        $this->qualityP3 = $qualityP3;

        return $this;
    }

    /**
     * Get qualityP3
     *
     * @return string
     */
    public function getQualityP3()
    {
        return $this->qualityP3;
    }

    /**
     * Set qualityP4
     *
     * @param string $qualityP4
     *
     * @return ProgramSa
     */
    public function setQualityP4($qualityP4)
    {
        $this->qualityP4 = $qualityP4;

        return $this;
    }

    /**
     * Get qualityP4
     *
     * @return string
     */
    public function getQualityP4()
    {
        return $this->qualityP4;
    }

    /**
     * Set qualityP5
     *
     * @param string $qualityP5
     *
     * @return ProgramSa
     */
    public function setQualityP5($qualityP5)
    {
        $this->qualityP5 = $qualityP5;

        return $this;
    }

    /**
     * Get qualityP5
     *
     * @return string
     */
    public function getQualityP5()
    {
        return $this->qualityP5;
    }

    /**
     * Set qualityP6
     *
     * @param string $qualityP6
     *
     * @return ProgramSa
     */
    public function setQualityP6($qualityP6)
    {
        $this->qualityP6 = $qualityP6;

        return $this;
    }

    /**
     * Get qualityP6
     *
     * @return string
     */
    public function getQualityP6()
    {
        return $this->qualityP6;
    }

    /**
     * Set qualityP7
     *
     * @param string $qualityP7
     *
     * @return ProgramSa
     */
    public function setQualityP7($qualityP7)
    {
        $this->qualityP7 = $qualityP7;

        return $this;
    }

    /**
     * Get qualityP7
     *
     * @return string
     */
    public function getQualityP7()
    {
        return $this->qualityP7;
    }

    /**
     * Set qualityP8
     *
     * @param string $qualityP8
     *
     * @return ProgramSa
     */
    public function setQualityP8($qualityP8)
    {
        $this->qualityP8 = $qualityP8;

        return $this;
    }

    /**
     * Get qualityP8
     *
     * @return string
     */
    public function getQualityP8()
    {
        return $this->qualityP8;
    }

    /**
     * Set qualityQ1
     *
     * @param string $qualityQ1
     *
     * @return ProgramSa
     */
    public function setQualityQ1($qualityQ1)
    {
        $this->qualityQ1 = $qualityQ1;

        return $this;
    }

    /**
     * Get qualityQ1
     *
     * @return string
     */
    public function getQualityQ1()
    {
        return $this->qualityQ1;
    }

    /**
     * Set qualityQ2
     *
     * @param string $qualityQ2
     *
     * @return ProgramSa
     */
    public function setQualityQ2($qualityQ2)
    {
        $this->qualityQ2 = $qualityQ2;

        return $this;
    }

    /**
     * Get qualityQ2
     *
     * @return string
     */
    public function getQualityQ2()
    {
        return $this->qualityQ2;
    }

    /**
     * Set qualityQ3
     *
     * @param string $qualityQ3
     *
     * @return ProgramSa
     */
    public function setQualityQ3($qualityQ3)
    {
        $this->qualityQ3 = $qualityQ3;

        return $this;
    }

    /**
     * Get qualityQ3
     *
     * @return string
     */
    public function getQualityQ3()
    {
        return $this->qualityQ3;
    }

    /**
     * Set qualityQ4
     *
     * @param string $qualityQ4
     *
     * @return ProgramSa
     */
    public function setQualityQ4($qualityQ4)
    {
        $this->qualityQ4 = $qualityQ4;

        return $this;
    }

    /**
     * Get qualityQ4
     *
     * @return string
     */
    public function getQualityQ4()
    {
        return $this->qualityQ4;
    }

    /**
     * Set qualityQ5
     *
     * @param string $qualityQ5
     *
     * @return ProgramSa
     */
    public function setQualityQ5($qualityQ5)
    {
        $this->qualityQ5 = $qualityQ5;

        return $this;
    }

    /**
     * Get qualityQ5
     *
     * @return string
     */
    public function getQualityQ5()
    {
        return $this->qualityQ5;
    }

    /**
     * Set qualityQ6
     *
     * @param string $qualityQ6
     *
     * @return ProgramSa
     */
    public function setQualityQ6($qualityQ6)
    {
        $this->qualityQ6 = $qualityQ6;

        return $this;
    }

    /**
     * Get qualityQ6
     *
     * @return string
     */
    public function getQualityQ6()
    {
        return $this->qualityQ6;
    }

    /**
     * Set qualityQ7
     *
     * @param string $qualityQ7
     *
     * @return ProgramSa
     */
    public function setQualityQ7($qualityQ7)
    {
        $this->qualityQ7 = $qualityQ7;

        return $this;
    }

    /**
     * Get qualityQ7
     *
     * @return string
     */
    public function getQualityQ7()
    {
        return $this->qualityQ7;
    }

    /**
     * Set qualityQ8
     *
     * @param string $qualityQ8
     *
     * @return ProgramSa
     */
    public function setQualityQ8($qualityQ8)
    {
        $this->qualityQ8 = $qualityQ8;

        return $this;
    }

    /**
     * Get qualityQ8
     *
     * @return string
     */
    public function getQualityQ8()
    {
        return $this->qualityQ8;
    }

    /**
     * Set qualityG1
     *
     * @param string $qualityG1
     *
     * @return ProgramSa
     */
    public function setQualityG1($qualityG1)
    {
        $this->qualityG1 = $qualityG1;

        return $this;
    }

    /**
     * Get qualityG1
     *
     * @return string
     */
    public function getQualityG1()
    {
        return $this->qualityG1;
    }

    /**
     * Set qualityG2
     *
     * @param string $qualityG2
     *
     * @return ProgramSa
     */
    public function setQualityG2($qualityG2)
    {
        $this->qualityG2 = $qualityG2;

        return $this;
    }

    /**
     * Get qualityG2
     *
     * @return string
     */
    public function getQualityG2()
    {
        return $this->qualityG2;
    }

    /**
     * Set qualityG3
     *
     * @param string $qualityG3
     *
     * @return ProgramSa
     */
    public function setQualityG3($qualityG3)
    {
        $this->qualityG3 = $qualityG3;

        return $this;
    }

    /**
     * Get qualityG3
     *
     * @return string
     */
    public function getQualityG3()
    {
        return $this->qualityG3;
    }

    /**
     * Set qualityG4
     *
     * @param string $qualityG4
     *
     * @return ProgramSa
     */
    public function setQualityG4($qualityG4)
    {
        $this->qualityG4 = $qualityG4;

        return $this;
    }

    /**
     * Get qualityG4
     *
     * @return string
     */
    public function getQualityG4()
    {
        return $this->qualityG4;
    }

    /**
     * Set qualityG5
     *
     * @param string $qualityG5
     *
     * @return ProgramSa
     */
    public function setQualityG5($qualityG5)
    {
        $this->qualityG5 = $qualityG5;

        return $this;
    }

    /**
     * Get qualityG5
     *
     * @return string
     */
    public function getQualityG5()
    {
        return $this->qualityG5;
    }

    /**
     * Set qualityG6
     *
     * @param string $qualityG6
     *
     * @return ProgramSa
     */
    public function setQualityG6($qualityG6)
    {
        $this->qualityG6 = $qualityG6;

        return $this;
    }

    /**
     * Get qualityG6
     *
     * @return string
     */
    public function getQualityG6()
    {
        return $this->qualityG6;
    }

    /**
     * Set qualityG7
     *
     * @param string $qualityG7
     *
     * @return ProgramSa
     */
    public function setQualityG7($qualityG7)
    {
        $this->qualityG7 = $qualityG7;

        return $this;
    }

    /**
     * Get qualityG7
     *
     * @return string
     */
    public function getQualityG7()
    {
        return $this->qualityG7;
    }

    /**
     * Set qualityg8
     *
     * @param string $qualityg8
     *
     * @return ProgramSa
     */
    public function setQualityg8($qualityg8)
    {
        $this->qualityg8 = $qualityg8;

        return $this;
    }

    /**
     * Get qualityg8
     *
     * @return string
     */
    public function getQualityg8()
    {
        return $this->qualityg8;
    }

    /**
     * Set paperwork5
     *
     * @param array $paperwork5
     *
     * @return ProgramSa
     */
    public function setPaperwork5($paperwork5)
    {
        $this->paperwork5 = $paperwork5;

        return $this;
    }

    /**
     * Get paperwork5
     *
     * @return array
     */
    public function getPaperwork5()
    {
        return $this->paperwork5;
    }

    /**
     * Set paperwork6
     *
     * @param array $paperwork6
     *
     * @return ProgramSa
     */
    public function setPaperwork6($paperwork6)
    {
        $this->paperwork6 = $paperwork6;

        return $this;
    }

    /**
     * Get paperwork6
     *
     * @return array
     */
    public function getPaperwork6()
    {
        return $this->paperwork6;
    }

    /**
     * Set paperwork7
     *
     * @param array $paperwork7
     *
     * @return ProgramSa
     */
    public function setPaperwork7($paperwork7)
    {
        $this->paperwork7 = $paperwork7;

        return $this;
    }

    /**
     * Get paperwork7
     *
     * @return array
     */
    public function getPaperwork7()
    {
        return $this->paperwork7;
    }

    /**
     * Set paperwork8
     *
     * @param array $paperwork8
     *
     * @return ProgramSa
     */
    public function setPaperwork8($paperwork8)
    {
        $this->paperwork8 = $paperwork8;

        return $this;
    }

    /**
     * Get paperwork8
     *
     * @return array
     */
    public function getPaperwork8()
    {
        return $this->paperwork8;
    }

    /**
     * Set service1
     *
     * @param string $service1
     *
     * @return ProgramSa
     */
    public function setService1($service1)
    {
        $this->service1 = $service1;

        return $this;
    }

    /**
     * Get service1
     *
     * @return string
     */
    public function getService1()
    {
        return $this->service1;
    }

    /**
     * Set service2
     *
     * @param string $service2
     *
     * @return ProgramSa
     */
    public function setService2($service2)
    {
        $this->service2 = $service2;

        return $this;
    }

    /**
     * Get service2
     *
     * @return string
     */
    public function getService2()
    {
        return $this->service2;
    }

    /**
     * Set service3
     *
     * @param string $service3
     *
     * @return ProgramSa
     */
    public function setService3($service3)
    {
        $this->service3 = $service3;

        return $this;
    }

    /**
     * Get service3
     *
     * @return string
     */
    public function getService3()
    {
        return $this->service3;
    }

    /**
     * Set service4
     *
     * @param string $service4
     *
     * @return ProgramSa
     */
    public function setService4($service4)
    {
        $this->service4 = $service4;

        return $this;
    }

    /**
     * Get service4
     *
     * @return string
     */
    public function getService4()
    {
        return $this->service4;
    }

    /**
     * Set service5
     *
     * @param string $service5
     *
     * @return ProgramSa
     */
    public function setService5($service5)
    {
        $this->service5 = $service5;

        return $this;
    }

    /**
     * Get service5
     *
     * @return string
     */
    public function getService5()
    {
        return $this->service5;
    }

    /**
     * Set service6
     *
     * @param string $service6
     *
     * @return ProgramSa
     */
    public function setService6($service6)
    {
        $this->service6 = $service6;

        return $this;
    }

    /**
     * Get service6
     *
     * @return string
     */
    public function getService6()
    {
        return $this->service6;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return ProgramSa
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set revisionplan
     *
     * @param string $revisionplan
     *
     * @return ProgramSa
     */
    public function setRevisionplan($revisionplan)
    {
        $this->revisionplan = $revisionplan;

        return $this;
    }

    /**
     * Get revisionplan
     *
     * @return string
     */
    public function getRevisionplan()
    {
        return $this->revisionplan;
    }

    /**
     * Set revisionproduct
     *
     * @param string $revisionproduct
     *
     * @return ProgramSa
     */
    public function setRevisionproduct($revisionproduct)
    {
        $this->revisionproduct = $revisionproduct;

        return $this;
    }

    /**
     * Get revisionproduct
     *
     * @return string
     */
    public function getRevisionproduct()
    {
        return $this->revisionproduct;
    }

    /**
     * Set revisionprocess
     *
     * @param string $revisionprocess
     *
     * @return ProgramSa
     */
    public function setRevisionprocess($revisionprocess)
    {
        $this->revisionprocess = $revisionprocess;

        return $this;
    }

    /**
     * Get revisionprocess
     *
     * @return string
     */
    public function getRevisionprocess()
    {
        return $this->revisionprocess;
    }

    /**
     * Set revisionpromotion
     *
     * @param string $revisionpromotion
     *
     * @return ProgramSa
     */
    public function setRevisionpromotion($revisionpromotion)
    {
        $this->revisionpromotion = $revisionpromotion;

        return $this;
    }

    /**
     * Get revisionpromotion
     *
     * @return string
     */
    public function getRevisionpromotion()
    {
        return $this->revisionpromotion;
    }

    /**
     * Set revisionprice
     *
     * @param string $revisionprice
     *
     * @return ProgramSa
     */
    public function setRevisionprice($revisionprice)
    {
        $this->revisionprice = $revisionprice;

        return $this;
    }

    /**
     * Get revisionprice
     *
     * @return string
     */
    public function getRevisionprice()
    {
        return $this->revisionprice;
    }

    /**
     * Set revisionpaperwork
     *
     * @param string $revisionpaperwork
     *
     * @return ProgramSa
     */
    public function setRevisionpaperwork($revisionpaperwork)
    {
        $this->revisionpaperwork = $revisionpaperwork;

        return $this;
    }

    /**
     * Get revisionpaperwork
     *
     * @return string
     */
    public function getRevisionpaperwork()
    {
        return $this->revisionpaperwork;
    }

    /**
     * Set revisionquality
     *
     * @param string $revisionquality
     *
     * @return ProgramSa
     */
    public function setRevisionquality($revisionquality)
    {
        $this->revisionquality = $revisionquality;

        return $this;
    }

    /**
     * Get revisionquality
     *
     * @return string
     */
    public function getRevisionquality()
    {
        return $this->revisionquality;
    }

    /**
     * Set revisionservice
     *
     * @param string $revisionservice
     *
     * @return ProgramSa
     */
    public function setRevisionservice($revisionservice)
    {
        $this->revisionservice = $revisionservice;

        return $this;
    }

    /**
     * Get revisionservice
     *
     * @return string
     */
    public function getRevisionservice()
    {
        return $this->revisionservice;
    }

    /**
     * Set filestudent
     *
     * @param string $filestudent
     *
     * @return ProgramSa
     */
    public function setFilestudent($filestudent)
    {
        $this->filestudent = $filestudent;

        return $this;
    }

    /**
     * Get filestudent
     *
     * @return string
     */
    public function getFilestudent()
    {
        return $this->filestudent;
    }

    /**
     * Set fileambassador
     *
     * @param string $fileambassador
     *
     * @return ProgramSa
     */
    public function setFileambassador($fileambassador)
    {
        $this->fileambassador = $fileambassador;

        return $this;
    }

    /**
     * Get fileambassador
     *
     * @return string
     */
    public function getFileambassador()
    {
        return $this->fileambassador;
    }

    /**
     * Set modality
     *
     * @param string $modality
     *
     * @return ProgramSa
     */
    public function setModality($modality)
    {
        $this->modality = $modality;

        return $this;
    }

    /**
     * Get modality
     *
     * @return string
     */
    public function getModality()
    {
        return $this->modality;
    }

    /**
     * Set uploadDateStudent
     *
     * @param \DateTime $uploadDateStudent
     *
     * @return ProgramSa
     */
    public function setUploadDateStudent($uploadDateStudent)
    {
        $this->uploadDateStudent = $uploadDateStudent;

        return $this;
    }

    /**
     * Get uploadDateStudent
     *
     * @return \DateTime
     */
    public function getUploadDateStudent()
    {
        return $this->uploadDateStudent;
    }

    /**
     * Set uploadDateAdmin
     *
     * @param \DateTime $uploadDateAdmin
     *
     * @return ProgramSa
     */
    public function setUploadDateAdmin($uploadDateAdmin)
    {
        $this->uploadDateAdmin = $uploadDateAdmin;

        return $this;
    }

    /**
     * Get uploadDateAdmin
     *
     * @return \DateTime
     */
    public function getUploadDateAdmin()
    {
        return $this->uploadDateAdmin;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return ProgramSa
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set approvalDate
     *
     * @param \DateTime $approvalDate
     *
     * @return ProgramSa
     */
    public function setApprovalDate($approvalDate)
    {
        $this->approvalDate = $approvalDate;

        return $this;
    }

    /**
     * Get approvalDate
     *
     * @return \DateTime
     */
    public function getApprovalDate()
    {
        return $this->approvalDate;
    }

    /**
     * Set statusplan
     *
     * @param string $statusplan
     *
     * @return ProgramSa
     */
    public function setStatusplan($statusplan)
    {
        $this->statusplan = $statusplan;

        return $this;
    }

    /**
     * Get statusplan
     *
     * @return string
     */
    public function getStatusplan()
    {
        return $this->statusplan;
    }

    /**
     * Set statusproduct
     *
     * @param string $statusproduct
     *
     * @return ProgramSa
     */
    public function setStatusproduct($statusproduct)
    {
        $this->statusproduct = $statusproduct;

        return $this;
    }

    /**
     * Get statusproduct
     *
     * @return string
     */
    public function getStatusproduct()
    {
        return $this->statusproduct;
    }

    /**
     * Set statusprice
     *
     * @param string $statusprice
     *
     * @return ProgramSa
     */
    public function setStatusprice($statusprice)
    {
        $this->statusprice = $statusprice;

        return $this;
    }

    /**
     * Get statusprice
     *
     * @return string
     */
    public function getStatusprice()
    {
        return $this->statusprice;
    }

    /**
     * Set statuspromotion
     *
     * @param string $statuspromotion
     *
     * @return ProgramSa
     */
    public function setStatuspromotion($statuspromotion)
    {
        $this->statuspromotion = $statuspromotion;

        return $this;
    }

    /**
     * Get statuspromotion
     *
     * @return string
     */
    public function getStatuspromotion()
    {
        return $this->statuspromotion;
    }

    /**
     * Set statuspaperwork
     *
     * @param string $statuspaperwork
     *
     * @return ProgramSa
     */
    public function setStatuspaperwork($statuspaperwork)
    {
        $this->statuspaperwork = $statuspaperwork;

        return $this;
    }

    /**
     * Get statuspaperwork
     *
     * @return string
     */
    public function getStatuspaperwork()
    {
        return $this->statuspaperwork;
    }

    /**
     * Set statusprocess
     *
     * @param string $statusprocess
     *
     * @return ProgramSa
     */
    public function setStatusprocess($statusprocess)
    {
        $this->statusprocess = $statusprocess;

        return $this;
    }

    /**
     * Get statusprocess
     *
     * @return string
     */
    public function getStatusprocess()
    {
        return $this->statusprocess;
    }

    /**
     * Set statusquality
     *
     * @param string $statusquality
     *
     * @return ProgramSa
     */
    public function setStatusquality($statusquality)
    {
        $this->statusquality = $statusquality;

        return $this;
    }

    /**
     * Get statusquality
     *
     * @return string
     */
    public function getStatusquality()
    {
        return $this->statusquality;
    }

    /**
     * Set statusservice
     *
     * @param string $statusservice
     *
     * @return ProgramSa
     */
    public function setStatusservice($statusservice)
    {
        $this->statusservice = $statusservice;

        return $this;
    }

    /**
     * Get statusservice
     *
     * @return string
     */
    public function getStatusservice()
    {
        return $this->statusservice;
    }

    /**
     * Set mision1
     *
     * @param string $mision1
     *
     * @return ProgramSa
     */
    public function setMision1($mision1)
    {
        $this->mision1 = $mision1;

        return $this;
    }

    /**
     * Get mision1
     *
     * @return string
     */
    public function getMision1()
    {
        return $this->mision1;
    }

    /**
     * Set mision2
     *
     * @param string $mision2
     *
     * @return ProgramSa
     */
    public function setMision2($mision2)
    {
        $this->mision2 = $mision2;

        return $this;
    }

    /**
     * Get mision2
     *
     * @return string
     */
    public function getMision2()
    {
        return $this->mision2;
    }

    /**
     * Set mision3
     *
     * @param string $mision3
     *
     * @return ProgramSa
     */
    public function setMision3($mision3)
    {
        $this->mision3 = $mision3;

        return $this;
    }

    /**
     * Get mision3
     *
     * @return string
     */
    public function getMision3()
    {
        return $this->mision3;
    }

    /**
     * Set mision4
     *
     * @param string $mision4
     *
     * @return ProgramSa
     */
    public function setMision4($mision4)
    {
        $this->mision4 = $mision4;

        return $this;
    }

    /**
     * Get mision4
     *
     * @return string
     */
    public function getMision4()
    {
        return $this->mision4;
    }

    /**
     * Set generateGroups1
     *
     * @param array $generateGroups1
     *
     * @return ProgramSa
     */
    public function setGenerateGroups1($generateGroups1)
    {
        $this->generateGroups1 = $generateGroups1;

        return $this;
    }

    /**
     * Get generateGroups1
     *
     * @return array
     */
    public function getGenerateGroups1()
    {
        return $this->generateGroups1;
    }

    /**
     * Set generateGroups2
     *
     * @param string $generateGroups2
     *
     * @return ProgramSa
     */
    public function setGenerateGroups2($generateGroups2)
    {
        $this->generateGroups2 = $generateGroups2;

        return $this;
    }

    /**
     * Get generateGroups2
     *
     * @return string
     */
    public function getGenerateGroups2()
    {
        return $this->generateGroups2;
    }

    /**
     * Set generateGroups3
     *
     * @param array $generateGroups3
     *
     * @return ProgramSa
     */
    public function setGenerateGroups3($generateGroups3)
    {
        $this->generateGroups3 = $generateGroups3;

        return $this;
    }

    /**
     * Get generateGroups3
     *
     * @return array
     */
    public function getGenerateGroups3()
    {
        return $this->generateGroups3;
    }

    /**
     * Set generateGroups4
     *
     * @param string $generateGroups4
     *
     * @return ProgramSa
     */
    public function setGenerateGroups4($generateGroups4)
    {
        $this->generateGroups4 = $generateGroups4;

        return $this;
    }

    /**
     * Get generateGroups4
     *
     * @return string
     */
    public function getGenerateGroups4()
    {
        return $this->generateGroups4;
    }

    /**
     * Set generateGroups5
     *
     * @param array $generateGroups5
     *
     * @return ProgramSa
     */
    public function setGenerateGroups5($generateGroups5)
    {
        $this->generateGroups5 = $generateGroups5;

        return $this;
    }

    /**
     * Get generateGroups5
     *
     * @return array
     */
    public function getGenerateGroups5()
    {
        return $this->generateGroups5;
    }

    /**
     * Set generateGroups6
     *
     * @param string $generateGroups6
     *
     * @return ProgramSa
     */
    public function setGenerateGroups6($generateGroups6)
    {
        $this->generateGroups6 = $generateGroups6;

        return $this;
    }

    /**
     * Get generateGroups6
     *
     * @return string
     */
    public function getGenerateGroups6()
    {
        return $this->generateGroups6;
    }

    /**
     * Set generateGroups7
     *
     * @param string $generateGroups7
     *
     * @return ProgramSa
     */
    public function setGenerateGroups7($generateGroups7)
    {
        $this->generateGroups7 = $generateGroups7;

        return $this;
    }

    /**
     * Get generateGroups7
     *
     * @return string
     */
    public function getGenerateGroups7()
    {
        return $this->generateGroups7;
    }

    /**
     * Set rule1
     *
     * @param string $rule1
     *
     * @return ProgramSa
     */
    public function setRule1($rule1)
    {
        $this->rule1 = $rule1;

        return $this;
    }

    /**
     * Get rule1
     *
     * @return string
     */
    public function getRule1()
    {
        return $this->rule1;
    }

    /**
     * Set rule2
     *
     * @param string $rule2
     *
     * @return ProgramSa
     */
    public function setRule2($rule2)
    {
        $this->rule2 = $rule2;

        return $this;
    }

    /**
     * Get rule2
     *
     * @return string
     */
    public function getRule2()
    {
        return $this->rule2;
    }

    /**
     * Set rule3
     *
     * @param string $rule3
     *
     * @return ProgramSa
     */
    public function setRule3($rule3)
    {
        $this->rule3 = $rule3;

        return $this;
    }

    /**
     * Get rule3
     *
     * @return string
     */
    public function getRule3()
    {
        return $this->rule3;
    }

    /**
     * Set rule4
     *
     * @param string $rule4
     *
     * @return ProgramSa
     */
    public function setRule4($rule4)
    {
        $this->rule4 = $rule4;

        return $this;
    }

    /**
     * Get rule4
     *
     * @return string
     */
    public function getRule4()
    {
        return $this->rule4;
    }

    /**
     * Set rule5
     *
     * @param string $rule5
     *
     * @return ProgramSa
     */
    public function setRule5($rule5)
    {
        $this->rule5 = $rule5;

        return $this;
    }

    /**
     * Get rule5
     *
     * @return string
     */
    public function getRule5()
    {
        return $this->rule5;
    }

    /**
     * Set rule6
     *
     * @param string $rule6
     *
     * @return ProgramSa
     */
    public function setRule6($rule6)
    {
        $this->rule6 = $rule6;

        return $this;
    }

    /**
     * Get rule6
     *
     * @return string
     */
    public function getRule6()
    {
        return $this->rule6;
    }

    /**
     * Set rule7
     *
     * @param string $rule7
     *
     * @return ProgramSa
     */
    public function setRule7($rule7)
    {
        $this->rule7 = $rule7;

        return $this;
    }

    /**
     * Get rule7
     *
     * @return string
     */
    public function getRule7()
    {
        return $this->rule7;
    }

    /**
     * Set rule8
     *
     * @param string $rule8
     *
     * @return ProgramSa
     */
    public function setRule8($rule8)
    {
        $this->rule8 = $rule8;

        return $this;
    }

    /**
     * Get rule8
     *
     * @return string
     */
    public function getRule8()
    {
        return $this->rule8;
    }

  
    /**
     * Set rule10
     *
     * @param string $rule10
     *
     * @return ProgramSa
     */
    public function setRule10($rule10)
    {
        $this->rule10 = $rule10;

        return $this;
    }

    /**
     * Get rule10
     *
     * @return string
     */
    public function getRule10()
    {
        return $this->rule10;
    }

    /**
     * Set graduate1
     *
     * @param string $graduate1
     *
     * @return ProgramSa
     */
    public function setGraduate1($graduate1)
    {
        $this->graduate1 = $graduate1;

        return $this;
    }

    /**
     * Get graduate1
     *
     * @return string
     */
    public function getGraduate1()
    {
        return $this->graduate1;
    }

    /**
     * Set graduate2
     *
     * @param string $graduate2
     *
     * @return ProgramSa
     */
    public function setGraduate2($graduate2)
    {
        $this->graduate2 = $graduate2;

        return $this;
    }

    /**
     * Get graduate2
     *
     * @return string
     */
    public function getGraduate2()
    {
        return $this->graduate2;
    }

    /**
     * Set graduate3
     *
     * @param string $graduate3
     *
     * @return ProgramSa
     */
    public function setGraduate3($graduate3)
    {
        $this->graduate3 = $graduate3;

        return $this;
    }

    /**
     * Get graduate3
     *
     * @return string
     */
    public function getGraduate3()
    {
        return $this->graduate3;
    }

    /**
     * Set graduate4
     *
     * @param string $graduate4
     *
     * @return ProgramSa
     */
    public function setGraduate4($graduate4)
    {
        $this->graduate4 = $graduate4;

        return $this;
    }

    /**
     * Get graduate4
     *
     * @return string
     */
    public function getGraduate4()
    {
        return $this->graduate4;
    }

    /**
     * Set graduate5
     *
     * @param string $graduate5
     *
     * @return ProgramSa
     */
    public function setGraduate5($graduate5)
    {
        $this->graduate5 = $graduate5;

        return $this;
    }

    /**
     * Get graduate5
     *
     * @return string
     */
    public function getGraduate5()
    {
        return $this->graduate5;
    }

    /**
     * Set support1
     *
     * @param string $support1
     *
     * @return ProgramSa
     */
    public function setSupport1($support1)
    {
        $this->support1 = $support1;

        return $this;
    }

    /**
     * Get support1
     *
     * @return string
     */
    public function getSupport1()
    {
        return $this->support1;
    }

    /**
     * Set support2
     *
     * @param string $support2
     *
     * @return ProgramSa
     */
    public function setSupport2($support2)
    {
        $this->support2 = $support2;

        return $this;
    }

    /**
     * Get support2
     *
     * @return string
     */
    public function getSupport2()
    {
        return $this->support2;
    }

    /**
     * Set support3
     *
     * @param string $support3
     *
     * @return ProgramSa
     */
    public function setSupport3($support3)
    {
        $this->support3 = $support3;

        return $this;
    }

    /**
     * Get support3
     *
     * @return string
     */
    public function getSupport3()
    {
        return $this->support3;
    }

    /**
     * Set revisionmision
     *
     * @param string $revisionmision
     *
     * @return ProgramSa
     */
    public function setRevisionmision($revisionmision)
    {
        $this->revisionmision = $revisionmision;

        return $this;
    }

    /**
     * Get revisionmision
     *
     * @return string
     */
    public function getRevisionmision()
    {
        return $this->revisionmision;
    }

    /**
     * Set revisiongenerategroups
     *
     * @param string $revisiongenerategroups
     *
     * @return ProgramSa
     */
    public function setRevisiongenerategroups($revisiongenerategroups)
    {
        $this->revisiongenerategroups = $revisiongenerategroups;

        return $this;
    }

    /**
     * Get revisiongenerategroups
     *
     * @return string
     */
    public function getRevisiongenerategroups()
    {
        return $this->revisiongenerategroups;
    }

    /**
     * Set revisionrule
     *
     * @param string $revisionrule
     *
     * @return ProgramSa
     */
    public function setRevisionrule($revisionrule)
    {
        $this->revisionrule = $revisionrule;

        return $this;
    }

    /**
     * Get revisionrule
     *
     * @return string
     */
    public function getRevisionrule()
    {
        return $this->revisionrule;
    }

    /**
     * Set revisiongraduate
     *
     * @param string $revisiongraduate
     *
     * @return ProgramSa
     */
    public function setRevisiongraduate($revisiongraduate)
    {
        $this->revisiongraduate = $revisiongraduate;

        return $this;
    }

    /**
     * Get revisiongraduate
     *
     * @return string
     */
    public function getRevisiongraduate()
    {
        return $this->revisiongraduate;
    }

    /**
     * Set statusmision
     *
     * @param string $statusmision
     *
     * @return ProgramSa
     */
    public function setStatusmision($statusmision)
    {
        $this->statusmision = $statusmision;

        return $this;
    }

    /**
     * Get statusmision
     *
     * @return string
     */
    public function getStatusmision()
    {
        return $this->statusmision;
    }

    /**
     * Set statusgenerategroups
     *
     * @param string $statusgenerategroups
     *
     * @return ProgramSa
     */
    public function setStatusgenerategroups($statusgenerategroups)
    {
        $this->statusgenerategroups = $statusgenerategroups;

        return $this;
    }

    /**
     * Get statusgenerategroups
     *
     * @return string
     */
    public function getStatusgenerategroups()
    {
        return $this->statusgenerategroups;
    }

    /**
     * Set statusrule
     *
     * @param string $statusrule
     *
     * @return ProgramSa
     */
    public function setStatusrule($statusrule)
    {
        $this->statusrule = $statusrule;

        return $this;
    }

    /**
     * Get statusrule
     *
     * @return string
     */
    public function getStatusrule()
    {
        return $this->statusrule;
    }

    /**
     * Set statusgraduate
     *
     * @param string $statusgraduate
     *
     * @return ProgramSa
     */
    public function setStatusgraduate($statusgraduate)
    {
        $this->statusgraduate = $statusgraduate;

        return $this;
    }

    /**
     * Get statusgraduate
     *
     * @return string
     */
    public function getStatusgraduate()
    {
        return $this->statusgraduate;
    }

    /**
     * Set revisionsupport
     *
     * @param string $revisionsupport
     *
     * @return ProgramSa
     */
    public function setRevisionsupport($revisionsupport)
    {
        $this->revisionsupport = $revisionsupport;

        return $this;
    }

    /**
     * Get revisionsupport
     *
     * @return string
     */
    public function getRevisionsupport()
    {
        return $this->revisionsupport;
    }

    /**
     * Set statussupport
     *
     * @param string $statussupport
     *
     * @return ProgramSa
     */
    public function setStatussupport($statussupport)
    {
        $this->statussupport = $statussupport;

        return $this;
    }

    /**
     * Get statussupport
     *
     * @return string
     */
    public function getStatussupport()
    {
        return $this->statussupport;
    }

    /**
     * Set productName.
     *
     * @param string|null $productName
     *
     * @return ProgramSa
     */
    public function setProductName($productName = null)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get productName.
     *
     * @return string|null
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set productDescription.
     *
     * @param string|null $productDescription
     *
     * @return ProgramSa
     */
    public function setProductDescription($productDescription = null)
    {
        $this->productDescription = $productDescription;

        return $this;
    }

    public function getProductDescription(): ?string
    {
        return $this->productDescription;
    }

      /**
     * Set rule9
     *
     * @param array $rule9
     *
     * @return ProgramSa
     */
    public function setRule9($rule9)
    {
        $this->rule9 = $rule9;

        return $this;
    }

    /**
     * Get rule9
     *
     * @return array
     */
    public function getRule9()
    {
        return $this->rule9;
    }

    
}
