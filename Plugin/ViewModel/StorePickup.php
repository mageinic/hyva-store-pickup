<?php

namespace Hyva\MageINICStorePickup\Plugin\ViewModel;

use MageINIC\StorePickup\Api\Data\StorePickupInterface;
use MageINIC\StorePickup\Helper\Data;
use MageINIC\StorePickup\Model\StorePickup\Image;
use MageINIC\StorePickup\ViewModel\StorePickup as StorePickupBlock;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class StorePickup
{
    /**
     * @var Image
     */
    protected Image $storePickupImage;

    /**
     * @var Escaper
     */
    protected Escaper $escaper;

    /**
     * @var Data
     */
    protected Data $helperData;

    /**
     * StorePickup Constructor
     *
     * @param Image $storePickupImage
     * @param Escaper $escaper
     * @param Data $helperData
     */
    public function __construct(
        Image   $storePickupImage,
        Escaper $escaper,
        Data    $helperData
    ) {
        $this->storePickupImage = $storePickupImage;
        $this->escaper = $escaper;
        $this->helperData = $helperData;
    }

    /**
     * Receive Store Pickup info window
     *
     * @param StorePickupBlock $subject
     * @param callable $proceed
     * @param StorePickupInterface $pickupStore
     * @return string[]
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function aroundGetStoreInfo(
        StorePickupBlock     $subject,
        callable             $proceed,
        StorePickupInterface $pickupStore
    ): array {

        $addressText = $this->escaper->escapeHtml(__('Address:'));
        $stateText = $this->escaper->escapeHtml(__('State:'));
        $cityText = $this->escaper->escapeHtml(__('City:'));
        $zipText = $this->escaper->escapeHtml(__('Zip:'));
        $descriptionText = $this->escaper->escapeHtml(__('Description:'));

        $image = $subject->getStoreImageUrl($pickupStore);
        $name = $pickupStore->getName();
        $url = $pickupStore->getUrl();
        $address = $pickupStore->getAddress();
        $region = $pickupStore->getRegion();
        $city = $pickupStore->getCity();
        $postcode = $pickupStore->getPostcode();
        $content = $this->helperData->trimContent($pickupStore->getContent());

        return [
            "<div class='store-marker-window' style='width: 250px'>
                <h3 class='mi-pickup-name font-bold font-base mb-2'>
                    <div class='mi-pickup-title'>
                        <a class='mi-pickup-link' href='{$this->escaper->escapeUrl($url)}'
                            title='{$this->escaper->escapeHtmlAttr($name)}'
                            target='_blank' tabindex='0'>{$this->escaper->escapeHtml($name)}</a>
                    </div>
                </h3>
                <div class='mi-pickup-image'>
                    <div class='mi-pickup-image'>
                        <img class='w-full' style='max-width: 100%' src='{$this->escaper->escapeUrl($image)}'
                            alt='{$this->escaper->escapeHtml($name)}'/>
                    </div>
                </div>
                <div class='mi-pickup-address mt-2'>
                    <span><strong>{$addressText}</strong>{$this->escaper->escapeHtml($address)}</span>
                    <span><strong>{$stateText}</strong>{$this->escaper->escapeHtml($region)}</span>
                    <span><strong>{$cityText}</strong>{$this->escaper->escapeHtml($city)}</span>
                    <span><strong>{$zipText}</strong>{$this->escaper->escapeHtml($postcode)}</span>
                    <span><strong>{$descriptionText}</strong></span>
                </div>
                <div class='mi-pickup-description'>{$this->escaper->escapeHtml($content)}
                    <a href='{$this->escaper->escapeUrl($url)}' class='text-[#006bb4]' title='read more'
                    target='_blank'>{$this->escaper->escapeHtml(__('Read More'))}</a>
                </div><br>
            </div>"
        ];
    }
}
