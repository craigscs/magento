<?php

namespace Gradus\Compatibility\Model\Product\Initialization\Helper;

use Gradus\Compatibility\Model\Catalog\Product\Link;
use Magento\Catalog\Api\Data\ProductLinkExtensionFactory;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

class ProductLinks
{
    /**
     * String name for link type
     */
    const TYPE_NAME = 'compatibility';
    const TYPE_NAME2 = 'accessories';
    const TYPE_NAME3 = 'parts';
    /**
     * @var ProductLinkInterfaceFactory
     */
    protected $productLinkFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ProductLinkExtensionFactory
     */
    protected $productLinkExtensionFactory;

    /**
     * Init
     *
     * @param ProductLinkInterfaceFactory $productLinkFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ProductLinkExtensionFactory $productLinkExtensionFactory
     */
    public function __construct(
        ProductLinkInterfaceFactory $productLinkFactory,
        ProductRepositoryInterface $productRepository,
        ProductLinkExtensionFactory $productLinkExtensionFactory
    ) {
        $this->productLinkFactory = $productLinkFactory;
        $this->productRepository = $productRepository;
        $this->productLinkExtensionFactory = $productLinkExtensionFactory;
    }

    public function beforeInitializeLinks(
        \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $subject,
        \Magento\Catalog\Model\Product $product,
        array $links
    )
    {
        $full = $links;
        $newLinks = [];
        if(isset($full[self::TYPE_NAME]) && !$product->getCompatibilityReadonly()) {

            $links = (isset($full[self::TYPE_NAME])) ? $full[self::TYPE_NAME] : $product->getCompatibilityLinkData();
            if (!is_array($links)) {
                $links = [];
            }
            if ($product->getCompatibilityLinkData()) {
                $links = array_merge($links, $product->getCompatibilityLinkData());
            }
            foreach ($links as $linkRaw) {
                /** @var \Magento\Catalog\Api\Data\ProductLinkInterface $productLink */
                $productLink = $this->productLinkFactory->create();
                if (!isset($linkRaw['id'])) {
                    continue;
                }
                $productId = $linkRaw['id'];
                if (!isset($linkRaw['qty'])) {
                    $linkRaw['qty'] = 0;
                }
                $linkedProduct = $this->productRepository->getById($productId);

                $productLink->setSku($product->getSku())
                    ->setLinkType(self::TYPE_NAME)
                    ->setLinkedProductSku($linkedProduct->getSku())
                    ->setLinkedProductType($linkedProduct->getTypeId())
                    ->setPosition($linkRaw['position'])
                    ->getExtensionAttributes()
                    ->setQty($linkRaw['qty']);
                $newLinks[] = $productLink;
            }
                    }

        if(isset($full[self::TYPE_NAME2]) && !$product->getAccessoriesReadonly()) {

            $links = (isset($full[self::TYPE_NAME2])) ? $full[self::TYPE_NAME2] : $product->getAccessoriesLinkData();
            if (!is_array($links)) {
                $links = [];
            }

            if ($product->getAccessoriesLinkData()) {
                $links = array_merge($links, $product->getAccessoriesLinkData());
            }
            foreach ($links as $linkRaw) {
                /** @var \Magento\Catalog\Api\Data\ProductLinkInterface $productLink */
                $productLink = $this->productLinkFactory->create();
                if (!isset($linkRaw['id'])) {
                    continue;
                }
                $productId = $linkRaw['id'];
                if (!isset($linkRaw['qty'])) {
                    $linkRaw['qty'] = 0;
                }
                $linkedProduct = $this->productRepository->getById($productId);

                $productLink->setSku($product->getSku())
                    ->setLinkType(self::TYPE_NAME2)
                    ->setLinkedProductSku($linkedProduct->getSku())
                    ->setLinkedProductType($linkedProduct->getTypeId())
                    ->setPosition($linkRaw['position'])
                    ->getExtensionAttributes()
                    ->setQty($linkRaw['qty']);

                $newLinks[] = $productLink;
            }
        }

        if(isset($full[self::TYPE_NAME3]) && !$product->getPartsReadOnly()) {

            $links = (isset($full[self::TYPE_NAME3])) ? $full[self::TYPE_NAME3] : $product->getPartsLinkData();
            if (!is_array($links)) {
                $links = [];
            }

            if ($product->getPartsLinkData()) {
                $links = array_merge($links, $product->getPartsData());
            }
            foreach ($links as $linkRaw) {
                /** @var \Magento\Catalog\Api\Data\ProductLinkInterface $productLink */
                $productLink = $this->productLinkFactory->create();
                if (!isset($linkRaw['id'])) {
                    continue;
                }
                $productId = $linkRaw['id'];
                if (!isset($linkRaw['qty'])) {
                    $linkRaw['qty'] = 0;
                }
                $linkedProduct = $this->productRepository->getById($productId);

                $productLink->setSku($product->getSku())
                    ->setLinkType(self::TYPE_NAME3)
                    ->setLinkedProductSku($linkedProduct->getSku())
                    ->setLinkedProductType($linkedProduct->getTypeId())
                    ->setPosition($linkRaw['position'])
                    ->getExtensionAttributes()
                    ->setQty($linkRaw['qty']);

                $newLinks[] = $productLink;
            }
        }
        $existingLinks = $product->getProductLinks();
        $existingLinks = $this->removeUnExistingLinks($existingLinks, $newLinks);
        $product->setProductLinks(array_merge($existingLinks, $newLinks));
    }

    /**
     * Removes unexisting links
     *
     * @param array $existingLinks
     * @param array $newLinks
     * @return array
     */
    private function removeUnExistingLinks($existingLinks, $newLinks)
    {
        $result = [];
        foreach ($existingLinks as $key => $link) {
            $result[$key] = $link;
            if ($link->getLinkType() == self::TYPE_NAME) {
                $exists = false;
                foreach ($newLinks as $newLink) {
                    if ($link->getLinkedProductSku() == $newLink->getLinkedProductSku()) {
                        $exists = true;
                    }
                }
                if (!$exists) {
                    unset($result[$key]);
                }
            }
            if ($link->getLinkType() == self::TYPE_NAME2) {
                $exists = false;
                foreach ($newLinks as $newLink) {
                    if ($link->getLinkedProductSku() == $newLink->getLinkedProductSku()) {
                        $exists = true;
                    }
                }
                if (!$exists) {
                    unset($result[$key]);
                }
            }
            if ($link->getLinkType() == self::TYPE_NAME3) {
                $exists = false;
                foreach ($newLinks as $newLink) {
                    if ($link->getLinkedProductSku() == $newLink->getLinkedProductSku()) {
                        $exists = true;
                    }
                }
                if (!$exists) {
                    unset($result[$key]);
                }
            }
        }
        return $result;
    }

}