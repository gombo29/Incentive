<?php

namespace incentive\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Branch
 *
 * @ORM\Table(name="branch")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Branch
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     *
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="BranchType")
     * @ORM\JoinColumn(name="branch_type_id", referencedColumnName="id", nullable=true)
     */
    private $branchType;

    /**
     * @ORM\ManyToOne(targetEntity="Branch")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private $parent;

    /**
     * @var boolean
     * @ORM\Column(name="isHrm", type="boolean",nullable=true)
     */
    private $isHRM;

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

    public $cattmp;


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
     * Set id
     *
     * @param integer $id
     *
     * @return Branch
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
     * Set name
     *
     * @param string $name
     *
     * @return Branch
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
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return Branch
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
     * @return Branch
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
     * Set branchType
     *
     * @param \incentive\AppBundle\Entity\BranchType $branchType
     *
     * @return Branch
     */
    public function setBranchType(\incentive\AppBundle\Entity\BranchType $branchType = null)
    {
        $this->branchType = $branchType;

        return $this;
    }

    /**
     * Get branchType
     *
     * @return \incentive\AppBundle\Entity\BranchType
     */
    public function getBranchType()
    {
        return $this->branchType;
    }

    /**
     * Set parent
     *
     * @param \incentive\AppBundle\Entity\Branch $parent
     *
     * @return Branch
     */
    public function setParent(\incentive\AppBundle\Entity\Branch $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \incentive\AppBundle\Entity\Branch
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function getChoiceArray($em, $remid)
    {
        $repository = $em->getRepository('incentiveAppBundle:Branch');
        $entities = $repository->createQueryBuilder('mn')
            ->addSelect('p')
            ->leftJoin('mn.parent', 'p')
            ->orderBy('mn.branchType', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $treeEntities = array();

        foreach ($entities as $entity) {
            if (!$entity['parent']) {
                $this->makeTree($entity, $entities);
                $treeEntities[] = $entity;
            }
        }

        $res = $this->makeFlatZuraas($treeEntities);
        return $res;
    }

    private function makeTree(&$root, $entities)
    {
        $children = array();
        foreach ($entities as $entity) {
            if ($entity['parent']['id'] == $root['id']) {
                $this->makeTree($entity, $entities);
                $children[] = $entity;
            }
        }
        $root['children'] = $children;
    }

    private function makeFlatZuraas($tree, $level = 0)
    {
        $arr = array();
        foreach ($tree as $node) {
            $arr[$node['id']] = $this->getZuras($level, '|---') . $node['name'];
            $arr += $this->makeFlatZuraas($node['children'], $level + 1);
        }
        return $arr;
    }

    public function makeTreeArray($entities, $ch)
    {
        $res = array();
        if ($entities && count($entities) > 0) {
            foreach ($entities as $kd => $e) {
                if (array_key_exists('parent', $e) && !$e['parent']) {
                    array_splice($res, count($res), 0, array($e));
                }
            }
            foreach ($entities as $kd => $e) {
                $e['listtitle'] = $this->getZuras($e['branchType'], $ch);
                if ($e['parent']) {
                    $o = 0;
                    for ($k = 0; $k < count($res); $k++) {
                        $re = $res[$k];
                        if ($re['id'] == $e['parent']['id']) {
                            $o = $k + 1;
                        }
                    }
                    if (count($res) - 1 > $o) {
                        for ($k = $o; $k < count($res); $k++) {
                            $re = $res[$k];
                            if ($e['parent']['id'] == $re['parent']['id']) {
                                if ($re['sortId'] < $e['sortId']) {
                                    $o = $k + 1;
                                } else {
                                    break;
                                }
                            } else {
                                break;
                            }
                        }
                    }
                    array_splice($res, $o, 0, array($e));
                }
            }
        }
        return $res;
    }

    private function getZuras($level, $cc)
    {
        $res = ' ';
        if ($level > 0) {
            for ($i = 0; $i < $level; $i++) {
                $res = $res . $cc;
            }
        }
        return $res;
    }



    /**
     * Set isHRM
     *
     * @param boolean $isHRM
     *
     * @return Branch
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
