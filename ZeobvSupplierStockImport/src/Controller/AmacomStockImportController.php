<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\Controller;

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
class AmacomStockImportController extends AbstractController
{
    public const ON_COLLISION_OVERWRITE = 1;
    public const ON_COLLISION_SKIP = 2;
    public const ON_COLLISION_ABORT = 3;

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
     * @Route("/api/zeostock/amacomsupplier", name="api.action.zeo.amacom.import", methods={"GET"})
     */
    public function amacomStockImport(?string $cron, Context $context): JsonResponse
    {
        $ch = curl_init();
        $source = "https://client.quecom.nl//auth/product-download/user_id/5684/hash/f525ec9e9cdfae5d1b49b9a59ceb2ef81383d883";
        curl_setopt($ch, CURLOPT_URL, $source);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);

        if ($cron == 1) {
            $destination = getcwd() . "/public/stock/amacom/amacom-" . date("d-m-Y") . ".csv";
        } else {
            $destination = getcwd() . "/stock/amacom/amacom-" . date("d-m-Y") . ".csv";
        }
        $file = fopen($destination, "w+");
        fputs($file, $data);
        fclose($file);

        $eanList = $this->readLookupTableFromCsv(
            $destination,
            ';',
            '"',
            array(5 => ''),
            self::ON_COLLISION_SKIP,
            4096,
            1
        );

