<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SyncProductsStripeAndSystemDB extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        
        $courses = Course::all();
        foreach ($courses as $course) {
            $product = $stripe->products->create([
                'name' => $course->title,
                'default_price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $course->price * 100,
                ],
            ]);

            $course->stripeProduct()->create([
                'stripe_product_id' => $product->id,
                'item_id' => $course->id,
                'item_type' => Course::class,
            ]);
        }

        $moduls = Module::all();
        foreach ($moduls as $module) {
            $product = $stripe->products->create([
                'name' => $module->title,
                'default_price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $module->price * 100,
                ],
            ]);

            $module->stripeProduct()->create([
                'stripe_product_id' => $product->id,
                'item_id' => $module->id,
                'item_type' => Module::class,
            ]);
        }
    }
}
