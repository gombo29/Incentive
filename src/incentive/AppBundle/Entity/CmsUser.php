<?php

namespace incentive\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
/**
 * CmsUser
 *
 * @ORM\Table(name="cms_user")
 * @ORM\Entity
 */
class CmsUser extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    public $cattmp;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=100, nullable=true)
     */
    private $mail;

    /**
     * @var boolean
     * @ORM\Column(name="isHrm", type="boolean",nullable=true)
     */
    private $isHRM;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=100, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=100, nullable=true)
     */
    private $firstname;


    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=100, nullable=true)
     */
    private $mobile;

    /**
     * @ORM\ManyToOne(targetEntity="Branch")
     * @ORM\JoinColumn(name="branch", referencedColumnName="id", nullable=true)
     */
    private $branch;


    /**
     * Set id
     *
     * @param integer $id
     *
     * @return CmsUser
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
     * Set branch
     *
     * @param \incentive\AppBundle\Entity\Branch $branch
     *
     * @return CmsUser
     */
    public function setBranch(\incentive\AppBundle\Entity\Branch $branch = null)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get branch
     *
     * @return \incentive\AppBundle\Entity\Branch
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return CmsUser
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return CmsUser
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     *
     * @return CmsUser
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
     * Set mail
     *
     * @param string $mail
     *
     * @return CmsUser
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set isHRM
     *
     * @param boolean $isHRM
     *
     * @return CmsUser
     */
    public function setIsHRM($isHRM)
    {
        $this->isHRM = $isHRM;

        return $this;
    }

    /**
     * Get isHRM
     *
     * @return boolean
     */
    public function getIsHRM()
    {
        return $this->isHRM;
    }
}
