<?php
namespace Fovoso\ImportProducts\Api;

interface RbMessageInterface
{

    /**
     *
     * @return mixed
     */
    public function getMessage();

    /**
     *
     * @param $message
     * @return mixed
     */
    public function setMessage($message);

     /**
     *
     * @return mixed
     */
    public function getType();

    /**
     *
     * @param $type
     * @return mixed
     */
    public function setType($type);

}
