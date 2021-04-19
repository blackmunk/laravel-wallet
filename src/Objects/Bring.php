<?php

namespace Bavix\Wallet\Objects;

use Bavix\Wallet\Interfaces\Mathable;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Models\Transfer;
use Ramsey\Uuid\Uuid;

/** @deprecated  */
class Bring
{
    private Mathable $math;

    protected string $status;

    protected Wallet $from;

    protected Wallet $to;

    protected Transaction $deposit;

    protected Transaction $withdraw;

    protected string $uuid;

    protected ?int $fee = null;

    protected int $discount = 0;

    public function __construct(Mathable $math)
    {
        $this->math = $math;
        $this->uuid = Uuid::uuid4()->toString();
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setDiscount(int $discount): self
    {
        $this->discount = $this->math->round($discount);

        return $this;
    }

    public function getFrom(): Wallet
    {
        return $this->from;
    }

    /**
     * @param Wallet $from
     *
     * @return static
     */
    public function setFrom(Wallet $from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return Wallet
     */
    public function getTo(): Wallet
    {
        return $this->to;
    }

    /**
     * @param Wallet $to
     *
     * @return static
     */
    public function setTo(Wallet $to): self
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return Transaction
     */
    public function getDeposit(): Transaction
    {
        return $this->deposit;
    }

    /**
     * @param Transaction $deposit
     *
     * @return static
     */
    public function setDeposit(Transaction $deposit): self
    {
        $this->deposit = $deposit;

        return $this;
    }

    /**
     * @return Transaction
     */
    public function getWithdraw(): Transaction
    {
        return $this->withdraw;
    }

    /**
     * @param Transaction $withdraw
     *
     * @return static
     */
    public function setWithdraw(Transaction $withdraw): self
    {
        $this->withdraw = $withdraw;

        return $this;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return int
     */
    public function getDiscount(): int
    {
        return $this->discount;
    }

    /**
     * @return int
     */
    public function getFee(): int
    {
        $fee = $this->fee;
        if ($fee === null) {
            $fee = app(Mathable::class)->round(
                app(Mathable::class)->sub(
                    app(Mathable::class)->abs($this->getWithdraw()->amount),
                    app(Mathable::class)->abs($this->getDeposit()->amount)
                )
            );
        }

        return $fee;
    }

    /**
     * @param int $fee
     *
     * @return Bring
     */
    public function setFee($fee): self
    {
        $this->fee = app(Mathable::class)->round($fee);

        return $this;
    }

    /**
     * @return Transfer
     *
     * @throws
     */
    public function create(): Transfer
    {
        return app(Transfer::class)
            ->create($this->toArray());
    }

    /**
     * @return array
     *
     * @throws
     */
    public function toArray(): array
    {
        return [
            'status' => $this->getStatus(),
            'deposit_id' => $this->getDeposit()->getKey(),
            'withdraw_id' => $this->getWithdraw()->getKey(),
            'from_type' => $this->getFrom()->getMorphClass(),
            'from_id' => $this->getFrom()->getKey(),
            'to_type' => $this->getTo()->getMorphClass(),
            'to_id' => $this->getTo()->getKey(),
            'discount' => $this->getDiscount(),
            'fee' => $this->getFee(),
            'uuid' => $this->getUuid(),
        ];
    }
}
