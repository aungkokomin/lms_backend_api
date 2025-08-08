<?php

namespace Database\Seeders;

use App\Models\Commission;
use App\Models\Referral;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SyncReferralDatasIntoCommissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $commissions = Commission::get();
        $commissions->each(function($commission){
            $referral = Referral::where('id',$commission->referral_id)->first();
            if($referral){
                $commission->update([
                    'referral_id' => $referral->referrer_id,
                ]);
            }
        });
    }
}
