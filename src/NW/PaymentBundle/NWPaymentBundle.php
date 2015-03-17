<?php
namespace NW\PaymentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NWPaymentBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'PayumBundle';
    }
}