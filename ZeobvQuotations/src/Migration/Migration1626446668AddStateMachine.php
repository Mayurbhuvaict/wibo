<?php declare(strict_types=1);

namespace Zeobv\Quotations\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;
use Zeobv\Quotations\Core\StateMachine\QuotationStateMachineTransitionActions;
use Zeobv\Quotations\Core\StateMachine\QuotationStates;
use Zeobv\Quotations\Migration\Step\PluginMigrationStep;

class Migration1626446668AddStateMachine extends PluginMigrationStep
{
    private string $stateMachineId;
    private string $openStateId;
    private string $definitiveStateId;
    private string $acceptedStateId;
    private string $declinedStateId;
    private string $expiredStateId;

    public function getCreationTimestamp(): int
    {
        return 1626446668;
    }

    public function update(Connection $connection): void
    {
        $connection->beginTransaction();

        try {
            // Insert state machine
            $this->insert($connection, 'state_machine', [
                'id' => $this->stateMachineId = Uuid::randomBytes(),
                'technical_name' => QuotationStates::STATE_MACHINE,
            ]);

            // Insert state machine states
            $this->insertStateMachineStates($connection);

            // Set initial state for quotation state machine
            $connection->update('state_machine', [
                'initial_state_id' => $this->openStateId
            ], ['id' => $this->stateMachineId]);

            // Insert all possible transitions
            $this->insertStateTransitions($connection);

            // Insert translations
            $this->insertStateMachineTranslations($connection);
            $this->insertStateMachineStateTranslations($connection);

            $connection->commit();
        } catch (\Throwable $exception) {
            $connection->rollBack();
            throw $exception;
        }
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    protected function insertStateMachineStates(Connection $connection)
    {
        $this->openStateId = Uuid::randomBytes();
        $this->insert($connection, 'state_machine_state', [
            'id' => $this->openStateId,
            'technical_name' => QuotationStates::STATE_OPEN,
            'state_machine_id' => $this->stateMachineId
        ]);

        $this->definitiveStateId = Uuid::randomBytes();
        $this->insert($connection, 'state_machine_state', [
            'id' => $this->definitiveStateId,
            'technical_name' => QuotationStates::STATE_DEFINITIVE,
            'state_machine_id' => $this->stateMachineId
        ]);

        $this->acceptedStateId = Uuid::randomBytes();
        $this->insert($connection, 'state_machine_state', [
            'id' => $this->acceptedStateId,
            'technical_name' => QuotationStates::STATE_ACCEPTED,
            'state_machine_id' => $this->stateMachineId
        ]);

        $this->declinedStateId = Uuid::randomBytes();
        $this->insert($connection, 'state_machine_state', [
            'id' => $this->declinedStateId,
            'technical_name' => QuotationStates::STATE_DECLINED,
            'state_machine_id' => $this->stateMachineId
        ]);

        $this->expiredStateId = Uuid::randomBytes();
        $this->insert($connection, 'state_machine_state', [
            'id' => $this->expiredStateId,
            'technical_name' => QuotationStates::STATE_EXPIRED,
            'state_machine_id' => $this->stateMachineId
        ]);
    }

    protected function insertStateTransitions(Connection $connection)
    {
        $stateMachineId = $this->stateMachineId;

        $this->insert($connection, 'state_machine_transition', [
            'state_machine_id' => $stateMachineId,
            'action_name' => QuotationStateMachineTransitionActions::ACTION_DEFINITIVE,
            'from_state_id' => $this->openStateId,
            'to_state_id' => $this->definitiveStateId
        ]);

        $this->insert($connection, 'state_machine_transition', [
            'state_machine_id' => $stateMachineId,
            'action_name' => QuotationStateMachineTransitionActions::ACTION_ACCEPT,
            'from_state_id' => $this->definitiveStateId,
            'to_state_id' => $this->acceptedStateId
        ]);

        $this->insert($connection, 'state_machine_transition', [
            'state_machine_id' => $stateMachineId,
            'action_name' => QuotationStateMachineTransitionActions::ACTION_DECLINE,
            'from_state_id' => $this->definitiveStateId,
            'to_state_id' => $this->declinedStateId
        ]);

        $this->insert($connection, 'state_machine_transition', [
            'state_machine_id' => $stateMachineId,
            'action_name' => QuotationStateMachineTransitionActions::ACTION_EXPIRE,
            'from_state_id' => $this->definitiveStateId,
            'to_state_id' => $this->expiredStateId
        ]);
    }

    private function insertStateMachineTranslations(Connection $connection)
    {
        $this->insertTranslations($connection, 'state_machine_translation', [
            'nl-NL' => [
                [
                    'state_machine_id' => $this->stateMachineId,
                    'name' => 'Offerte status',
                ],
            ],
            'de-DE' => [
                [
                    'state_machine_id' => $this->stateMachineId,
                    'name' => 'Angebotsstatus',
                ],
            ],
            'en-GB' => [
                [
                    'state_machine_id' => $this->stateMachineId,
                    'name' => 'Quotation state',
                ],
            ],
        ]);
    }

    private function insertStateMachineStateTranslations(Connection $connection)
    {
        $this->insertTranslations($connection, 'state_machine_state_translation', [
            'nl-NL' => [
                [
                    'state_machine_state_id' => $this->openStateId,
                    'name' => 'Open',
                ],
                [
                    'state_machine_state_id' => $this->definitiveStateId,
                    'name' => 'Definitief',
                ],
                [
                    'state_machine_state_id' => $this->acceptedStateId,
                    'name' => 'Geaccepteerd',
                ],
                [
                    'state_machine_state_id' => $this->declinedStateId,
                    'name' => 'Afgewezen',
                ],
                [
                    'state_machine_state_id' => $this->expiredStateId,
                    'name' => 'Verlopen',
                ],
            ],
            'de-DE' => [
                [
                    'state_machine_state_id' => $this->openStateId,
                    'name' => 'Öffnen',
                ],
                [
                    'state_machine_state_id' => $this->definitiveStateId,
                    'name' => 'Final',
                ],
                [
                    'state_machine_state_id' => $this->acceptedStateId,
                    'name' => 'Akzeptiert',
                ],
                [
                    'state_machine_state_id' => $this->declinedStateId,
                    'name' => 'Abgelehnt',
                ],
                [
                    'state_machine_state_id' => $this->expiredStateId,
                    'name' => 'Verfallen',
                ],
            ],
            'en-GB' => [
                [
                    'state_machine_state_id' => $this->openStateId,
                    'name' => 'Open',
                ],
                [
                    'state_machine_state_id' => $this->definitiveStateId,
                    'name' => 'Definitive',
                ],
                [
                    'state_machine_state_id' => $this->acceptedStateId,
                    'name' => 'Accepted',
                ],
                [
                    'state_machine_state_id' => $this->declinedStateId,
                    'name' => 'Declined',
                ],
                [
                    'state_machine_state_id' => $this->expiredStateId,
                    'name' => 'Expired',
                ],
            ],
        ]);
    }

    public function down(Connection $connection, bool $keepUserData): void
    {
        if ($keepUserData) {
            return;
        }

        $stateMachineId = $connection->executeQuery("
            SELECT id FROM `state_machine` WHERE `technical_name` = ?
        ", [QuotationStates::STATE_MACHINE])->fetchOne();

        $connection->executeStatement("
            DELETE FROM `state_machine_transition` WHERE `state_machine_id` = ?
        ", [$stateMachineId]);
        $connection->executeStatement("
            DELETE FROM `state_machine_history` WHERE `state_machine_id` = ?
        ", [$stateMachineId]);
        $connection->executeStatement("
            DELETE FROM `state_machine` WHERE `id` = ?
        ", [$stateMachineId]);
    }
}
