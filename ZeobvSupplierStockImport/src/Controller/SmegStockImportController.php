<?php //declare(strict_types=1);

namespace Zeobv\SupplierStockImport\Controller;

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
class SmegStockImportController extends AbstractController
{

    /**
     * @var EntityRepositoryInterface
     */
    private $productsRepository;

    public function __construct(
        EntityRepositoryInterface $productsRepository
    )
    {
        $this->productsRepository = $productsRepository;
    }

    /**
     * @Route("/api/zeostock/smegsupplier", name="api.action.zeo.smeg.import", methods={"GET"})
     */
    public function smegStockImport(Context $context): JsonResponse
    {
        $att_id_inkoop = 'inkooppartij'; //inkooppartij
        $att_id_invfab = 'inv_fabrikant'; //inv_fabrikant

        $files = scandir('/home/deschouw/domains/deschouwwitgoed.nl/public_html/private/voorraad/fabrikant/inventum/', SCANDIR_SORT_DESCENDING);
        for ($x = 0; $x <= 1300; $x++) {
            if (isset($files[$x]) && !empty($files[$x]) && (!strpos($files[$x], '.txt'))) {
                unset($files[$x]);
            }
        }
        $filename = getcwd().'/stock/smeg/' . array_shift($files);
        file_put_contents(
            "SmegImportLog.txt",
            'Gebruikte filename is ' . $filename . '|',
            FILE_APPEND
        );

        $lijst = $this->read_lookup_table_from_csv(
            $filename,
            '|',
            '"',
            array(12 => ''),
            ON_COLLISION_SKIP,
            4096,
            1
        );

        $typenummer = $this->read_lookup_table_from_csv(
            $filename,
            '|',
            '"',
            array(1 => ''),
            ON_COLLISION_SKIP,
            4096,
            1
        );

        $collection = $this->getAllProduct($context);
        file_put_contents(
            "SmegImportLog.txt",
            "\r\n\r\n" . 'INVENTUM voorraad check ingelezen met ' . count($collection) . ' producten' . "\r\n\r\n",
            FILE_APPEND
        );

// Mage::log('-----> INVENTUM voorraad check ingelezen met ' . count($collection) . ' producten <-------', null, 'tp.log');

        foreach ($collection as $product) {
            $ean = $product->getEan();
            $sku = $product->getSku();

            $fouteean = false;
            if (array_key_exists($sku, $typenummer)) {
                if ($lijst[$ean][12] != $product->getEan()) {
                    echo '--> Controleer EAN code voor product ' . $product->getSku() . "\r\n";
                    $fouteean = true;
                }
            }
            if ($product->getEan() == '') {
                continue;
            }
            if (array_key_exists($ean, $lijst)) {
                //  $stock = $lijst[$ean][13];
                if (($lijst[$ean][13]) == '>75') {
                    $stock = '100';
                } elseif (($lijst[$ean][13]) == '25-75') {
                    $stock = '50';
                } elseif (($lijst[$ean][13]) == '1-25') {
                    $stock = '25';
                } elseif (($lijst[$ean][13]) == '0') {
                    $stock = '0';
                }

                echo "Het product " . $lijst[$ean][1] . " wordt ingekocht bij Inventum, EAN-code van het product is gevonden in het csv-bestand en voorraad fabrikant is " . $stock . "\r\n";
                $product->setData($att_id_invfab, intval($stock));
                $product->getResource()->saveAttribute($product, $att_id_invfab);
            } else {
                // $voorraad = $product->getInvVlot() + $product->getInvIndustrieweg() + $product->getInvWinkel() + $product->getInvFabrikant();
                //  if ($voorraad < 1) {
                echo '************ ' . $product->getSku() . ' is niet meer leverbaar (' . $product->getEan() . ') *********************';
                // Mage::log($product->getSku(). ' is niet meer leverbaar (' . $product->getEan() . ')', null, 'tp.log');
                //  }

                echo "\r\n";

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
        return $this->productsRepository->search($criteria, $context)->getEntities()->getElements();
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

