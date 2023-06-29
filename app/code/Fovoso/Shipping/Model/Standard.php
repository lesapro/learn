<?php

namespace Fovoso\Shipping\Model;

/**
 * Class Standard
 * @package Fovoso\Shipping\Model
 */
class Standard extends Carrier
{
    /** @const */
    const CODE = 'tn_standard';

    /**
     * {@inheritdoc}
     */
    protected $_code = self::CODE;
}
