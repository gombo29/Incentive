<?php

namespace incentive\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * testBranch
 *
 * @ORM\Table(name="test_branch")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class testBranch
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="testBranch")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private $parent;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="altName", type="string", length=100, nullable=true)
     */
    private $altName;

    /**
     * @var int
     * @ORM\Column(name="level", type="integer", nullable=true)
     */
    private $level;


    /**
     * @var string
     *
     * @ORM\Column(name="camundaKey", type="string", length=100, nullable=true)
     */
    private $camundaKey;

    /**
     * @var int
     * @ORM\Column(name="companyId", type="integer", nullable=true)
     */
    private $companyId;

    /**
     * @var string
     *
     * @ORM\Column(name="companyName", type="string", length=100, nullable=true)
     */
    private $companyName;


    /**
     * Get id
     *
     * @return integer
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
     * @return testBranch
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
     * Set altName
     *
     * @param string $altName
     *
     * @return testBranch
     */
    public function setAltName($altName)
    {
        $this->altName = $altName;

        return $this;
    }

    /**
     * Get altName
     *
     * @return string
     */
    public function getAltName()
    {
        return $this->altName;
    }

    /**
     * Set level
     *
     * @param integer $level
     *
     * @return testBranch
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set camundaKey
     *
     * @param string $camundaKey
     *
     * @return testBranch
     */
    public function setCamundaKey($camundaKey)
    {
        $this->camundaKey = $camundaKey;

        return $this;
    }

    /**
     * Get camundaKey
     *
     * @return string
     */
    public function getCamundaKey()
    {
        return $this->camundaKey;
    }

    /**
     * Set companyId
     *
     * @param integer $companyId
     *
     * @return testBranch
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * Get companyId
     *
     * @return integer
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set companyName
     *
     * @param string $companyName
     *
     * @return testBranch
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set parent
     *
     * @param \incentive\AppBundle\Entity\testBranch $parent
     *
     * @return testBranch
     */
    public function setParent(\incentive\AppBundle\Entity\testBranch $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \incentive\AppBundle\Entity\testBranch
     */
    public function getParent()
    {
        return $this->parent;
    }
}
