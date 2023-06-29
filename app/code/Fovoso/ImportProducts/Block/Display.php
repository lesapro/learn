<?php
namespace Fovoso\ImportProducts\Block;
class Display extends \Magento\Framework\View\Element\Template
{
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    )
	{
        $this->_state = $state;
        $this->_resourceConnection = $resourceConnection;
		parent::__construct($context);
	}
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
	public function getNumberProductMissing()
	{
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $state = $this->_state;

        $resource = $this->_resourceConnection ;
        $connection = $resource->getConnection();

        $sql1 = "SELECT entity_id, sku FROM catalog_product_entity WHERE type_id='configurable'";
        $result1 = $connection->fetchAll($sql1);

        $configurableArray = array();
        foreach($result1 as $r){
            $sql2 = "SELECT COUNT(*) as count FROM catalog_product_relation WHERE parent_id='". $r['entity_id'] ."'";
            $result2 = $connection->fetchOne($sql2);
            if($result2>0){}else{$configurableArray[] = $r['sku'];}
        }
        $number = count($configurableArray);
		return $number;
	}

    public function listSKUProductError()
    {
        $state = $this->_state;

        $resource = $this->_resourceConnection ;
        $connection = $resource->getConnection();

        $sql1 = "SELECT entity_id, sku FROM catalog_product_entity WHERE type_id='configurable'";
        $result1 = $connection->fetchAll($sql1);

        $configurableArray = array();
        foreach($result1 as $r){
            $sql2 = "SELECT COUNT(*) as count FROM catalog_product_relation WHERE parent_id='". $r['entity_id'] ."'";
            $result2 = $connection->fetchOne($sql2);
            if($result2>0){}else{$configurableArray[] = $r['sku'];}
        }
      
		return $configurableArray;
    }

}