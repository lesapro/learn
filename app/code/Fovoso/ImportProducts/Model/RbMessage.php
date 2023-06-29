<?php

namespace Fovoso\ImportProducts\Model;


use Fovoso\ImportProducts\Api\RbMessageInterface;

class RbMessage implements RbMessageInterface
{
    protected $message;
    protected $type;

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}
