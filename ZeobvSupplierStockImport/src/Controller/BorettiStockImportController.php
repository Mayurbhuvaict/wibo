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
    private $supplierstockimportRepository;

    public function __construct(
        EntityRepositoryInterface $productsRepository,
        EntityRepositoryInterface $supplierstockimportRepository
    ) {
        $this->productsRepository = $productsRepository;
        $this->supplierstockimportRepository = $supplierstockimportRepository;
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
        unset($borettiData['error']);
        foreach ($borettiData as $borettiProduct) {
            if ($borettiProduct) {
                $aantal = count($borettiProduct);
                for ($x = 0; $x < $aantal; $x++) {
                    $leverdatumreset = 'nee';
                    $ean = $borettiProduct[$x]['ean_code'];
                    $date = $borettiProduct[$x]['updated_at'];
                    $updatedDate = date('Y-m-d', strtotime($date));

                    if (!empty($ean)) {
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
                        $product = $this->getProduct($ean, $context);
                        if ($product) {
                            if (array_key_exists(
                                'migration_attribute_16_refund_image_235',
                                $product->getcustomFields()
                            )) {
                                $refundImage = $product->getcustomFields()['migration_attribute_16_refund_image_235'];
                                if ($refundImage == 'Boretti voorraad sale') {
                                    file_put_contents(
                                        "BorettiImportLog.txt",
                                        $refundImage . ' ||||| DEZE ILVE OVERSLAAN ||||' . "\r\n",
                                        FILE_APPEND
                                    );
                                }
                            }

                            $customFields['migration_attribute_16_inv_fabrikant_201'] = $borettivoorraad;
                            if ($leverdatumreset == 'ja') {
                                $customFields['migration_attribute_16_inv_leverdatum_262'] = '';
                            }
                            if ($uitzetten == 'ja') {
                                $customFields['migration_attribute_16_feed_name_224'] = 'Boretti product uitzetten ivm out of stock melding';
                            }
                            $productId = $product->getId();
                            $checkUpdatedProduct = $this->checkUpdatedProduct($productId,$updatedDate, $context);
                            if (empty($checkUpdatedProduct)) {
                                $updatedData = $this->updateProduct($product, $customFields, $context);
                                $this->insertStock($updatedData, $updatedDate, $borettiData, $ean, $context);

                            }
                            else{
                                return new JsonResponse(
                                    [
                                        'type'=>'success',
                                        'message' => 'No product updated'
                                    ]
                                );
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
        }
        return new JsonResponse(
            [
                'type'=>'Error',
                'message' => 'Error'
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

    public function insertStock($updatedDataId,$updatedDate,$borettiData,$ean, $context)
    {
        $productEAN = $this->getProduct($ean,$context);
        foreach ($borettiData as $borettiProduct) {
            if ($borettiProduct) {
                $aantal = count($borettiProduct);
                for ($x = 0; $x < $aantal; $x++) {
                    $apiEAN = $borettiProduct[$x]['ean_code'];
                    if($apiEAN == $productEAN->getEAN()) {
                        $data = [
                            'id'            => Uuid::randomHex(),
                            'productId'            => $updatedDataId,
                            'apiRecord' => $borettiProduct[$x],
                            'lastUsageAt' => $updatedDate
                        ];
                        $this->supplierstockimportRepository->upsert([$data], $context);
                    }
                }
            }
        }
    }

    public function checkUpdatedProduct($productId, $updatedDate = null , $context) : int
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('lastUsageAt',$updatedDate));
        $criteria->addFilter(new EqualsFilter('productId',$productId));

        return $this->supplierstockimportRepository->search($criteria, $context)->getTotal();
    }

}
