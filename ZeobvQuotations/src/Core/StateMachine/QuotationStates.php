<?php


namespace Zeobv\Quotations\Core\StateMachine;


class QuotationStates
{
    public const STATE_MACHINE = 'quotation.state';

    public const STATE_OPEN = 'open';
    public const STATE_DEFINITIVE = 'definitive';
    public const STATE_ACCEPTED = 'accepted';
    public const STATE_DECLINED = 'declined';
    public const STATE_EXPIRED = 'expired';
}
