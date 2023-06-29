<?php 

namespace Magiccart\Shopbrand\Block\Widget;

class Listbrands extends Brand
{
    protected $_template = "listbrands.phtml";
  
    public function getNumberBrands(){
        return $this->getData('numberbrand');
    }

    public function getListBrands(){
        $collection = $this->getBrandCollection();
        $collection->getSelect()->limit($this->getNumberBrands());
        $collection->setOrder('orders','DESC')->getSelect()->limit($this->getNumberBrands());
        // print_r($collection->getSelect()->__toString());die;
        return $collection;
    }
   
}