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
            // Handle the case where the article does not exist.
            return response()->error('Invalid article ID provided!');
        }

        $item_id = $article->id;
        $cart = $user->cart ?: Cart::create(['user_id' => $user->user_id]);
        $cartItem = $cart->items()->where('article_id', $item_id)->where('size', $size)->first();

        // Determine the column name dynamically based on the size
        $columnName = 'size_' . strtoupper($size) . '_availability';

        if (!isset($article->{$columnName})) {
            return response()->error('Invalid size provided!');
        }

        $availableSizeQuantity = $article->{$columnName};

        if ($cartItem) {
            // If the item's new quantity exceeds availability, return an error
            if ($cartItem->quantity + 1 > $availableSizeQuantity) {
                return response()->error('You can\'t add any more items of this type!');
            }

            $cartItem->update([
                'quantity' => ++$cartItem->quantity
            ]);
        } else {
            CartItem::create([
                'cart_id' => $cart->cart_id,
                'article_id' => $item_id,
                'size' => $size
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

        if ($delete_all) {
            $cartItem->delete();
            return response()->success('Successfully removed item from cart!');
        }

        if ($cartItem->quantity && $cartItem->quantity > 1) {
            $cartItem->update([
                'quantity' => --$cartItem->quantity
            ]);
        } else {
            $cartItem->delete();
        }

        return response()->success('Successfully removed item from cart!');
    }

    public function getArticlesFromCart()
    {
        $user = Auth::user();
        $cart = $user->cart;

        if (!$cart) {
            return response()->error("You don't have a cart yet!");
        }

        $cartItems = $cart->items;

        if ($cartItems->isEmpty()) {
            return response()->success([]);
        }

        $total_order_price = 0;

        $responseArticles = $cartItems->map(function ($cartItem) use (&$total_order_price) {
            $article = $cartItem->article;

            $selectedSizeAvailability = 'size_' . strtoupper($cartItem->size) . '_availability';
            $total_price = $article->price * $cartItem->quantity;
            $total_order_price += $total_price;
            return [
                'id' => $article->id,
                'article_id' => $article->article_id,
                'article_number' => $article->article_number,
                'display_name' => $article->display_name,
                'brand_name' => $article->brand_name,
                'colour' => $article->colour,
                'default_image' => $article->default_image,
                'price' => $article->price,
                'total_price' => $total_price,
                'selected_size' => $cartItem->size,
                'selected_size_availability' => $article->$selectedSizeAvailability,
                'quantity' => $cartItem->quantity,
                'cart_id' => $cartItem->cart_id
            ];
        })->all();


        return response()->success([
            'articles' => $responseArticles,
            'total_order_price' => $total_order_price,
        ]);
    }
}
