<?php

namespace Fovoso\ImportProducts\Model;

use Fovoso\ImportProducts\Api\Data\ProductInterface;

/**
 *
 */
class Product extends \Magento\Framework\DataObject implements ProductInterface
{
    /**
     * @return mixed|null
     */
    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    /**
     * @param $sku
     * @return Product|mixed
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * @return mixed|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param $name
     * @return Product|mixed
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @return mixed|null
     */
    public function getShortDescription()
    {
        return $this->getData(self::SHORT_DESCRIPTION);
    }

    /**
     * @param $shortDescription
     * @return Product|mixed
     */
    public function setShortDescription($shortDescription)
    {
        return $this->setData(self::SHORT_DESCRIPTION, $shortDescription);
    }

    /**
     * @return mixed|null
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @param $description
     * @return Product|mixed
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @return mixed|null
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @param $price
     * @return Product|mixed
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * @return mixed|null
     */
    public function getSpecialPrice()
    {
        return $this->getData(self::SPECIAL_PRICE);
    }

    /**
     * @param $specialPrice
     * @return Product|mixed
     */
    public function setSpecialPrice($specialPrice)
    {
        return $this->setData(self::SPECIAL_PRICE, $specialPrice);
    }

    /**
     * @return mixed|null
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * @param $qty
     * @return Product|mixed
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * @return mixed|null
     */
    public function getSoldOut()
    {
        return $this->getData(self::SOLD_OUT);
    }

    /**
     * @param $soldOut
     * @return Product|mixed
     */
    public function setSoldOut($soldOut)
    {
        return $this->setData(self::SOLD_OUT, $soldOut);
    }

    /**
     * @return mixed|null
     */
    public function getWeight()
    {
        return $this->getData(self::WEIGHT);
    }

    /**
     * @param $weight
     * @return Product|mixed
     */
    public function setWeight($weight)
    {
        return $this->setData(self::WEIGHT, $weight);
    }

    /**
     * @return mixed|null
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * @param $image
     * @return Product|mixed
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * @return mixed|null
     */
    public function getShippingFee()
    {
        return $this->getData(self::SHIPPING_FEE);
    }

    /**
     * @param $shippingFee
     * @return Product|mixed
     */
    public function setShippingFee($shippingFee)
    {
        return $this->setData(self::SHIPPING_FEE, $shippingFee);
    }

    /**
     * @return mixed|null
     */
    public function getCategories()
    {
        return $this->getData(self::CATEGORIES);
    }

    /**
     * @param $categories
     * @return Product|mixed
     */
    public function setCategories($categories)
    {
        return $this->setData(self::CATEGORIES, $categories);
    }

    /**
     * @return mixed|null
     */
    public function getShipping()
    {
        return $this->getData(self::SHIPPING);
    }

    /**
     * @param $shipping
     * @return Product|mixed
     */
    public function setShipping($shipping)
    {
        return $this->setData(self::SHIPPING, $shipping);
    }

    /**
     * @return mixed|null
     */
    public function getAdditionalImages()
    {
        return $this->getData(self::ADDITIONAL_IMAGES);
    }

    /**
     * @param $additionalImages
     * @return Product|mixed
     */
    public function setAdditionalImages($additionalImages)
    {
        return $this->setData(self::ADDITIONAL_IMAGES, $additionalImages);
    }

    /**
     * @return mixed|null
     */
    public function getChildProducts()
    {
        return $this->getData(self::CHILD_PRODUCTS);
    }

    /**
     * @param $childProducts
     * @return Product|mixed
     */
    public function setChildProducts($childProducts)
    {
        return $this->setData(self::CHILD_PRODUCTS, $childProducts);
    }

    /**
     * @return mixed|null
     */
    public function getAttributes()
    {
        return $this->getData(self::ATTRIBUTES);
    }

    /**
     * @param $attributes
     * @return Product|mixed
     */
    public function setAttributes($attributes)
    {
        return $this->setData(self::ATTRIBUTES, $attributes);
    }

    /**
     * @return mixed|null
     */
    public function getStore()
    {
        return $this->getData(self::STORE);
    }

    /**
     * @param $store
     * @return Product|mixed
     */
    public function setStore($store)
    {
        return $this->setData(self::STORE, $store);
	
    }
	public function getShippingFrom()
	{
    return $this->_getData(\Fovoso\ImportProducts\Api\Data\ProductInterface::SHIPPING_FROM);
	}

	public function setShippingFrom($shippingFrom)
	{
    return $this->setData(\Fovoso\ImportProducts\Api\Data\ProductInterface::SHIPPING_FROM, $shippingFrom);
	}

	public function getBrands()
	{
    return $this->_getData(\Fovoso\ImportProducts\Api\Data\ProductInterface::BRANDS);
	}

	public function setBrands($brands)
	{
    return $this->setData(\Fovoso\ImportProducts\Api\Data\ProductInterface::BRANDS, $brands);
	}

}
