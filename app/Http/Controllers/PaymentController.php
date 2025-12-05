<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

   
public function cancel(Request $request)
{
    $bookingId = $request->query('booking_id');
    
    // Update the booking status
    $booking = Booking::find($bookingId);
    
    if ($booking) {
        $booking->update([
            'payment_status' => 'canceled',
            'status' => 'cancelled',
        ]);
    }
    
    // Return the view with booking data
    return view('payment.cancel', [
        'booking' => $booking
    ]);
}
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'booking_id' => 'required|exists:bookings,id',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100, // Convert to cents
                'currency' => 'usd',
                'metadata' => [
                    'booking_id' => $request->booking_id,
                    'customer_name' => $request->customer_name,
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'booking_id' => 'required|exists:bookings,id',
            'customer_email' => 'required|email',
            'room_name' => 'required|string',
            'nights' => 'required|integer',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Hotel Booking - ' . $request->room_name,
                            'description' => $request->nights . ' night(s) stay',
                        ],
                        'unit_amount' => $request->amount * 100, // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}&booking_id=' . $request->booking_id,
                'cancel_url' => route('payment.cancel') . '?booking_id=' . $request->booking_id,
                'customer_email' => $request->customer_email,
                'metadata' => [
                    'booking_id' => $request->booking_id,
                ],
            ]);

            return response()->json([
                'sessionId' => $session->id, 
                'url' => $session->url
            ]);
            
        } catch (\Exception $e) {
            Log::error('Stripe Session Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function success(Request $request)
    {
        $booking = Booking::findOrFail($request->booking_id);
        $booking->update([
            'payment_status' => 'paid',
            'stripe_session_id' => $request->session_id,
            'status' => 'confirmed',
        ]);

        return view('payment.success', compact('booking'));
    }

   
    public function handleWebhook(Request $request)
{
    $payload = $request->getContent();
    $sig_header = $request->header('Stripe-Signature');
    $endpoint_secret = config('services.stripe.webhook');

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
        );
    } catch (\UnexpectedValueException $e) {
        return response()->json(['error' => 'Invalid payload'], 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        return response()->json(['error' => 'Invalid signature'], 400);
    }

    switch ($event->type) {
        case 'checkout.session.completed':
            $session = $event->data->object;
            $booking = Booking::where('id', $session->metadata->booking_id)->first();
            if ($booking) {
                $booking->update([
                    'payment_status' => 'paid',
                    'stripe_session_id' => $session->id,
                    'status' => 'confirmed',
                ]);
            }
            break;
            
        case 'payment_intent.succeeded':
            $paymentIntent = $event->data->object;
            // Handle payment intent success
            break;
            
        case 'payment_intent.payment_failed':
            $paymentIntent = $event->data->object;
            $booking = Booking::where('stripe_payment_intent', $paymentIntent->id)->first();
            if ($booking) {
                $booking->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled',
                ]);
            }
            break;
    }

    return response()->json(['status' => 'success']);
}

}