<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Facades\Auth;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartsController extends Controller
{

    public function addToCart(Request $request)
    {
        $user = Auth::user();
        $id = $request->input('id');
        $size = $request->input('size');

        $article = Article::where('article_id', $id)->first();

        if (!$article) {
            return response()->error('Invalid article ID provided!');
        }

        $item_id = $article->id;

        // Search for an active cart (is_active = 1)
        $cart = $user->cart?->where('is_active', 1)->first();

        // If no cart exists at all, create one
        if (!$cart) {
            $cart = Cart::create(['user_id' => $user->user_id, 'is_active' => 1, 'price' => 0]);
        }

        $cartItem = $cart->items()->where('article_id', $item_id)->where('size', $size)->first();

        $columnName = 'size_' . strtoupper($size) . '_availability';

        if (!isset($article->{$columnName})) {
            return response()->error('Invalid size provided!');
        }

        $availableSizeQuantity = $article->{$columnName};

        if ($cartItem) {
            if ($cartItem->quantity + 1 > $availableSizeQuantity) {
                return response()->error('You can\'t add any more items of this type!');
            }

            $cartItem->update([
                'quantity' => ++$cartItem->quantity
            ]);

            $cart->update([
                'price' => $cart->price + $article->price
            ]);
        } else {
            CartItem::create([
                'cart_id' => $cart->cart_id,
                'article_id' => $item_id,
                'size' => $size,
            ]);

            $cart->update([
                'price' => $cart->price + $article->price
            ]);
        }

        return response()->success('Successfully added item to cart!');
    }

    public function removeFromCart(Request $request)
    {
        $user = Auth::user();

        $item_id = $request->input('id');
        $delete_all = $request->input('delete_all');

        $cart = $user->cart;

        if (!$cart) {
            return response()->error("You don't have a cart yet!");
        }

        $cartItem = $cart->items()->where('article_id', $item_id)->first();

        if (!$cartItem) {
            return response()->error('This item is not in your cart!');
        }

        $article = Article::find($item_id);

        if ($delete_all) {
            $cartItem->delete();
            return response()->success('Successfully removed item from cart!');
        }

        if ($cartItem->quantity && $cartItem->quantity > 1) {
            $cartItem->update([
                'quantity' => --$cartItem->quantity
            ]);

            $cart->update([
                'price' => $cart->price - $article->price
            ]);
        } else {
            $cartItem->delete();

            $cart->update([
                'price' => $cart->price - $article->price
            ]);
        }

        return response()->success('Successfully removed item from cart!');
    }


    public function getArticlesFromCart()
    {
        $user = Auth::user();
        $cart = $user->cart->where('is_active', 1)->first();

        if (!$cart) {
            $cart = Cart::create(['user_id' => $user->user_id, 'is_active' => 1, 'price' => 0]);
        }

        $cartItems = $cart->items;

        if ($cartItems->isEmpty()) {
            return response()->success([]);
        }

        $responseArticles = $cartItems->map(function ($cartItem) use ($cart) {
            $article = $cartItem->article;

            $selectedSizeAvailability = 'size_' . strtoupper($cartItem->size) . '_availability';
            return [
                'id' => $article->id,
                'article_id' => $article->article_id,
                'article_number' => $article->article_number,
                'display_name' => $article->display_name,
                'brand_name' => $article->brand_name,
                'colour' => $article->colour,
                'default_image' => $article->default_image,
                'price' => $article->price * $cartItem->quantity,
                'selected_size' => $cartItem->size,
                'selected_size_availability' => $article->$selectedSizeAvailability,
                'quantity' => $cartItem->quantity,
                'cart_id' => $cartItem->cart_id,
            ];
        })->all();

        return response()->success([
            'articles' => $responseArticles,
            'total_cart_price' => $cart->price, // Use the price from the cart
        ]);
    }
}
