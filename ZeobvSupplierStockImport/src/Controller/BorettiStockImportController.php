<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\Controller;

use Shopware\Core\Framework\Uuid\Uuid;
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
class BorettiStockImportController extends AbstractController
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
     * @Route("/api/zeostock/borettisupplier", name="api.action.zeo.boretti.import", methods={"GET"})
     */
    public function borettiStockImport(Context $context): JsonResponse
    {
        $ch = curl_init();
        $authorization = "Authorization: Bearer 080042cad6356ad5dc0a720c18b53b8e53d4c274";
        $source = "https://middleware.boretti.weneverletyoudown.nl/catalog/517c0eca-7185-4877-98d7-1e72ed669ea0/catalog.json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization));
        curl_setopt($ch, CURLOPT_URL, $source);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        $borettiData = json_decode($data, true);
        if ($borettiData) {
            unset($borettiData['error']);
            foreach ($borettiData as $borettiProduct) {
                if ($borettiProduct) {
                    $aantal = count($borettiProduct);
                    for ($x = 0; $x < $aantal; $x++) {
                        $leverdatumreset = 'nee';
                        $apiData = $borettiProduct[$x];
                        $ean = $borettiProduct[$x]['ean_code'];
                        $date = $borettiProduct[$x]['updated_at'];
                        $updatedDate = date('Y-m-d', strtotime($date));
                        if (!empty($ean)) {
                            $product = $this->getProduct($ean, $context);
                            if ($product) {
                                $dataExist = $this->checkSupplierTable($product, $apiData, $updatedDate, $context);
                                if (empty($dataExist)) {
                                    if ($borettiProduct[$x]['stock'] == 'in_stock') {
                                        file_put_contents(
                                            "BorettiImportLog.txt",
                                            $borettiProduct[$x]['item_code'] . ' met ean code ' .
                                            $borettiProduct[$x]['ean_code'] . ' is op voorraad bij Boretti',
                                            FILE_APPEND
                                        );
                                        $borettivoorraad = '10';
                                        $uitzetten = 'nee';
                                    } elseif ($borettiProduct[$x]['stock'] == 'limited_stock') {
                                        file_put_contents(
                                            "BorettiImportLog.txt",
                                            $borettiProduct[$x]['item_code'] . ' met ean code ' .
                                            $borettiProduct[$x]['ean_code'] . ' is BEPERKT voorraad bij Boretti',
                                            FILE_APPEND
                                        );
                                        $borettivoorraad = '1';
                                        $uitzetten = 'nee';
                                    } elseif ($borettiProduct[$x]['stock'] == 'out_of_stock') {
                                        file_put_contents(
                                            "BorettiImportLog.txt",
                                            $borettiProduct[$x]['item_code'] . ' met ean code ' .
                                            $borettiProduct[$x]['ean_code'] . ' is OUT OF STOCK',
                                            FILE_APPEND
                                        );
                                        $borettivoorraad = '0';
                                        $uitzetten = 'nee';
                                        $leverdatumreset = 'ja';
                                    } else {
                                        file_put_contents(
                                            "BorettiImportLog.txt",
                                            $borettiProduct[$x]['item_code'] . ' met ean code ' .
                                            $borettiProduct[$x]['ean_code'] . ' is HELAAS NIET voorraad bij Boretti',
                                            FILE_APPEND
                                        );
                                        $borettivoorraad = '0';
                                        $uitzetten = 'nee';
                                    }


                                    if (array_key_exists(
                                        'migration_attribute_12_refund_image_235',
                                        $product->getcustomFields()
                                    )) {
                                        $refundImage = $product->getcustomFields()['migration_attribute_12_refund_image_235'];
                                        if ($refundImage == 'Boretti voorraad sale') {
                                            file_put_contents(
                                                "BorettiImportLog.txt",
                                                $refundImage . ' ||||| DEZE ILVE OVERSLAAN ||||' . "\r\n",
                                                FILE_APPEND
                                            );
                                        }
                                    }

                                    $customFields['migration_attribute_12_inv_fabrikant_201'] = $borettivoorraad;
                                    if ($leverdatumreset == 'ja') {
                                        $customFields['migration_attribute_12_inv_leverdatum_262'] = '';
                                    }
                                    if ($uitzetten == 'ja') {
                                        $customFields['migration_attribute_12_feed_name_224'] = 'Boretti product
                                uitzetten ivm out of stock melding';
                                    }

                                    $this->updateProduct($product, $customFields, $context);
                                    $this->updateSupplierTable($product, $apiData, $dataExist, $updatedDate, $context);
                                }
                            }
                        }
                    }
                }
            }
        }

        return new JsonResponse(
            [
                'type'=>'success',
                'message' => 'Success'
            ]
        );
    }

    public function getProduct($ean, Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('ean', $ean));
        return $this->productsRepository->search($criteria, $context)->first();
    }

    public function updateProduct($product, $customFields, $context)
    {
        $data = [
            'id'            => $product->getId(),
            'customFields' => $customFields,
        ];
        $this->productsRepository->upsert([$data], $context);
        return $product->getId();
    }

    public function updateSupplierTable($product, $apiData, $dataExist, $updatedDate, Context $context)
    {
        $data = [
            'id'            => $dataExist ? $dataExist->getId() : Uuid::randomHex(),
            'productId'     => $product->getId(),
            'eanNumber'     => $product->getEan(),
            'borettiApiRecord' => json_encode($apiData),
            'lastUsageAt' => $updatedDate
        ];
        $this->supplierStockRepository->upsert([$data], $context);
    }

    public function checkSupplierTable($product, $apiData, $updatedDate, Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('lastUsageAt', $updatedDate));
        $criteria->addFilter(new EqualsFilter('productId', $product->getId()));
        $criteria->addFilter(new EqualsFilter('borettiApiRecord', json_encode($apiData)));
        return $this->supplierStockRepository->search($criteria, $context)->first();
    }
}
