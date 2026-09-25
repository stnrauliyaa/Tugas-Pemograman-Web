<?php

declare(strict_types=1);

/**
 * Merepresentasikan satu transaksi keuangan (deposit atau penarikan).
 *
 * Properti disimpan private dan diisi lewat constructor property promotion,
 * sehingga objek Transaction bersifat immutable setelah dibuat (enkapsulasi ketat).
 */
final class Transaction
{
    private const TYPE_DEPOSIT = 'deposit';
    private const TYPE_WITHDRAW = 'withdraw';
    
    public function __construct(
        private readonly string $id,
        private readonly string $type,
        private readonly float $amount
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Memproses transaksi terhadap saldo sesi yang diberikan lewat referensi.
     *
     * - deposit  : saldo bertambah sebesar $amount.
     * - withdraw : ditolak (return false) bila saldo tidak mencukupi,
     *              selain itu saldo berkurang sebesar $amount.
     *
     * @param float $balance Saldo saat ini, diteruskan by-reference agar bisa dimutasi.
     * @return bool true bila transaksi berhasil diproses, false bila ditolak.
     */
    public function process(float &$balance): bool
    {
        return match ($this->type) {
            self::TYPE_DEPOSIT => $this->processDeposit($balance),
            self::TYPE_WITHDRAW => $this->processWithdraw($balance),
            default => false,
        };
    }

    private function processDeposit(float &$balance): bool
    {
        $balance += $this->amount;

        return true;
    }

    private function processWithdraw(float &$balance): bool
    {
        if ($this->amount > $balance) {
            return false;
        }

        $balance -= $this->amount;

        return true;
    }

    /**
     * Representasi array untuk disimpan di riwayat sesi.
     *
     * @return array{id: string, type: string, amount: float}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => $this->amount,
        ];
    }    
}