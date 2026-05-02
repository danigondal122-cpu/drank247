<?php

namespace App\Enums;

enum OrderStatusEnum: int
{
    case ORDER_CREATED = 0; # Untuk ini belum tahu fungsinya apa
    case ORDER_PLACED = 1;
    case APPROVED = 2;
    case PREPARING = 3;
    case PREPARED = 4;
    case READY_FOR_PICKUP = 5;
    case DELIVERED = 6;
    case REJECTED = 7;
    case FAILED = 8;
    case PENDING = 9;
    case FINALIZED = 10;
    case CANCELED = 11;
    case ACCEPTED = 12;
    case COMPLETED = 22;
    case IN_PROGRESS = 23;

    /**
     * @return static[] [5, 6, 11, 8]
     */
    public static function getOrderStatusForFranchise(): array
    {
        return [
            self::READY_FOR_PICKUP,
            self::DELIVERED,
            self::FAILED,
            self::CANCELED,
        ];
    }

    public function getLabel(): string
    {
        return str($this->name)->replace('_', ' ')->toString();
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ORDER_PLACED      => '#d3d3d3',
            self::APPROVED          => '#adadad',
            self::PREPARING         => '#0bbfe0',
            self::PREPARED          => '#246bc7',
            self::FINALIZED,
            self::READY_FOR_PICKUP,
            self::DELIVERED         => '#39b54a',
            self::REJECTED,
            self::FAILED            => '#DD3131',
            self::PENDING           => '#A35E4E',
            self::CANCELED          => '#4e4e4e',
            self::ACCEPTED          => '#ffc006',
            self::COMPLETED         => '#FFC90E',
            self::IN_PROGRESS       => '#f2f2f2',
        };
    }
}
