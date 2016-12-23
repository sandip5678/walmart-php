<?php
require_once '../app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


ini_set('display_errors', 1);
umask(0);


if (!$handle = fopen("CategoryTree.txt", "r"))
	die('Failed to open file');

$last_offsets = 0;
$last_item_per_offset = array();
while (($line = fgets($handle)) !== false) 
{
	$offset = strlen(substr($line, 0, strpos($line,'-')));
	$cat_name = trim(substr($line, $offset+1));
	
	$category_collection = Mage::getModel('catalog/category')->getCollection()
								->addFieldToFilter('name', $cat_name)
								->setPageSize(1);
	
							
	if (isset($last_item_per_offset[$offset-1]))
	{
		$category_collection->addAttributeToFilter('parent_id', (int)$last_item_per_offset[$offset-1]->getId());
	}
	
	if ($category_collection->count()) // item exists, move on to next tree item
	{
		$last_item_per_offset[$offset] = $category_collection->getFirstItem();
		continue;
	}
	else
	{
		if ($offset-1 == 0 && !isset($last_item_per_offset[$offset-1])) // no root item found
		{
			echo "ERROR: root category not found. Please create the root\n";
		}
		else if(!isset($last_item_per_offset[$offset-1])) // no parent found. something must be wrong in the file
		{
			echo "ERROR: parent item does not exist. Please check your tree file\n";
		}
		
		$parentitem = $last_item_per_offset[$offset-1];
		
		// create a new category item
		$category = Mage::getModel('catalog/category');
		$category->setStoreId(0);
		 
		$category->addData(array(
			'name' 			=> $cat_name,
			'meta_title'	=> $cat_name,
			'display_mode'	=> Mage_Catalog_Model_Category::DM_PRODUCT,
			'is_active'		=> 1,
			'is_anchor'		=> 1,
			'path'			=> $parentitem->getPath(),
		));
		 
		try {
			$category->save();
		} catch (Exception $e){
			echo "ERROR: {$e->getMessage()}\n";
			die();
		}
		
		$last_item_per_offset[$offset] = $category;
		echo "> Created category '{$cat_name}'\n";
	}
}
fclose($handle);

/*for( $i=0; $i < 20; $i++ )
{ 
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
    //echo $category->getId();
}
*/

        
?>