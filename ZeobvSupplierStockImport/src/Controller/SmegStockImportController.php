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
        $filename = '/home/deschouw/domains/deschouwwitgoed.nl/public_html/private/voorraad/fabrikant/inventum/' . array_shift($files);
        echo 'Gebruikte filename is ' . $filename . '|';

        $lijst = read_lookup_table_from_csv($filename, '|', '"', array(12 => ''), ON_COLLISION_SKIP, 4096, 1);
        $typenummer = read_lookup_table_from_csv($filename, '|', '"', array(1 => ''), ON_COLLISION_SKIP, 4096, 1);


        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(array('id', 'ean', 'visibility', 'manufacturer', 'attribute_set_id', 'inv_industrieweg', 'opvolger', 'inv_vlot', 'inv_winkel', 'inv_fabrikant', 'inkooppartij', $att_id_inkoop, $att_id_invfab))
            ->addAttributeToFilter('visibility', array('3', '4'))
            ->addAttributeToFilter('manufacturer', '3293')
            //->addAttributeToFilter('sku', 'IKI3020')
            ->load();

        echo "\r\n\r\n" . 'INVENTUM voorraad check ingelezen met ' . count($collection) . ' producten' . "\r\n\r\n";
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
}