        $allProducts = $this->getAllProduct($context);
        if ($allProducts) {
            foreach ($allProducts as $product) {
                $ean = $product->getEan();
                $apiData = null;
                if (array_key_exists($ean, $eanList)) {
                    $apiData = $eanList[$ean];
                }
                if ($apiData) {
                    $dataExist = $this->checkSupplierTable($product, $apiData, $context);
                    if (empty($dataExist)) {
                        if (array_key_exists($ean, $eanList)) {
                            file_put_contents(
                                "AmacomImportLog.txt",
                                "De " . $eanList[$ean][2] . " wordt ingekocht bij Amacon, EAN-code " .
                                $eanList[$ean][5] . " van het product is gevonden in het csv-bestand
                            en voorraad fabrikant is " .
                                $eanList[$ean][10] . ' en cost is ' . $eanList[$ean][3] . "\r\n",
                                FILE_APPEND
                            );
                            $att_id_invfab = intval($eanList[$ean][10]);
                            $att_id_costama = $eanList[$ean][3];
                            $this->updateProduct($product, $att_id_invfab, $att_id_costama, $context);
                        } else {
                            if (array_key_exists('migration_attribute_20_inv_vlot_204', $product->getcustomFields())
                                && array_key_exists('migration_attribute_20_inv_industrieweg_202', $product->getcustomFields())
                                && array_key_exists('migration_attribute_20_inv_winkel_205', $product->getcustomFields())) {
                                $getInvVlot = $product->getcustomFields()['migration_attribute_20_inv_vlot_204'];
                                $getInvIndustrieweg = $product->getcustomFields()['migration_attribute_20_inv_industrieweg_202'];
                                $getInvWinkel = $product->getcustomFields()['migration_attribute_20_inv_winkel_205'];
                                $voorraad = ($getInvVlot + $getInvIndustrieweg + $getInvWinkel);
                                if ($voorraad < 1) {
                                    file_put_contents(
                                        "AmacomImportLog.txt",
                                        $product->getProductNumber(). ' is niet meer leverbaar (' .
                                        $product->getEan() . ')' . "\r\n",
                                        FILE_APPEND
                                    );
                                }
                            }
                        }
                        $this->updateSupplierTable($product, $apiData, $dataExist, $context);
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

    public function getAllProduct(Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addAssociation('manufacturer');
        $criteria->addFilter(new EqualsFilter('customFields.migration_attribute_20_tradeplace_inlezen_239', '1'));
        $criteria->addFilter(new EqualsFilter('manufacturer.name', 'boneco'));
        return $this->productsRepository->search($criteria, $context)->getEntities()->getElements();
    }

    public function checkSupplierTable($product, $apiData, Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $product->getId()));
        $criteria->addFilter(new EqualsFilter('amacomApiRecord', json_encode($apiData)));
        return $this->supplierStockRepository->search($criteria, $context)->first();
    }
    public function updateSupplierTable($product, $apiData, $dataExist, Context $context)
    {
        $data = [
            'id'            => $dataExist ? $dataExist->getId() : Uuid::randomHex(),
            'productId'     => $product->getId(),
            'eanNumber'     => $product->getEan(),
            'amacomApiRecord' => json_encode($apiData),
        ];
        $this->supplierStockRepository->upsert([$data], $context);
    }
    public function updateProduct($product, $att_id_invfab, $att_id_costama, $context)
    {
        $data = [
            'id'            => $product->getId(),
            'customFields' => [
                'migration_attribute_20_inv_fabrikant_201' => $att_id_invfab,
                'migration_attribute_20_cost_amacom_328' => $att_id_costama,

            ],
        ];
        $this->productsRepository->upsert([$data], $context);
    }

    /**
     *  Reads a CSV file and stores it as a lookup table, implemented as a PHP hash table.
     *
     * @param string $csv_file the CSV file to read.
     * @param string $separator_input
     * @param string $separator_index
     * @param array $index_by the array containing the columns to index the lookup table by,
     * and the function to pre-process those columns with.
     * @param integer $on_collision a constant that determines what to do when an index is already in use.
     * @param integer $rec_len the maximum length of a record in the input file.
     * @param int $hoewilikindex
     * @return array|int                   an error number or the resulting hash table.
     */
    public function readLookupTableFromCsv(
        string $csv_file,
        string $separator_input = ',',
        string $separator_index = '"',
        array  $index_by = array(0 => ''),
        int    $on_collision = self::ON_COLLISION_SKIP,
        int    $rec_len = 4096,
        int $hoewilikindex = 0
    ) {
        $handle = fopen($csv_file, 'r');
        if ($handle == null || ($data = fgetcsv($handle, $rec_len, $separator_input)) === false) {
            // Couldn't open/read from CSV file.
            return -1;
        }

        $names = array();
        foreach ($data as $field) {
            $names[] = trim($field);
        }
        $indexes = array();
        foreach ($index_by as $index_in => $function) {
            if (is_int($index_in)) {
                if ($index_in < 0 || $index_in > count($data)) {
                    // Index out of bounds.
                    fclose($handle);
                    return -2;
                }

                $index_out = $index_in;
            } else {
                // If a column that is used as part of the key to the hash table is supplied
                // as a name rather than as an integer, then determine that named column's
                // integer index in the $names array, because the integer index is used, below.
                $get_index = array_keys($names, $index_in);
                $index_out = $get_index[0];

                if (is_null($index_out)) {
                    // A column name was given (as opposed to an integer index), but the
                    // name was not found in the first row that was read from the CSV file.
                    fclose($handle);
                    return -3;
                }
            }

            $indexes[$index_out] = $function;
        }

        if (count($indexes) == 0) {
            // No columns were supplied to index by.
            fclose($handle);
            return -4;
        }

        $retval = array();
        $iii = 0;
        while (($data = fgetcsv($handle, $rec_len, $separator_input)) !== false) {
            $index_by = '';
            foreach ($indexes as $index => $function) {
                $index_by .= ($function ? $function($data[$index]) : $data[$index]) . $separator_index;
            }
            $index_by = substr($index_by, 0, -1);

            if (isset($retval[$index_by])) {
                switch ($on_collision) {
                    case self::ON_COLLISION_OVERWRITE:
                        $retval[$index_by] = array_combine($names, $data);
                        // no break
                    case self::ON_COLLISION_SKIP:
                        break;
                    case self::ON_COLLISION_ABORT:
                        return -5;
                }
            } else {
                if ($hoewilikindex == 0) {
                    $retval[$iii] = array_combine($names, $data);
                } else {
                    $retval[$index_by] = $data;
                }
                $iii++;
            }
        }
        fclose($handle);
        return $retval;
    }
}
