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
class SmegStockImportController extends AbstractController
{
    public const ON_COLLISION_SKIP = 2;

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
     * @Route("/api/zeostock/smegsupplier", name="api.action.zeo.smeg.import", methods={"GET"})
     */
    public function smegStockImport(?string  $cron, Context $context): JsonResponse
    {
        //find files in dir
        if ($cron == 1) {
            $files = scandir(getcwd().'/public/stock/smeg/', SCANDIR_SORT_DESCENDING);
            $filename = getcwd().'/public/stock/smeg/' . array_shift($files);
        } else {
            $files = scandir(getcwd().'/stock/smeg/', SCANDIR_SORT_DESCENDING);
            $filename = getcwd().'/stock/smeg/' . array_shift($files);
        }
        for ($x = 0; $x <= 2000; $x++) {
            if (isset($files[$x]) && !empty($files[$x]) && (!strpos($files[$x], '.txt'))) {
                unset($files[$x]);
            }
        }

        //get file name
        file_put_contents(
            "SmegImportLog.txt",
            'Gebruikte filename is ' . $filename . '|',
            FILE_APPEND
        );

        //read file
        $eanList = $this->readLookupTableFromCsv(
            $filename,
            "\t",
            '"',
            array(3 => ''),
            self::ON_COLLISION_SKIP,
            4096,
            1
        );
        $skuList = $this->readLookupTableFromCsv(
            $filename,
            "\t",
            '"',
            array(2 => ''),
            self::ON_COLLISION_SKIP,
            4096,
            1
        );

        //get smeg manufacturer product
        $collection = $this->getAllProduct($context);
        file_put_contents(
            "SmegImportLog.txt",
            "\r\n\r\n" . 'SMEG voorraad check ingelezen met ' . count($collection) . ' producten' . "\r\n\r\n",
            FILE_APPEND
        );

        foreach ($collection as $product) {
            //all smeg product update
            if (array_key_exists('migration_attribute_12_inv_fabrikant_201', $product->getCustomFields())) {
                $att_id_invfab = $product->getCustomFields()['migration_attribute_12_inv_fabrikant_201'];
                if ($att_id_invfab > '0' && $product->getManufacturer()->getName() == 'Smeg') {
                    $att_id_invfab = '0';
                    $this->updateProduct($product, $att_id_invfab, $context);
                }
            }

            //check api
            $ean = $product->getEan();
            $sku = $product->getProductNumber();
            $apiData = null;
            if (array_key_exists($ean, $eanList)) {
                $apiData = $eanList[$ean];
            } elseif (array_key_exists($sku, $skuList)) {
                $apiData = $skuList[$sku];
            }
            if ($apiData) {
                $dataExist = $this->checkSupplierTable($product, $apiData, $context);
                if (empty($dataExist)) {
                    if (array_key_exists($ean, $eanList)) {
                        $stock = '0';
                        if (($eanList[$ean][4]) == 'Ja') {
                            $stock = '1';
                        }
                        file_put_contents(
                            "SmegImportLog.txt",
                            "Het product " . $eanList[$ean][2] . " wordt ingekocht bij Smeg,
                    EAN-code van het product is gevonden in het csv-bestand en
                    voorraad fabrikant is " . $stock . "\r\n",
                            FILE_APPEND
                        );
                        $att_id_invfab = intval($stock);
                        $this->updateProduct($product, $att_id_invfab, $context);
                    } elseif (array_key_exists($sku, $skuList)) {
                        $stock = '0';
                        if (($skuList[$sku][4]) == 'Ja') {
                            $stock = '1';
                        }
                        file_put_contents(
                            "SmegImportLog.txt",
                            "Het product " . $skuList[$sku][2] . " wordt ingekocht bij Smeg, SKU-code van het product
                    is gevonden in het csv-bestand en voorraad fabrikant is " . $stock . "\r\n",
                            FILE_APPEND
                        );
                        $att_id_invfab = intval($stock);
                        $this->updateProduct($product, $att_id_invfab, $context);
                    } elseif (strpos($sku, '_')) {
                        $sku = str_replace('_', '-', $product->getProductNumber());
                        if (array_key_exists($sku, $skuList)) {
                            $stock = '0';
                            if (($skuList[$sku][4]) == 'Ja') {
                                $stock = '1';
                            }
                            file_put_contents(
                                "SmegImportLog.txt",
                                "Het product " . $skuList[$sku][2] . " wordt ingekocht bij Smeg, SKU-code van het
                    product is gevonden in het csv-bestand en voorraad fabrikant is " . $stock . "\r\n",
                                FILE_APPEND
                            );
                            $att_id_invfab = intval($stock);
                            $this->updateProduct($product, $att_id_invfab, $context);
                        }
                    }
                    $this->updateSupplierTable($product, $apiData, $dataExist, $context);
                }
            } else {
                file_put_contents(
                    "SmegImportLog.txt",
                    '** ' . $product->getProductNumber(). ' ontbreekt in bestand (' . $product->getEan() . ') **',
                    FILE_APPEND
                );
            }
        }

        return new JsonResponse(
            [
                'type' => 'success',
                'message' => 'Success'
            ]
        );
    }

    public function getAllProduct(Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addAssociation('manufacturer');
        $criteria->addFilter(new EqualsFilter('manufacturer.name', 'smeg'));
        $criteria->addFilter(new EqualsFilter('category', 'smeg'));
        return $this->productsRepository->search($criteria, $context)->getEntities()->getElements();
    }

    public function updateProduct($product, $att_id_invfab, $context)
    {
        $data = [
            'id'            => $product->getId(),
            'customFields' => [
                'migration_attribute_12_inv_fabrikant_201' => (string)$att_id_invfab,
            ],
        ];
        $this->productsRepository->upsert([$data], $context);
    }

    public function updateSupplierTable($product, $apiData, $dataExist, Context $context)
    {
        $data = [
            'id'            => $dataExist ? $dataExist->getId() : Uuid::randomHex(),
            'productId'     => $product->getId(),
            'eanNumber'     => $product->getEan(),
            'smegApiRecord' => json_encode($apiData),
        ];
        $this->supplierStockRepository->upsert([$data], $context);
    }

    public function checkSupplierTable($product, $apiData, Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $product->getId()));
        $criteria->addFilter(new EqualsFilter('smegApiRecord', json_encode($apiData)));
        return $this->supplierStockRepository->search($criteria, $context)->first();
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
        int    $on_collision = ON_COLLISION_SKIP,
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
                    case ON_COLLISION_OVERWRITE:
                        $retval[$index_by] = array_combine($names, $data);
                        // no break
                    case ON_COLLISION_SKIP:
                        break;
                    case ON_COLLISION_ABORT:
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
