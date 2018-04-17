<?php

namespace incentive\AppBundle\Functions;

use incentive\AppBundle\Entity\Role;
use Symfony\Component\DependencyInjection\ContainerInterface;


class SpecialFunction
{
    private $container;
    private $em;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getManager();
    }

    public function getRoles($user)
    {
        $firstRole = null;
        foreach ($user->getRoles() as $role) {
            $firstRole = $role;
            break;
        }

        $qb = $this->em->getRepository('incentiveAppBundle:Role')->createQueryBuilder('p');
        /**@var Role $role */
        $role = $qb
            ->where('p.name like :name')
            ->setParameter('name', '%' . $firstRole . '%')
            ->getQuery()
            ->getArrayResult();
        $roleName = $role[0]['disname'];
        return $roleName;
    }
}
