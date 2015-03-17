<?php

namespace NW\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NWUserBundle extends Bundle
{
	public function getParent()
    {
        return 'HWIOAuthBundle';
    }
}