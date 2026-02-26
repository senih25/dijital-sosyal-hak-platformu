<?php

declare(strict_types=1);

final class IncomeCalculatorService
{
    public function calculateHouseholdIncomeTest(float $householdIncome, int $householdMembers): array
    {
        if ($householdMembers <= 0) {
            throw new InvalidArgumentException('Hane üye sayısı en az 1 olmalıdır.');
        }

        if ($householdIncome < 0) {
            throw new InvalidArgumentException('Hane geliri negatif olamaz.');
        }

        return calculateIncomeTest($householdIncome, $householdMembers);
    }
}
