<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionConfirmation;
use App\Models\NewsletterSubscription;
use Illuminate\Support\Facades\Validator;

class NewsletterSubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'email' => 'required|email|unique:newsletter_subscriptions,email',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $subscription = new NewsletterSubscription([
            'email' => $request->input('email'),
        ]);

        $subscription->save();

        // Envoi de l'email de confirmation
        Mail::to($subscription->email)->send(new SubscriptionConfirmation($subscription));

        return response()->json(['message' => 'Abonnement à la newsletter réussi'], 201);
    }
}
