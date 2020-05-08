<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_BannerSlider
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BannerSlider\Controller\Adminhtml\Banner;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Registry;
use Mageplaza\BannerSlider\Controller\Adminhtml\Banner;
use Mageplaza\BannerSlider\Helper\Image;
use Mageplaza\BannerSlider\Helper\MobileImage;
use Mageplaza\BannerSlider\Helper\TabletImage;
use Mageplaza\BannerSlider\Model\BannerFactory;
use RuntimeException;

/**
 * Class Save
 * @package Mageplaza\BannerSlider\Controller\Adminhtml\Banner
 */
class Save extends Banner
{
    /**
     * JS helper
     *
     * @var Js
     */
    public $jsHelper;
    /**
     * Image Helper
     *
     * @var Image
     */
    protected $imageHelper;
    /**
     * Tablet Image Helper
     *
     * @var TabletImage
     */
    protected $imageTHelper;
    /**
     * Mobile Image Helper
     *
     * @var MobileImage
     */
    protected $imageMHelper;

    /**
     * Save constructor.
     *
     * @param Image $imageHelper
     * @param ImageT $imageTHelper
     * @param ImageM $imageMHelper
     * @param BannerFactory $bannerFactory
     * @param Registry $registry
     * @param Js $jsHelper
     * @param Context $context
     */
    public function __construct(
        Image $imageHelper,
        TabletImage $imageTHelper,
        MobileImage $imageMHelper,
        BannerFactory $bannerFactory,
        Registry $registry,
        Js $jsHelper,
        Context $context
    ) {
        $this->imageHelper = $imageHelper;
        $this->imageTHelper = $imageTHelper;
        $this->imageMHelper = $imageMHelper;
        $this->jsHelper = $jsHelper;

        parent::__construct($bannerFactory, $registry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws FileSystemException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->getRequest()->getPost('banner')) {
            $data = $this->getRequest()->getPost('banner');
            $banner = $this->initBanner();

            $this->imageHelper->uploadImage($data, 'image', Image::TEMPLATE_MEDIA_TYPE_BANNER, $banner->getImage());
            $this->imageTHelper->uploadImage($data, 'tabletimage', TabletImage::TEMPLATE_MEDIA_TYPE_BANNER_TABLET, $banner->getTabletimage());
            $this->imageMHelper->uploadImage($data, 'mobileimage', MobileImage::TEMPLATE_MEDIA_TYPE_BANNER_MOBILE, $banner->getMobileimage());
            $data['sliders_ids'] = (isset($data['sliders_ids']) && $data['sliders_ids'])
            ? explode(',', $data['sliders_ids']) : [];
            if ($this->getRequest()->getPost('sliders', false)) {
                $banner->setTagsData(
                    $this->jsHelper->decodeGridSerializedInput($this->getRequest()->getPost('sliders', false))
                );
            }
            $banner->addData($data);

            // echo "<pre>";
            // print_r($banner->getData());
            // exit;
            $this->_eventManager->dispatch(
                'mpbannerslider_banner_prepare_save',
                [
                    'banner' => $banner,
                    'request' => $this->getRequest(),
                ]
            );
            try {
                $banner->save();
                $this->messageManager->addSuccess(__('The Banner has been saved.'));
                $this->_session->setMageplazaBannerSliderBannerData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mpbannerslider/*/edit',
                        [
                            'banner_id' => $banner->getId(),
                            '_current' => true,
                        ]
                    );

                    return $resultRedirect;
                }
                $resultRedirect->setPath('mpbannerslider/*/');

                return $resultRedirect;
            } catch (RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Banner.'));
            }

            $this->_getSession()->setData('mageplaza_bannerSlider_banner_data', $data);
            $resultRedirect->setPath(
                'mpbannerslider/*/edit',
                [
                    'banner_id' => $banner->getId(),
                    '_current' => true,
                ]
            );

            return $resultRedirect;
        }

        $resultRedirect->setPath('mpbannerslider/*/');

        return $resultRedirect;
    }
}
