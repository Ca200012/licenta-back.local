<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Facades\Auth;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartsController extends Controller
{

    // Verificare articol -> daca exista
    public function addToCart(Request $request)
    {
        $sizeColumnName = 'size_' . strtoupper($request->size) . '_availability';
        $user = Auth::user();

        $article = Article::where('article_id', $request->id)->where($sizeColumnName, '>', 0)->first();

        if (!$article) {
            return response()->error('Article does not exist or desired size is not available!');
        }

        $activeCart = $user->activeCart ?? Cart::create([
            'user_id' => $user->user_id
        ]);

        $existingCartItem = CartItem::where([
            ['article_id', '=', $article->id],
            ['cart_id', '=', $activeCart->cart_id],
            ['size', '=', $request->size],
            ['quantity', '>', 0]
        ])->first();

        if (!$existingCartItem) {
            CartItem::create([
                'article_id' => $article->id,
                'cart_id' => $activeCart->cart_id,
                'size' => $request->size
            ]);
        } else {
            if ($existingCartItem->quantity >= $article->$sizeColumnName) {
                return response()->error('Cannot add more of this item: maximum size quantity reached.');
            }

            $existingCartItem->update(['quantity', ++$existingCartItem->quantity]);
        }

        return response()->success('Successfully added item to cart');
    }

    public function removeFromCart(Request $request)
    {
        $sizeColumnName = 'size_' . strtoupper($request->size) . '_availability';
        $user = Auth::user();

        $article = Article::where('article_id', $request->id)->where($sizeColumnName, '>', 0)->first();

        if (!$article) {
            return response()->error('Article does not exist or desired size is not available!');
        }

        $activeCart = $user->activeCart;

        if (!$activeCart) {
            return response()->error('No active cart found!');
        }

        $existingCartItem = CartItem::where([
            ['article_id', '=', $article->id],
            ['cart_id', '=', $activeCart->cart_id],
            ['size', '=', $request->size],
            ['quantity', '>', 0]
        ])->first();

        if (!$existingCartItem) {
            return response()->error('Item does not exist in cart!');
        }

        if ($request->delete_all) {
            $existingCartItem->delete();
        } else {
            if ($existingCartItem->quantity <= 1) {
                $existingCartItem->delete();
            } else {
                $existingCartItem->update(['quantity' => --$existingCartItem->quantity]);
            }
        }

        return response()->success('Successfully removed item from cart');
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
