<?php
namespace incentive\AppBundle\Security;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

class PasswordEncoder extends BasePasswordEncoder
{
    public function encodePassword($raw, $salt)
    {
        return md5($raw);
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $this->comparePasswords($encoded, $this->encodePassword($raw, $salt));
    }
}