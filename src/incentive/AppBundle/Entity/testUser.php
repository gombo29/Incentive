<?php

namespace incentive\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * testBranch
 *
 * @ORM\Table(name="test_user")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class testUser
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
     * @var string
     *
     * @ORM\Column(name="user_name", type="string", length=145, nullable=true)
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=145, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=145, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=145, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=145, nullable=true)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=145, nullable=true)
     */
    private $picture;

    /**
     * @var boolean
     * @ORM\Column(name="tmp", type="boolean",nullable=true)
     */
    private $tmp;

    /**
     * @var string
     *
     * @ORM\Column(name="branch_name", type="string", length=145, nullable=true)
     */
    private $branchName;

    /**
     * @var string
     *
     * @ORM\Column(name="position_name", type="string", length=145, nullable=true)
     */
    private $positionName;

    /**
     * @var int
     * @ORM\Column(name="sector_id", type="integer", nullable=true)
     */
    private $sectorId;

    /**
     * @var int
     * @ORM\Column(name="division_id", type="integer", nullable=true)
     */
    private $divisionId;

    /**
     * @var int
     * @ORM\Column(name="department_id", type="integer", nullable=true)
     */
    private $departmentId;

    /**
     * @var int
     * @ORM\Column(name="section_id", type="integer", nullable=true)
     */
    private $sectionId;

    /**
     * @var string
     *
     * @ORM\Column(name="sector_name", type="string", length=145, nullable=true)
     */
    private $sectorName;

    /**
     * @var string
     *
     * @ORM\Column(name="division_name", type="string", length=145, nullable=true)
     */
    private $divisionName;

    /**
     * @var string
     *
     * @ORM\Column(name="department_name", type="string", length=145, nullable=true)
     */
    private $departmentName;


    /**
     * @var string
     *
     * @ORM\Column(name="section_name", type="string", length=145, nullable=true)
     */
    private $sectionName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=145, nullable=true)
     */
    private $password;

    /**
     * @var int
     * @ORM\Column(name="lineId", type="integer", nullable=true)
     */
    private $lineId;

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
     * Set userName
     *
     * @param string $userName
     *
     * @return testUser
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return testUser
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return testUser
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return testUser
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     *
     * @return testUser
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set picture
     *
     * @param string $picture
     *
     * @return testUser
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set tmp
     *
     * @param boolean $tmp
     *
     * @return testUser
     */
    public function setTmp($tmp)
    {
        $this->tmp = $tmp;

        return $this;
    }

    /**
     * Get tmp
     *
     * @return boolean
     */
    public function getTmp()
    {
        return $this->tmp;
    }

    /**
     * Set branchName
     *
     * @param string $branchName
     *
     * @return testUser
     */
    public function setBranchName($branchName)
    {
        $this->branchName = $branchName;

        return $this;
    }

    /**
     * Get branchName
     *
     * @return string
     */
    public function getBranchName()
    {
        return $this->branchName;
    }

    /**
     * Set positionName
     *
     * @param string $positionName
     *
     * @return testUser
     */
    public function setPositionName($positionName)
    {
        $this->positionName = $positionName;

        return $this;
    }

    /**
     * Get positionName
     *
     * @return string
     */
    public function getPositionName()
    {
        return $this->positionName;
    }

    /**
     * Set sectorId
     *
     * @param integer $sectorId
     *
     * @return testUser
     */
    public function setSectorId($sectorId)
    {
        $this->sectorId = $sectorId;

        return $this;
    }

    /**
     * Get sectorId
     *
     * @return integer
     */
    public function getSectorId()
    {
        return $this->sectorId;
    }

    /**
     * Set divisionId
     *
     * @param integer $divisionId
     *
     * @return testUser
     */
    public function setDivisionId($divisionId)
    {
        $this->divisionId = $divisionId;

        return $this;
    }

    /**
     * Get divisionId
     *
     * @return integer
     */
    public function getDivisionId()
    {
        return $this->divisionId;
    }

    /**
     * Set departmentId
     *
     * @param integer $departmentId
     *
     * @return testUser
     */
    public function setDepartmentId($departmentId)
    {
        $this->departmentId = $departmentId;

        return $this;
    }

    /**
     * Get departmentId
     *
     * @return integer
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     * Set sectionId
     *
     * @param integer $sectionId
     *
     * @return testUser
     */
    public function setSectionId($sectionId)
    {
        $this->sectionId = $sectionId;

        return $this;
    }

    /**
     * Get sectionId
     *
     * @return integer
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }

    /**
     * Set sectorName
     *
     * @param string $sectorName
     *
     * @return testUser
     */
    public function setSectorName($sectorName)
    {
        $this->sectorName = $sectorName;

        return $this;
    }

    /**
     * Get sectorName
     *
     * @return string
     */
    public function getSectorName()
    {
        return $this->sectorName;
    }

    /**
     * Set divisionName
     *
     * @param string $divisionName
     *
     * @return testUser
     */
    public function setDivisionName($divisionName)
    {
        $this->divisionName = $divisionName;

        return $this;
    }

    /**
     * Get divisionName
     *
     * @return string
     */
    public function getDivisionName()
    {
        return $this->divisionName;
    }

    /**
     * Set departmentName
     *
     * @param string $departmentName
     *
     * @return testUser
     */
    public function setDepartmentName($departmentName)
    {
        $this->departmentName = $departmentName;

        return $this;
    }

    /**
     * Get departmentName
     *
     * @return string
     */
    public function getDepartmentName()
    {
        return $this->departmentName;
    }

    /**
     * Set sectionName
     *
     * @param string $sectionName
     *
     * @return testUser
     */
    public function setSectionName($sectionName)
    {
        $this->sectionName = $sectionName;

        return $this;
    }

    /**
     * Get sectionName
     *
     * @return string
     */
    public function getSectionName()
    {
        return $this->sectionName;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return testUser
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
     * Set password
     *
     * @param string $password
     *
     * @return testUser
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set lineId
     *
     * @param integer $lineId
     *
     * @return testUser
     */
    public function setLineId($lineId)
    {
        $this->lineId = $lineId;

        return $this;
    }

    /**
     * Get lineId
     *
     * @return integer
     */
    public function getLineId()
    {
        return $this->lineId;
    }
}
