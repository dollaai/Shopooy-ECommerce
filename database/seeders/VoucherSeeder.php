<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;


class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

    $voucher_type = ['discount', 'cashback'];
        // $voucher_cashback_type = ['percentage', 'fixed'];
        $data = [
            [
                'code' => 'HEMAT10',
                'name' => 'Diskon 10%',
                'voucher_type' => 'discount',
                'voucher_cashback_type' => 'percentage',
                'discount_cashback_value' => 10,
                'discount_cashback_max' => 50000,
                'min_transaction' => 100000,
                'is_public' => true,
                'start_date' => now()->subDays(3),
                'end_date' => now()->addDays(30),
            ],
            [
                'code' => 'HEMAT20',
                'name' => 'Diskon 20%',
                'voucher_type' => 'discount',
                'voucher_cashback_type' => 'percentage',
                'discount_cashback_value' => 20,
                'discount_cashback_max' => 100000,
                'min_transaction' => 250000,
                'is_public' => true,
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(45),
            ],
            [
                'code' => 'DISC50K',
                'name' => 'Potongan Rp50.000',
                'voucher_type' => 'discount',
                'voucher_cashback_type' => 'fixed',
                'discount_cashback_value' => 50000,
                'discount_cashback_max' => null,
                'min_transaction' => 300000,
                'is_public' => false,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(20),
            ],
            [
                'code' => 'DISC100K',
                'name' => 'Potongan Rp100.000',
                'voucher_type' => 'discount',
                'voucher_cashback_type' => 'fixed',
                'discount_cashback_value' => 100000,
                'discount_cashback_max' => null,
                'min_transaction' => 750000,
                'is_public' => true,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(60),
            ],
            [
                'code' => 'WELCOME15',
                'name' => 'Welcome Discount 15%',
                'voucher_type' => 'discount',
                'voucher_cashback_type' => 'percentage',
                'discount_cashback_value' => 15,
                'discount_cashback_max' => 75000,
                'min_transaction' => 200000,
                'is_public' => true,
                'start_date' => now()->subDays(7),
                'end_date' => now()->addDays(90),
            ],

            // Cashback
            [
                'code' => 'CASH5',
                'name' => 'Cashback 5%',
                'voucher_type' => 'cashback',
                'voucher_cashback_type' => 'percentage',
                'discount_cashback_value' => 5,
                'discount_cashback_max' => 25000,
                'min_transaction' => 100000,
                'is_public' => true,
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(30),
            ],
            [
                'code' => 'CASH10',
                'name' => 'Cashback 10%',
                'voucher_type' => 'cashback',
                'voucher_cashback_type' => 'percentage',
                'discount_cashback_value' => 10,
                'discount_cashback_max' => 50000,
                'min_transaction' => 250000,
                'is_public' => false,
                'start_date' => now()->subDays(4),
                'end_date' => now()->addDays(45),
            ],
            [
                'code' => 'CB25K',
                'name' => 'Cashback Rp25.000',
                'voucher_type' => 'cashback',
                'voucher_cashback_type' => 'fixed',
                'discount_cashback_value' => 25000,
                'discount_cashback_max' => null,
                'min_transaction' => 150000,
                'is_public' => true,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(40),
            ],
            [
                'code' => 'CB50K',
                'name' => 'Cashback Rp50.000',
                'voucher_type' => 'cashback',
                'voucher_cashback_type' => 'fixed',
                'discount_cashback_value' => 50000,
                'discount_cashback_max' => null,
                'min_transaction' => 500000,
                'is_public' => false,
                'start_date' => now()->subDays(3),
                'end_date' => now()->addDays(50),
            ],
            [
                'code' => 'LOYAL15',
                'name' => 'Cashback 15%',
                'voucher_type' => 'cashback',
                'voucher_cashback_type' => 'percentage',
                'discount_cashback_value' => 15,
                'discount_cashback_max' => 100000,
                'min_transaction' => 500000,
                'is_public' => true,
                'start_date' => now()->subDays(6),
                'end_date' => now()->addDays(90),
            ],
            [
                'code' => 'FLASH50',
                'name' => 'Flash Sale 50%',
                'voucher_type' => 'discount',
                'voucher_cashback_type' => 'percentage',
                'discount_cashback_value' => 50,
                'discount_cashback_max' => 250000,
                'min_transaction' => 500000,
                'is_public' => false,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(14),
            ],
            [
                'code' => 'VIP75',
                'name' => 'VIP Discount 75%',
                'voucher_type' => 'discount',
                'voucher_cashback_type' => 'percentage',
                'discount_cashback_value' => 75,
                'discount_cashback_max' => 500000,
                'min_transaction' => 1000000,
                'is_public' => false,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDays(7),
            ],
        ];

        foreach ($data as $item) {
            if (random_int(0, 1)) {
                $item['seller_id'] = null;
                } else {
                    $item['seller_id'] = User::whereHas('products')->inRandomOrder()->value('id');
            }
            $seller_id = $item['seller_id'];
            \App\Models\Voucher::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'seller_id' => $item['seller_id'],
                'code' => $item['code'],
                'name' => $item['name'],
                'voucher_type' => $item['voucher_type'],
                'voucher_cashback_type' => $item['voucher_cashback_type'],
                'is_public' => $item['is_public'],
                'discount_cashback_value' => $item['discount_cashback_value'],
                'discount_cashback_max' => $item['discount_cashback_max'],
                'min_transaction' => $item['min_transaction'],
                'start_date' => $item['start_date'],
                'end_date' => $item['end_date'],
            ]);
        }
    }
}
