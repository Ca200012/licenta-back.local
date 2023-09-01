<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Order;
use App\Models\ViewedArticle;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationsController extends Controller
{
    public function getRecommendations(Request $request)
    {
        $user_id = Auth::id();

        // Get viewed articles
        $viewedArticles = ViewedArticle::where('user_id', $user_id)->with('article')->orderBy('times_viewed', 'desc')->get();
        $viewedMetadata = $viewedArticles->pluck('article'); // Only get article data

        // Get ordered articles
        $orderedCarts = Order::where('status', '<>', 'Cancelled')
            ->whereHas('cart', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->with('cart.items.article')
            ->get();

        $orderedMetadata = [];
        foreach ($orderedCarts as $order) {
            foreach ($order->cart->items as $item) {
                $orderedMetadata[] = $item->article;
            }
        }

        $payload = [
            'viewed' => $viewedMetadata,
            'ordered' => $orderedMetadata,
        ];

        // Send a POST request to your Python Flask API
        $response = Http::post('http://127.0.0.1:5000/recommend', $payload);
        if ($response->failed()) {
            return response()->error('Could not get recommendations!');
        }

        $recommendations_ids = json_decode($response->getBody()->getContents(), true);

        // Get the articles based on the IDs from the articles table
        $recommendations = Article::whereIn('id', $recommendations_ids)->get();

        return response()->success($recommendations);
    }
}
