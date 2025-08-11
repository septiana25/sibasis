<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Members;
use Carbon\Carbon;

class ValidMember implements ValidationRule
{
    protected $member = null;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $this->member = Members::where('member_id', $value)->first();

        if (! $this->member) {
            $fail('Member tidak ditemukan.');
            return;
        }

        $expire = $this->member->expire_date ?? null;

        if (empty($expire) || $expire === '0000-00-00') {
            $fail('Tanggal kadaluarsa member tidak valid.');
            return;
        }

        try {
            $expireDate = Carbon::parse($expire)->endOfDay();
        } catch (\Exception $e) {
            $fail('Format tanggal kadaluarsa tidak valid.');
            return;
        }

        if ($expireDate->lt(Carbon::now())) {
            $fail('Member sudah kedaluwarsa.');
            return;
        }
    }

    public function getMember()
    {
        return $this->member;
    }
}
