<?php
require_once '../app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


ini_set('display_errors', 1);
umask(0);

 

for( $i=0; $i < 50; $i++ )
{ 
	try {
		$rand = rand( 1, 1000);
		$storeId    = 0;
		$category = Mage::getModel('catalog/category');
		$category->setStoreId($storeId);
		$category->setName('Test '.$rand.' Category');
	  //  $category->setUrlKey('Test category');
		$category->setIsActive(1);
		$category->setDisplayMode('PRODUCTS');
		//$parentId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
		$parentId = 2;
		$parentCategory = Mage::getModel('catalog/category')->load($parentId);
		$category->setPath($parentCategory->getPath());
		$category->save();
		echo $category->getId();
	
	} catch (Exception $e ) {
		echo "ERROR: {$e->getMessage()}\n";	
	}	
}
        
?>