<?php

namespace App\Policies; 

use App\Models\Loan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class DebitCardPolicy
 */
class LoanPolicy
{
    use HandlesAuthorization;

    /**
     * View Debit cards or a specific Debit Card
     *
     * @param User           $user
     * @param DebitCard|null $debitCard
     *
     * @return bool
     */
    public function view(User $user, ?Loan $loan = null): bool
    {
        if (!$loan) {
            return true;
        }

        return $user->is($loan->user);
    }

    /**
     * Create a Debit card
     *
     * @param User  $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * View Debit cards or a specific Debit Card
     *
     * @param User           $user
     * @param DebitCard $debitCard
     *
     * @return bool
     */
    public function update(User $user, Loan $loan): bool
    {
        return $user->is($loan->user);
    }

    /**
     * View Debit cards or a specific Debit Card
     *
     * @param User           $user
     * @param DebitCard $debitCard
     *
     * @return bool
     */
    public function delete(User $user, Loan $loan): bool
    {
        return $user->is($loan->user)
            && $debitCard->debitCardTransactions()->doesntExist();
    }
}
