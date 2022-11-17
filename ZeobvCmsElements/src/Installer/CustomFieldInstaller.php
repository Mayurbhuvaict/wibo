<?php
declare(strict_types=1);

namespace Zeobv\CmsElements\Installer;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class CustomFieldInstaller implements InstallerInterface
{

    public const FIELDSET_ID_CUSTOM_TRANSACTION = 'accbcf9bedfb4827853b75c5fd278d3d';
    public const FIELDSET_ORDER_ID_CUSTOM_TRANSACTION = 'accbcf9bedfb4827853b75c5fd277d3d';

    /** @var EntityRepositoryInterface */
    private EntityRepositoryInterface $customFieldRepository;

    /** @var EntityRepositoryInterface */
    private EntityRepositoryInterface $customFieldSetRepository;

    /** @var array */
    private array $customFields;

    /** @var array */
    private array $customFieldSets;

    public function __construct(EntityRepositoryInterface $customFieldSetRepository, EntityRepositoryInterface $customFieldRepository)
    {
        $this->customFieldSetRepository = $customFieldSetRepository;
        $this->customFieldRepository = $customFieldRepository;

        $this->customFieldSets = [
            [
                'id' => 'accbcf9bedfb4827853b75c5fd278d3d',
                'name' => 'product_video_description',
                'config' => [
                    'label' => [
                        'en-GB' => 'Product video and description',
                        'de-DE' => 'Produktvideo und Beschreibung',
                    ],
                ],
                'relation' => [
                    'entityName' => 'product'
                ],
            ],
            [
                'id' => 'c71c9320dc2a949c6bcf2470744c1d67',
                'name' => 'product_property_group',
                'config' => [
                    'label' => [
                        'en-GB' => 'Product Properties',
                        'de-DE' => 'Produkteigenschaften',
                        'nl-NL' => 'producteigenschappen'
                    ],
                ],
                'relation' => [
                    'entityName' => 'product'
                ],
            ]
        ];
        $this->customFields = [
            //block two image, title and description
            [
                'id' => 'ff83a318808f412c9f657e9182f6ed78',
                'name' => 'product_video_description_block_two_url',
                'type' => CustomFieldTypes::TEXT,
                'customFieldSetId' => 'accbcf9bedfb4827853b75c5fd278d3d',
                'config' => [
                    'label' => [
                        'en-GB' => 'Product video URL one',
                        'de-DE' => 'Produktvideo-URL eins'
                    ]
                ]
            ],
            [
                'id' => 'ef880f9328d4497ba905fbba903a538f',
                'name' => 'product_video_description_block_two_title',
                'type' => CustomFieldTypes::TEXT,
                'customFieldSetId' => 'accbcf9bedfb4827853b75c5fd278d3d',
                'config' => [
                    'label' => [
                        'en-GB' => 'Product video title one',
                        'de-DE' => 'Produktvideo Titel eins'
                    ]
                ]
            ],
            [
                'id' => '6a2c6945d1714056993a42432e118b20',
                'name' => 'product_video_description_block_two_desc',
                'type' => CustomFieldTypes::TEXT,
                'customFieldSetId' => 'accbcf9bedfb4827853b75c5fd278d3d',
                'config' => [
                    'label' => [
                        'en-GB' => 'Product video description one',
                        'de-DE' => 'Produktvideobeschreibung eins'
                    ]
                ]
            ],
            //block three image, title and description
            [
                'id' => '143ef23ebd304e4a8e002f4532d9ca44',
                'name' => 'product_video_description_block_three_url',
                'type' => CustomFieldTypes::TEXT,
                'customFieldSetId' => 'accbcf9bedfb4827853b75c5fd278d3d',
                'config' => [
                    'label' => [
                        'en-GB' => 'Product video URL two',
                        'de-DE' => 'Produktvideo-URL zwei'
                    ]
                ]
            ],
            [
                'id' => '186ec51a3bd442b69aefc3e8e40f59e4',
                'name' => 'product_video_description_block_three_title',
                'type' => CustomFieldTypes::TEXT,
                'customFieldSetId' => 'accbcf9bedfb4827853b75c5fd278d3d',
                'config' => [
                    'label' => [
                        'en-GB' => 'Product video title two',
                        'de-DE' => 'Titel zwei des Produktvideos'
                    ]
                ]
            ],
            [
                'id' => '83bdab18a6834aafbddea816dc441ed2',
                'name' => 'product_video_description_block_three_desc',
                'type' => CustomFieldTypes::TEXT,
                'customFieldSetId' => 'accbcf9bedfb4827853b75c5fd278d3d',
                'config' => [
                    'label' => [
                        'en-GB' => 'Product video description two',
                        'de-DE' => 'Produktvideobeschreibung zwei'
                    ]
                ]
            ],
            [
                'id' => '7c421fcb34edb9e5fabccfe21a035683',
                'name' => 'product_property_group',
                'type' => CustomFieldTypes::SELECT,
                'customFieldSetId' => 'c71c9320dc2a949c6bcf2470744c1d67',
                'config' => [
                    'componentName' => 'sw-entity-multi-id-select',
                    'customFieldType' => 'select',
                    'entity' => 'property_group',
                    'customFieldPosition' => 1,
                    'label' => [
                        'en-GB' => 'Displayed properties',
                        'de-DE' => 'Angezeigte Eigenschaften',
                        'nl-NL' => 'Weergegeven eigenschappen'
                    ],
                ]
            ]
        ];

    }

    /*install*/
    public function install(InstallContext $context): void
    {
        foreach ($this->customFieldSets as $customFieldSet) {
            $this->upsertCustomFieldSet($customFieldSet, $context->getContext());
        }
        foreach ($this->customFields as $customField) {
            $this->upsertCustomField($customField, $context->getContext());
        }
    }

    private function upsertCustomField(array $customField, Context $context): void
    {

        $data = [
            'id' => $customField['id'],
            'name' => $customField['name'],
            'type' => $customField['type'],
            'config' => $customField['config'],
            'active' => true,
            'customFieldSetId' => $customField['customFieldSetId'],

        ];
        $this->customFieldRepository->upsert([$data], $context);
    }

    private function upsertCustomFieldSet(array $customFieldSet, Context $context): void
    {
        $data = [
            'id' => $customFieldSet['id'],
            'name' => $customFieldSet['name'],
            'config' => $customFieldSet['config'],
            'active' => true,
            'relations' => [
                [
                    'entityName' => $customFieldSet['relation']['entityName'],
                ],
            ],
        ];
        $this->customFieldSetRepository->upsert([$data], $context);
    }

    /*install*/

    /*uninstall*/
    public function uninstall(UninstallContext $context): void
    {
        foreach ($this->customFieldSets as $customFieldSet) {
            $this->deactivateCustomFieldSet($customFieldSet, $context->getContext());
        }
        foreach ($this->customFields as $customField) {
            $this->deactivateCustomField($customField, $context->getContext());
        }
    }

    private function deactivateCustomField(array $customField, Context $context): void
    {
            $data = [
                'id' => $customField['id'],
                'name' => $customField['name'],
                'type' => $customField['type'],
                'config' => $customField['config'],
                'active' => false,
                'customFieldSetId' => $customField['customFieldSetId'],
            ];

            $this->customFieldRepository->delete([$data], $context);
    }

    private function deactivateCustomFieldSet(array $customFieldSet, Context $context): void
    {
            $data = [
                'id' => $customFieldSet['id'],
                'name' => $customFieldSet['name'],
                'config' => $customFieldSet['config'],
                'active' => false,
            ];

            $this->customFieldSetRepository->delete([$data], $context);

    }
    /*uninstall*/

}
