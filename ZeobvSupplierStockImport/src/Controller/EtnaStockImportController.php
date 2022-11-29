<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\Controller;

use DOMDocument;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

/**
 * @RouteScope(scopes={"api"})
 */
class EtnaStockImportController extends AbstractController
{

    /**
     * @var EntityRepositoryInterface
     */
    private $productsRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $supplierStockRepository;

    public function __construct(
        EntityRepositoryInterface $productsRepository,
        EntityRepositoryInterface $supplierStockRepository
    ) {
        $this->productsRepository = $productsRepository;
        $this->supplierStockRepository = $supplierStockRepository;
    }

    /**
     * @Route("/api/zeostock/etnasupplier", name="api.action.zeo.etna.import", methods={"GET"})
     */
    public function etnaStockImport(Context $context): JsonResponse
    {
        $username = 'SCHOUWWIT';
        $password = '4A2BbE99d';
        $url = "https://www.atagonline.nl/order?salesatp";

        $doc = new DOMDocument();
        try {
            $atp = $doc->appendChild($doc->createElement('ATP'));
            $atp->setAttribute('Mode', 'production');
            $atp->appendChild($doc->createElement('Source'));
            $atp->appendChild($doc->createElement('Version', 'A010'));
            $atp->appendChild($doc->createElement('Buyer', '262281'));
            $i = 1;
            $allProducts = $this->getAllProduct($context);
            if ($allProducts) {
                foreach ($allProducts as $product) {
                    $item = $atp->appendChild($doc->createElement('Item'));
                    $item->appendChild($doc->createElement('ItemNumber', (string)$i));
                    $item->appendChild($doc->createElement('EAN', $product->getean()));
                    $item->appendChild($doc->createElement('ArticleCode', $product->getproductNumber()));
                    $item->appendChild($doc->createElement('Quantity', '1'));
                    $i++;
                }
            }
        } catch (\DOMException $e) {
        }

        $doc->formatOutput = true;
        $finalXml = $doc->saveXML();

        $headers = array(
            "Content-type: text/xml",
            "Content-length: " . strlen($finalXml),
            "Authorization: Basic " . base64_encode($username . ':' . $password)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $finalXml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        $data = simplexml_load_string(curl_exec($ch));
        curl_close($ch);

        $message = 'Data not found';
        if ($data) {
            foreach ($data->Item as $item) {
                $product = $this->getProduct((string)$item->EAN, $context);
                if ($product) {
                    $dataExist = $this->checkSupplierTable($product, $item, $context);
                    if (empty($dataExist)) {
                        switch ($item->ATPCode) {
                            case '1':
                                $message = 'Stock Updated';
                                $d = $item->PossibleDate;
                                $date = substr($d, -2) . '-' . substr($d, 4, 2) . '-' . substr($d, 0, 4);
                                $date = date('d-m-Y', (strtotime('-2 weekdays', strtotime($date))));
                                $stock = $item->Quantity;
                                $this->updateProduct($product, $stock, $date, $context);
                                break;
                            case '2':
                                $message = 'Update Stock 0';
                                $stock = 0;
                                $d = $item->PossibleDate;
                                $date = substr($d, -2) . '-' . substr($d, 4, 2) . '-' . substr($d, 0, 4);
                                $date = date('d-m-Y', (strtotime('-2 weekdays', strtotime($date))));
                                $this->updateProduct($product, $stock, $date, $context);
                                break;
                            case '9':
                                $message = 'error code '.$item->ATPCode.':'.$item->EAN.' | '.$item->ArticleCode. PHP_EOL;
                                $stock = 0;
                                $date = '';
                                $this->updateProduct($product, $stock, $date, $context);
                                break;
                            default:
                                break;
                        }
                        $this->updateSupplierTable($product, $item, $dataExist, $context);
                    }
                }
            }
        }

        return new JsonResponse(
            [
                'type'=>'success',
                'message' => $message
            ]
        );
    }

    public function getAllProduct(Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addAssociation('manufacturer');
        $criteria->addFilter(new EqualsFilter('customFields.migration_attribute_12_inkooppartij_197', 'option_1701'));
        $criteria->addFilter(new EqualsFilter('manufacturer.name', 'etna'));
        return $this->productsRepository->search($criteria, $context)->getEntities()->getElements();
    }

    public function getProduct($ean, Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('ean', $ean));
        return $this->productsRepository->search($criteria, $context)->first();
    }

    public function updateProduct($product, $stock, $date, $context)
    {
        $data = [
            'id'            => $product->getId(),
            'stock'         => $stock,
            'customFields' => [
                'migration_attribute_12_inv_leverdatum_262' => $date
            ],
        ];
        $this->productsRepository->upsert([$data], $context);
    }

    public function checkSupplierTable($product, $apiData, Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $product->getId()));
        $criteria->addFilter(new EqualsFilter('etnaApiRecord', json_encode($apiData)));
        return $this->supplierStockRepository->search($criteria, $context)->first();
    }

    public function updateSupplierTable($product, $apiData, $dataExist, Context $context)
    {
        $data = [
            'id'            => $dataExist ? $dataExist->getId() : Uuid::randomHex(),
            'productId'     => $product->getId(),
            'eanNumber'     => $product->getEan(),
            'etnaApiRecord'  => json_encode($apiData),
        ];
        $this->supplierStockRepository->upsert([$data], $context);
    }
}
