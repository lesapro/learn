<?php

namespace Fovoso\Shipping\Model;

/**
 * Class Express
 * @package Fovoso\Shipping\Model
 */
class Express extends Carrier
{
    /** @const */
    const CODE = 'tn_express';

    /**
     * {@inheritdoc}
     */
    protected $_code = self::CODE;
}
