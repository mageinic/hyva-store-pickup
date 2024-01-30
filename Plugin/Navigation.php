<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package Hyva_MageINICStorePickup
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace Hyva\MageINICStorePickup\Plugin;

use Hyva\Theme\Service\Navigation as NavigationService;
use Hyva\Theme\ViewModel\Navigation as HyvaNavigation;
use MageINIC\StorePickup\Helper\Data;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;

/**
 * Plugin Class Hyva Navigation
 */
class Navigation
{
    /**
     * @var NavigationService
     */
    protected NavigationService $navigationService;

    /**
     * @var Data
     */
    private Data $helperData;

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * Navigation Constructor.
     *
     * @param NavigationService $navigationService
     * @param Data $helperData
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        NavigationService $navigationService,
        Data              $helperData,
        UrlInterface      $urlBuilder
    ) {
        $this->navigationService = $navigationService;
        $this->helperData = $helperData;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Plugin after Get Navigation
     *
     * @param HyvaNavigation $subject
     * @param array $result
     * @param bool|int $maxLevel
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGetNavigation(
        HyvaNavigation $subject,
        array          $result,
        bool|int       $maxLevel = false
    ): array {

        $menu = $this->navigationService->getMenuTree($maxLevel);
        $tree = $menu->getTree();

        $menuLabel = $this->helperData->getTitle();
        $urlKey = $this->helperData->getRoute();

        if ($this->helperData->getPosition() === 'navigation' && $menuLabel && $urlKey) {

            $data = $this->getNodeAsArray($menuLabel, $urlKey);

            $node = new Node($data, 'id', $tree, $menu);
            $menu->addChild($node);
        }

        return $result;
    }

    /**
     * Receive Node as Array.
     *
     * @param string $menuLabel
     * @param string $urlKey
     * @return array
     */
    protected function getNodeAsArray(string $menuLabel, string $urlKey): array
    {
        $currentUrl = $this->urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        return [
            'name' => __($menuLabel),
            'id' => $urlKey,
            'url' => $this->urlBuilder->getUrl($urlKey),
            'is_active' => str_contains($currentUrl, $urlKey),
        ];
    }
}
