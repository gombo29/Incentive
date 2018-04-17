<?php

namespace incentive\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Plan
 *
 * @ORM\Table(name="monthly_plan")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class MonthlyPlan
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
     * @ORM\ManyToOne(targetEntity="PlanProduct")
     * @ORM\JoinColumn(name="plan_product", referencedColumnName="id", nullable=true)
     */
    private $planProduct;

    /**
     * @ORM\ManyToOne(targetEntity="PlanBranch")
     * @ORM\JoinColumn(name="plan_branch", referencedColumnName="id", nullable=true)
     */
    private $planBranch;


    /**
     * @ORM\ManyToOne(targetEntity="Plan")
     * @ORM\JoinColumn(name="plan", referencedColumnName="id", nullable=true)
     */
    private $plan;

    /**
     * @var int
     * @ORM\Column(name="serviceValue", type="integer", nullable=true)
     */
    private $serviceValue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime", nullable=true)
     */
    private $updatedDate;


    /**
     *
     * @Assert\File
     * (
     * mimeTypes = {"application/vnd.ms-office", "application/vnd.ms-excel"},
     * mimeTypesMessage = "Please upload a valid XLS File"
     * )
     */
    private $excelfile;


    /**
     *
     * @param $container
     */
    public function uploadExcel(Container $container)
    {
        if (null === $this->getExcelFile()) {
            return;
        }
        $resources = $container->getParameter('statfolder');
        $dir = 'excel/draft';
        $filename = 'excel.' . $this->getExcelFile()->getClientOriginalExtension();
        $this->getExcelFile()->move(
            $resources . '/' . $dir, $filename
        );
    }


    /**
     * Get File
     *
     * @return UploadedFile
     */
    public function getExcelFile()
    {
        return $this->excelfile;
    }

    /**
     * Set File
     *
     * @param UploadedFile $file
     */
    public function setExcelFile(UploadedFile $file = null)
    {
        $this->excelfile = $file;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setCreatedDate(new \DateTime("now"));
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setUpdatedDate(new \DateTime("now"));
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
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return MonthlyPlan
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set updatedDate
     *
     * @param \DateTime $updatedDate
     *
     * @return MonthlyPlan
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * Set planProduct
     *
     * @param \incentive\AppBundle\Entity\PlanProduct $planProduct
     *
     * @return MonthlyPlan
     */
    public function setPlanProduct(\incentive\AppBundle\Entity\PlanProduct $planProduct = null)
    {
        $this->planProduct = $planProduct;

        return $this;
    }

    /**
     * Get planProduct
     *
     * @return \incentive\AppBundle\Entity\PlanProduct
     */
    public function getPlanProduct()
    {
        return $this->planProduct;
    }

    /**
     * Set planBranch
     *
     * @param \incentive\AppBundle\Entity\PlanBranch $planBranch
     *
     * @return MonthlyPlan
     */
    public function setPlanBranch(\incentive\AppBundle\Entity\PlanBranch $planBranch = null)
    {
        $this->planBranch = $planBranch;

        return $this;
    }

    /**
     * Get planBranch
     *
     * @return \incentive\AppBundle\Entity\PlanBranch
     */
    public function getPlanBranch()
    {
        return $this->planBranch;
    }

    /**
     * Set plan
     *
     * @param \incentive\AppBundle\Entity\Plan $plan
     *
     * @return MonthlyPlan
     */
    public function setPlan(\incentive\AppBundle\Entity\Plan $plan = null)
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get plan
     *
     * @return \incentive\AppBundle\Entity\Plan
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Set serviceValue
     *
     * @param integer $serviceValue
     *
     * @return MonthlyPlan
     */
    public function setServiceValue($serviceValue)
    {
        $this->serviceValue = $serviceValue;

        return $this;
    }

    /**
     * Get serviceValue
     *
     * @return integer
     */
    public function getServiceValue()
    {
        return $this->serviceValue;
    }
}
