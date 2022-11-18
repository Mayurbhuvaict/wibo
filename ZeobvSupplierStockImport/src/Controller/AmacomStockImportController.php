<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\Controller;

define('ON_COLLISION_OVERWRITE', 1);
define('ON_COLLISION_SKIP', 2);
define('ON_COLLISION_ABORT', 3);

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
class AmacomStockImportController extends AbstractController
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
     * @Route("/api/zeostock/amacomsupplier", name="api.action.zeo.amacom.import", methods={"GET"})
     */
    public function amacomStockImport(Context $context): JsonResponse
    {
        $ch = curl_init();
        $source = "https://client.quecom.nl//auth/product-download/user_id/5684/hash/f525ec9e9cdfae5d1b49b9a59ceb2ef81383d883";
        curl_setopt($ch, CURLOPT_URL, $source);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);

        $destination = getcwd()."/stock/amacom/amacom-" . date("d-m-Y") . ".csv";
        $file = fopen($destination, "w+");
        fputs($file, $data);
        fclose($file);

        $lijst = $this->read_lookup_table_from_csv(
            $destination,
            ';',
            '"',
            array(5 => ''),
            ON_COLLISION_SKIP,
            4096,
            1
        );
        $allProducts = $this->getAllProduct($context);
        foreach ($allProducts as $product) {
            $ean = $product->getEan();
            if (array_key_exists($ean, $lijst)) {
                file_put_contents(
                    "AmacomImportLog.txt",
                    "De " . $lijst[$ean][2] . " wordt ingekocht bij Amacon, EAN-code " . $lijst[$ean][5] . " van het product is gevonden in het csv-bestand en voorraad fabrikant is " . $lijst[$ean][10] . ' en cost is ' . $lijst[$ean][3] . "\r\n",
                    FILE_APPEND
                );
                $att_id_invfab = intval($lijst[$ean][10]);
                $att_id_costama = $lijst[$ean][3];
                $this->updateProduct($product, $att_id_invfab, $att_id_costama, $context);
            } else {
                $getInvVlot = $product->getcustomFields()['migration_attribute_16_inv_vlot_204'];
                $getInvIndustrieweg = $product->getcustomFields()['migration_attribute_16_inv_industrieweg_202'];
                $getInvWinkel = $product->getcustomFields()['migration_attribute_16_inv_winkel_205'];
                $voorraad = ($getInvVlot + $getInvIndustrieweg + $getInvWinkel);
                if ($voorraad < 1) {
                    file_put_contents(
                        "AmacomImportLog.txt",
                        $product->getSku(). ' is niet meer leverbaar (' . $product->getEan() . ')' . "\r\n",
                        FILE_APPEND
                    );
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
        $criteria->addFilter(new EqualsFilter('customFields.migration_attribute_16_tradeplace_inlezen_239', '1'));
        $criteria->addFilter(new EqualsFilter('manufacturer.name', 'amana'));
        return $this->productsRepository->search($criteria, $context)->getEntities()->getElements();
    }

    public function updateProduct($product, $att_id_invfab, $att_id_costama, $context)
    {
        $data = [
            'id'            => $product->getId(),
            'customFields' => [
                'migration_attribute_16_inv_fabrikant_201' => $att_id_invfab,
                'migration_attribute_16_cost_amacom_328' => $att_id_costama,

            ],
        ];
        $this->productsRepository->upsert([$data], $context);
    }

    public function read_lookup_table_from_csv(
        $csv_file,
        $separator_input = ';',
        $separator_index = '|',
        $index_by = array(0 => ''),
        $on_collision = ON_COLLISION_ABORT,
        $rec_len = 1024
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
                $retval[$index_by] = array_combine($names, $data);
            }
        }
        fclose($handle);

        return $retval;
    }
}
