<?php declare(strict_types=1);

namespace Zeobv\CmsElements;

use Shopware\Core\Framework\Plugin;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Context;

class ZeobvCmsElements extends Plugin
{

    function install(InstallContext $installContext): void
    {
        parent::install($installContext);
        $this->create_email_header_footer_template($installContext);
    }

    public function create_email_header_footer_template(InstallContext $installContext){
        /**
         * @var EntityRepositoryInterface $mailTemplateRepository
         */
        $mailTemplateRepository = $this->container->get( 'mail_header_footer.repository' );
        $criteria = New Criteria();
        $criteria->addAssociation('mail_header_footer_translation');
        $criteria->addFilter(New EqualsFilter('name','Schouw header & footer'));

        $uuid = $mailTemplateRepository->search($criteria,$installContext->getContext())->first();
        $data[] = [
            'id' => $uuid->getId(),
            'headerHtml' => '<!DOCTYPE html>'.
                '<html>'.

                '<head>'.
                '<title></title>'.
                '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.
                '<meta name="viewport" content="width=device-width, initial-scale=1">'.
                '<meta http-equiv="X-UA-Compatible" content="IE=edge" />'.
                '<style type="text/css">
                        body,
                        table,
                        td,
                        a {
                            -webkit-text-size-adjust: 100%;
                            -ms-text-size-adjust: 100%;
                        }

                        table,
                        td {
                            mso-table-lspace: 0pt;
                            mso-table-rspace: 0pt;
                        }

                        img {
                            -ms-interpolation-mode: bicubic;
                        }

                        img {
                            border: 0;
                            height: auto;
                            line-height: 100%;
                            outline: none;
                            text-decoration: none;
                        }

                        table {
                            border-collapse: collapse !important;
                        }

                        body {
                            height: 100% !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            width: 100% !important;
                        }

                        a[x-apple-data-detectors] {
                            color: inherit !important;
                            text-decoration: none !important;
                            font-size: inherit !important;
                            font-family: inherit !important;
                            font-weight: inherit !important;
                            line-height: inherit !important;
                        }

                        @media screen and (max-width: 480px) {
                            .mobile-hide {
                                display: none !important;
                            }

                            .mobile-center {
                                text-align: center !important;
                            }
                        }

                        div[style*="margin: 16px 0;"] {
                            margin: 0 !important;
                        }
                        .navigation.desktop {
                            text-align: center;
                            padding: 20px;
                            background: #f3f3f8;
                        }
                        a,
                        .footer p {
                            font-family: "Open Sans", Helvetica, Arial, sans-serif;
                        }
                        a, a:visited {
                            color:#3b3b71;
                        }
                    </style>'.

                '<body style="margin: 0 !important; padding: 0 !important; background-color: #eeeeee;" bgcolor="#eeeeee">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td align="center" style="background-color: #eeeeee;" bgcolor="#eeeeee">
                                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:900px;">
                                    <tr>
                                        <td align="center" valign="top" style="font-size:0; padding: 35px;" bgcolor="#ffffff">
                                            <div style="display:inline-block; max-width:100%; min-width:100px; vertical-align:top; width:100%;">
                                                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:900px;">
                                                    <tr>
                                                        <td align="left" valign="top" style="font-family: Open Sans, Helvetica, Arial, sans-serif; font-size: 36px; font-weight: 800; line-height: 48px;" class="mobile-center">
                                                            <img src="https://sw.deschouwwitgoed.nl/media/ab/53/4a/1634826715/deschouw-logo.svg" alt="">
                                                        </td>
                                                        <td align="right" valign="center" style="font-family: sans-serif; font-size: 17px; color: #3b3b71;" class="mobile-center">
                                                            <span style="">Nieuwe Brink 20, 1404 KB Bussum &nbsp; &nbsp;
                                                                <i><b>tel</b></i>
                                                                +31 (0)35 691 8115
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center" style="padding: 35px 35px 20px 35px; background-color: #ffffff;" bgcolor="#ffffff">
                                            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:900px;">

                ',
            'footerHtml' => '</table>'.
                '</td>'.
                '</tr>'.
                '<tr class="footer">'.
                '.<td align="center" valign="center" style="font-family: sans-serif; font-size: 17px; color: #3b3b71; background: #f3f3f8; padding: 35px 0; line-height: 35px;" class="mobile-center">
                                            <div>
                                                <i><b>mail</b></i> info@deschouwwitgoed.nl &nbsp; &nbsp;
                                                <i><b>webshop</b></i> deschouwwitgoed.nl
                                            </div>
                                            <div>
                                                <i><b>iban</b></i> NL40 ABNA 0613753372 &nbsp; &nbsp;
                                                <i><b>bic</b></i> ABNANL2A &nbsp; &nbsp;
                                                <i><b>kvk</b></i> 32138748 &nbsp; &nbsp;
                                                <i><b>btw</b></i> NL 819780832B01
                                            </div>
                                        </td>'.
                '</tr>'.

                '</table>'.
                '</td>'.
                '</tr>'.
                '</table>'.
                '</body>'.

                '</html>'
        ];
        try {
            $mailTemplateRepository->upsert( $data, $installContext->getContext());
        } catch ( UniqueConstraintViolationException $exception ) {

        }
    }

}
