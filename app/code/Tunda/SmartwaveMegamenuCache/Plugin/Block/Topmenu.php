<?php
namespace Tunda\SmartwaveMegamenuCache\Plugin\Block;

class Topmenu
{    

	protected $_helper;
	
	public function __construct(
			\Tunda\SmartwaveMegamenuCache\Helper\Data $helper
			) {			
        $this->_helper = $helper;

		return $this;
	}

    public function aroundGetMegamenuHtml(\Smartwave\Megamenu\Block\Topmenu $subject, callable $proceed)
    {
        if (!$this->_helper->hasMegamenuCached())
        {
            $html = $proceed();
            $this->_helper->setMegamenuCached($html);
        }
        else{
            $html = $this->_helper->getMegamenuCached();
        }
        return $html;
    }
     
}
