<?php

namespace App\Jobs;

use Carbon\Traits\Serialization;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CreateOrderEmailNotification implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public $order;

    /**
     * Create a new job instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    public function generateMessage()
    {
        return $message = "Salom {$this->order->user_email}. Siz {$this->order->id} IDli buyurtma yaratdingiz. 
        Product nomi: {$this->order->product_name}. Price: {$this->order->price}. 
        Time: {$this->order->created_at}";
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Log::info($this->generateMessage());

        Mail::raw($this->generateMessage(), function ($message) {
            $message
                ->to($this->order->user->email)
                ->subject('Order created');
        });
    }
}