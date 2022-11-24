<?php declare(strict_types=1);

namespace Zeobv\CmsElements;


use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Context;
use Doctrine\DBAL\Connection;
use Zeobv\CmsElements\Installer\CustomFieldInstaller;

class ZeobvCmsElements extends Plugin
{
    function install(InstallContext $installContext): void
    {
        parent::install($installContext);
        $this->createEmailHeaderFooterTemplate($installContext);
        $this->getCustomFieldInstaller()->install($installContext);
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);

        /**
         * @var Connection $connection ()
         */
        $connection = $this->container->get(Connection::class);

        /**
         * @var EntityRepositoryInterface $productVideoRepository ()
         */

        $productVideoRepository = $this->container->get('product.repository');

    }

    private function getCustomFieldInstaller(): CustomFieldInstaller
    {
        /**
         * @var EntityRepositoryInterface $customFieldSetRepository
         */
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        /**
         * @var EntityRepositoryInterface $customFieldRepository
         */

        $customFieldRepository = $this->container->get('custom_field.repository');

        return new CustomFieldInstaller($customFieldSetRepository, $customFieldRepository);
    }

    public function createEmailHeaderFooterTemplate(InstallContext $installContext){
        /**
         * @var EntityRepositoryInterface $mailTemplateRepository
         */

        /**
         * @var EntityRepositoryInterface $salesChannelRepository
         */
        $salesChannelRepository = $this->container->get('sales_channel.repository');

        $mailTemplateRepository = $this->container->get( 'mail_header_footer.repository' );
        $criteria = New Criteria();
        $criteria->addAssociation('translations');
        $criteria->addFilter(New EqualsFilter('name','Schouw header & footer'));

        $uuid = $mailTemplateRepository->search($criteria,$installContext->getContext())->first();
        $id = Uuid ::randomHex ();
        if(!is_null($uuid)){
            $id = $uuid->getId();
        }
        $data[] = [
            'id' => $id,
            'name' => "Schouw header & footer",
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
                                                            <img src="https://acceptatie.deschouwwitgoed.nl/media/ab/53/4a/1634826715/deschouw-logo.svg" alt="">
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
                                                <i><b>iban</b></i> NL40 ABNA 061 375 3372 &nbsp; &nbsp;
                                                <i><b>bic</b></i> ABNANL2A &nbsp; &nbsp;
                                                <i><b>kvk</b></i> 32138748 &nbsp; &nbsp;
                                                <i><b>btw</b></i> NL 8197 80 832 B01
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

            // update in sales channel
            $searchSalesChannel = New Criteria();
            $defaultMailTemplate = $salesChannelRepository->search($searchSalesChannel,$installContext->getContext());
            if(count($defaultMailTemplate)) {
                foreach ($defaultMailTemplate as $updateToSalesChannel) {
                    $updateSalesChannel[] = ['id' => $updateToSalesChannel->getId(), 'mailHeaderFooterId' => $id];
                    $salesChannelRepository->update($updateSalesChannel, $installContext->getContext());
                }
            }
        } catch ( UniqueConstraintViolationException $exception ) {

        }
    }

    //To uninstall template from database
    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }
        $this->getCustomFieldInstaller()->uninstall($uninstallContext);

        /**
         * @var EntityRepositoryInterface $mailTemplateRepository
         */
        $mailTemplateRepository = $this->container->get('mail_header_footer.repository');

        /**
         * @var EntityRepositoryInterface $salesChannelRepository
         */
        $salesChannelRepository = $this->container->get('sales_channel.repository');

        $criteria = New Criteria();
        $criteria->addAssociation('translations');
        $mailTemplate = $mailTemplateRepository->search($criteria,$uninstallContext->getContext());

        $myTemplateId = null;
        $defaultTemplateId = null;

        foreach ($mailTemplate as $template){
            if($template->name == "Schouw header & footer"){
                $myTemplateId = $template->id;
            }
            if($template->name == "Default email footer"){
                $defaultTemplateId = $template->id;
            }
        }

        $searchDefaultMailTemplate = New Criteria();
        $searchDefaultMailTemplate->addFilter(New EqualsFilter('mailHeaderFooterId',$myTemplateId));
        $defaultMailTemplate = $salesChannelRepository->search($searchDefaultMailTemplate,$uninstallContext->getContext());
        if(count($defaultMailTemplate)) {
            foreach ($defaultMailTemplate as $updateToSalesChannel) {
                $data[] = ['id' => $updateToSalesChannel->getId(), 'mailHeaderFooterId' => $defaultTemplateId];
                $salesChannelRepository->update($data, $uninstallContext->getContext());
            }
        }




        if($myTemplateId != null) {
            $mailTemplateRepository->delete([
                ['id' => $myTemplateId]
            ], $uninstallContext->getContext());
        }
    }
}
