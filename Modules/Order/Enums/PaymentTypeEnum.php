<?php

namespace Modules\Order\Enums;

enum PaymentTypeEnum: string
{
    case PIX = 'pix';
    case CREDITO_1X = 'credito_1x';
    case CREDITO_PARCELADO = 'credito_parcelado';

    public static function fromString(string $tipo): self
    {
        return match ($tipo) {
            'pix' => self::PIX,
            'credito_1x' => self::CREDITO_1X,
            'credito_parcelado' => self::CREDITO_PARCELADO,
            default => throw new \InvalidArgumentException("Tipo de pagamento inválido: $tipo"),
        };
    }
}
