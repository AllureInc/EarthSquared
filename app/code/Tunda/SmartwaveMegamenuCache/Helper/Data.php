<?php
namespace Tunda\SmartwaveMegamenuCache\Helper;
use Magento\Framework\View\Result\PageFactory;
class Data extends \Smartwave\Megamenu\Helper\Data
{
    const MEGAMENU	=	'megamenu';

    protected $_objectManager;
    protected $_categoryHelper;
    protected $_categoryFactory;
    protected $_categoryFlatConfig;
    protected $_filterProvider;
    protected $resultPageFactory;
    protected $directory_list;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        PageFactory $resultPageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list
    ) {
        $this->_storeManager = $storeManager;
        $this->_objectManager= $objectManager;
        $this->_categoryFactory = $categoryFactory;
        $this->_categoryFlatConfig = $categoryFlatState;
        $this->_categoryHelper = $categoryHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->_filterProvider = $filterProvider;
        $this->directory_list = $directory_list;

        \Magento\Framework\App\Helper\AbstractHelper::__construct($context);
    }


    protected function _getStoreId()
    {
        return $this->_storeManager->getStore()->getStoreId();
    }
    
    protected function _getCachePath()
    {
        return $this->directory_list->getPath('var').'/cache';
    }
    
    protected function _getMegamenuFileName()
    {
    	return $this->_getCachePath().'/'.self::MEGAMENU.'_'.$this->_getStoreId().'.html';
    }
    
    protected function _existMenuFile( $filename )
    {
    	return file_exists($filename);
    }
    
    protected function _readMenuFile( $filename )
    {
    	$handle = fopen($filename, "r");
    	$menu = fread($handle, filesize($filename));
    	fclose($handle);
    	return $menu;
    }
    
    protected function _writeMenuFile( $html, $filename )
    {
	@mkdir($this->_getCachePath());	    
	$fp = fopen($filename, 'w');
    	fwrite($fp, $html);
    	fclose($fp);
    }
    
    public function hasMegamenuCached()
    {
    	return $this->_existMenuFile($this->_getMegamenuFileName());
    }
    
    public function getMegamenuCached()
    {
    	return $this->_readMenuFile($this->_getMegamenuFileName());
    }
    
    public function setMegamenuCached( $html )
    {
    	$this->_writeMenuFile($html, $this->_getMegamenuFileName());
    	return $this;
    
    }
    
    
    public function clearMenu()
    {
    	$files = preg_grep('/^([^.])/', scandir($this->_getCachePath()));
    	if($files){
    		foreach($files as $key => $file){
    			if($file!='.' && $file!='..')
    			{
    				if(is_file($this->_getCachePath().'/'.$file))
    				{
    					preg_match('/^'.self::MEGAMENU.'_/', $file, $matches);
    					if($matches)
    					{
    						unlink($this->_getCachePath().'/'.$file);
    					}
    				}
    			}
    		}
    	}
    }
}
