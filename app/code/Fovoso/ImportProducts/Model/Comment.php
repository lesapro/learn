<?php

namespace Fovoso\ImportProducts\Model;

use Fovoso\ImportProducts\Api\Data\CommentInterface;

class Comment extends \Magento\Framework\DataObject implements CommentInterface
{
    /**
     * @inheritDoc
     */
    public function getNickName()
    {
        return $this->getData(self::NICK_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setNickName($nickName)
    {
        return $this->setData(self::NICK_NAME, $nickName);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritDoc
     */
    public function getDetail()
    {
        return $this->getData(self::DETAIL);
    }

    /**
     * @inheritDoc
     */
    public function setDetail($detail)
    {
        return $this->setData(self::DETAIL, $detail);
    }

    /**
     * @inheritDoc
     */
    public function getRating()
    {
        return $this->getData(self::RATING);
    }

    /**
     * @inheritDoc
     */
    public function setRating($rating)
    {
        return $this->setData(self::RATING, $rating);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getImages()
    {
        return $this->getData(self::IMAGES);
    }
    
    /**
     * @inheritDoc
     */
    public function setImages($images)
    {
        return $this->setData(self::IMAGES, $images);
    }
}
