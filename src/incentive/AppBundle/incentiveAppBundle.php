<?php

namespace incentive\AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class incentiveAppBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
