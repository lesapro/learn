<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fovoso\ImportProducts\Api\Data;


/**
 * CommentInterface.
 * @api
 * @since 100.0.2
 */
interface CommentInterface
{
    const NICK_NAME = 'nickName';
    const TITLE = 'title';
    const DETAIL = 'detail';
    const RATING = 'rating';
    const CREATED_AT = 'created_at';
    const IMAGES = 'Images';

    /**
     * @return mixed
     */
    public function getNickName();

    /**
     * @param $nickName
     * @return mixed
     */
    public function setNickName($nickName);

    /**
     * @return mixed
     */
    public function getTitle();

    /**
     * @param $title
     * @return mixed
     */
    public function setTitle($title);

    /**
     * @return mixed
     */
    public function getDetail();

    /**
     * @param $detail
     * @return mixed
     */
    public function setDetail($detail);

    /**
     * @return mixed
     */
    public function getRating();

    /**
     * @param $rating
     * @return mixed
     */
    public function setRating($rating);

    /**
     * @return mixed
     */
    public function getCreatedAt();

    /**
     * @param $createdAt
     * @return mixed
     */
    public function setCreatedAt($createdAt);

    /**
     * @return mixed
     */
    public function getImages();

    /**
     * @param $images
     * @return mixed
     */
    public function setImages($images);
}
