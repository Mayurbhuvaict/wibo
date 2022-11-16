<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Controller;

use DOMDocument;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

/**
 * @RouteScope(scopes={"api"})
 */
class StockImportController extends AbstractController
{

    /**
     * @var EntityRepositoryInterface
     */
    private $productsRepository;

    public function __construct(
        EntityRepositoryInterface $productsRepository
    ) {
        $this->productsRepository = $productsRepository;
    }

    /**
     * @Route("/api/zeostock/aepsupplier", name="api.action.zeo.aep.import", methods={"GET"})
     */
    public function stockImport(Context $context): JsonResponse
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
        $criteria->addFilter(new EqualsFilter('customFields.migration_attribute_15_inkooppartij_197', 'option_1701'));
        $criteria->addFilter(
            new MultiFilter(
                MultiFilter::CONNECTION_OR,
                [
                    new EqualsFilter('manufacturer.name', 'atag'),
                    new EqualsFilter('manufacturer.name', 'pelgrim'),
                    new EqualsFilter('manufacturer.name', 'etna'),
                    new EqualsFilter('manufacturer.name', 'asko'),
                    new EqualsFilter('manufacturer.name', 'gisense'),
                ]
            )
        );
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
                'migration_attribute_13_inv_leverdatum_262' => $date
            ],
        ];
        $this->productsRepository->upsert([$data], $context);
    }
}
