<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Fovoso\ImportProducts\Api\Data;


/**
 * Product Interface
 * @api
 * @since 100.0.2
 */
interface ProductInterface
{
    const SKU = 'sku';
    const NAME = 'name';
    const SHORT_DESCRIPTION = 'short_description';
    const DESCRIPTION = 'description';
    const PRICE = 'price';
    const SPECIAL_PRICE = 'special_price';
    const QTY = 'qty';
    const SOLD_OUT = 'sold_out';
    const WEIGHT = 'weight';
    const IMAGE = 'image';
    const SHIPPING_FEE = 'shipping_fee';
    const CATEGORIES = 'categories';
    const SHIPPING = 'shipping';
    const ADDITIONAL_IMAGES = 'additional_images';
    const CHILD_PRODUCTS = 'child_products';
    const ATTRIBUTES = 'attributes';
	const SHIPPING_FROM = 'shipping_from';
	const BRANDS = 'brands';
    const STORE = 'store';

    /**
     * @return mixed
     */
    public function getSku();

    /**
     * @param $sku
     * @return mixed
     */
    public function setSku($sku);

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param $name
     * @return mixed
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getShortDescription();

    /**
     * @param $shortDescription
     * @return mixed
     */
    public function setShortDescription($shortDescription);

    /**
     * @return mixed
     */
    public function getDescription();

    /**
     * @param $description
     * @return mixed
     */
    public function setDescription($description);

    /**
     * @return mixed
     */
    public function getPrice();

    /**
     * @param $price
     * @return mixed
     */
    public function setPrice($price);

    /**
     * @return mixed
     */
    public function getSpecialPrice();

    /**
     * @param $specialPrice
     * @return mixed
     */
    public function setSpecialPrice($specialPrice);

    /**
     * @return mixed
     */
    public function getQty();

    /**
     * @param $qty
     * @return mixed
     */
    public function setQty($qty);

    /**
     * @return mixed
     */
    public function getSoldOut();

    /**
     * @param $soldOut
     * @return mixed
     */
    public function setSoldOut($soldOut);

    /**
     * @return mixed
     */
    public function getWeight();

    /**
     * @param $weight
     * @return mixed
     */
    public function setWeight($weight);

    /**
     * @return mixed
     */
    public function getImage();

    /**
     * @param $image
     * @return mixed
     */
    public function setImage($image);

    /**
     * @return mixed
     */
    public function getShippingFee();

    /**
     * @param $shippingFee
     * @return mixed
     */
    public function setShippingFee($shippingFee);

    /**
     * @return mixed
     */
    public function getCategories();

    /**
     * @param $categories
     * @return mixed
     */
    public function setCategories($categories);

    /**
     * @return mixed
     */
    public function getShipping();

    /**
     * @param $shipping
     * @return mixed
     */
    public function setShipping($shipping);

    /**
     * @return mixed
     */
    public function getAdditionalImages();

    /**
     * @param $additionalImages
     * @return mixed
     */
    public function setAdditionalImages($additionalImages);

    /**
     * @return mixed
     */
    public function getChildProducts();

    /**
     * @param $childProducts
     * @return mixed
     */
    public function setChildProducts($childProducts);

    /**
     * @return mixed
     */
    public function getAttributes();

    /**
     * @param $attributes
     * @return mixed
     */
    public function setAttributes($attributes);

    /**
     * @return mixed
     */
    public function getStore();

    /**
     * @param $store
     * @return mixed
     */
    public function setStore($store);
	
	/**
	* @return mixed
	*/
	public function getShippingFrom();

	/**
	* @param $shippingFrom
	* @return mixed
	*/
	public function setShippingFrom($shippingFrom);

	/**
	* @return mixed
	*/
	public function getBrands();

	/**
	* @param $brands
	* @return mixed
	*/
	public function setBrands($brands);

	

}
