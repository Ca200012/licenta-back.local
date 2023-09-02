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
            $activeCart->update(['price' => $activeCart->price + $article->price]);
        } else {
            if ($existingCartItem->quantity >= $article->$sizeColumnName) {
                return response()->error('Cannot add more of this item: maximum size quantity reached.');
            }

            // Increment the item quantity
            $existingCartItem->update(['quantity' => ++$existingCartItem->quantity]);
            // Add the price of the additional item to the cart
            $activeCart->update(['price' => $activeCart->price + $article->price]);
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
            $activeCart->update(['price' => $activeCart->price - ($existingCartItem->quantity * $article->price)]);
            $existingCartItem->delete();
        } else {
            $activeCart->update(['price' => $activeCart->price - $article->price]);
            if ($existingCartItem->quantity <= 1) {
                $existingCartItem->delete();
            } else {
                $existingCartItem->update(['quantity' => --$existingCartItem->quantity]);
            }
        }

        return response()->success('Successfully removed item from cart');
    }

    public function getArticlesFromCart(Request $request)
    {
        $user = Auth::user();
        $responseArticles = [];

        if ($user) {
            $cart = $user->activeCart ?? Cart::create(['user_id' => $user->user_id]);
            $cartItems = $cart->items;

            if (!$cartItems->isEmpty()) {
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
                        'stock_message' => ($article->$selectedSizeAvailability == 0)
                            ? "We are sorry! There are no items available with this size. Please update your cart accordingly!"
                            : (($article->$selectedSizeAvailability < $cartItem->quantity)
                                ? "We are sorry! There are only " . $article->$selectedSizeAvailability . " item(s) available with this size. Please update your cart accordingly!"
                                : "")

                    ];
                })->all();

                $total_cart_price = $cart->price;
            }
        } else {
            $articlesFromRequest = $request->input('articles', []);
            foreach ($articlesFromRequest as $reqArticle) {
                $article = Article::where('article_id', $reqArticle['id'])->first();

                if ($article) {
                    $responseArticles[] = [
                        'id' => $article->id,
                        'article_id' => $article->article_id,
                        'article_number' => $article->article_number,
                        'display_name' => $article->display_name,
                        'brand_name' => $article->brand_name,
                        'colour' => $article->colour,
                        'default_image' => $article->default_image,
                        'price' => $article->price * $reqArticle['quantity'],
                        'selected_size' => $reqArticle['size'],
                        'selected_size_availability' => $article->{"size_" . strtoupper($reqArticle['size']) . "_availability"},
                        'quantity' => $reqArticle['quantity'],
                    ];
                }
            }
        }

        return response()->success([
            'articles' => $responseArticles,
            'total_cart_price' => $total_cart_price ?? 0,
        ]);
    }

    public function storeArticlesFromLs(Request $request)
    {
        $articlesFromRequest = $request->input('articles', []);
        $user = Auth::user();

        if (!$user) {
            return response()->error('User is not authenticated!');
        }

        $activeCart = $user->activeCart ?? Cart::create([
            'user_id' => $user->user_id
        ]);

        foreach ($articlesFromRequest as $reqArticle) {
            $sizeColumnName = 'size_' . strtoupper($reqArticle['size']) . '_availability';

            $article = Article::where('article_id', $reqArticle['id'])->where($sizeColumnName, '>', 0)->first();

            if ($article) {
                $existingCartItem = CartItem::where([
                    ['article_id', '=', $article->id],
                    ['cart_id', '=', $activeCart->cart_id],
                    ['size', '=', $reqArticle['size']]
                ])->first();

                if (!$existingCartItem) {
                    CartItem::create([
                        'article_id' => $article->id,
                        'cart_id' => $activeCart->cart_id,
                        'size' => $reqArticle['size'],
                        'quantity' => $reqArticle['quantity']
                    ]);

                    $activeCart->update(['price' => $activeCart->price + ($article->price * $reqArticle['quantity'])]);
                } else {
                    if (($existingCartItem->quantity + $reqArticle['quantity']) > $article->$sizeColumnName) {
                        return response()->error('Cannot add more of this item: maximum size quantity reached.');
                    }

                    // Increment the item quantity
                    $existingCartItem->update(['quantity' => $existingCartItem->quantity + $reqArticle['quantity']]);
                    // Add the price of the additional item to the cart
                    $activeCart->update(['price' => $activeCart->price + ($article->price * $reqArticle['quantity'])]);
                }
            }
        }

        return response()->success('Successfully added items to cart');
    }
}
