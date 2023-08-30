<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Mail\OrderConfirmation;
use App\Models\Cart;
use Illuminate\Support\Facades\Mail;

class OrdersController extends Controller
{
    public function addOrder(Request $request)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $address_id = $request->input('address_id');
            $order_id = $request->input('order_id');

            $cart = $user->cart->where('is_active', 1)->first();

            if (!$cart) {
                return response()->error("You don't have a cart yet!");
            }

            $cartItems = $cart->items;
            $cart_id = $cart->cart_id;

            // Create a new Order
            $order = Order::create([
                'order_id' => $order_id,
                'cart_id' => $cart_id,
                'address_id' => $address_id,
                'status' => 'Pending'
            ]);

            // Update isActive field in Cart to 0
            $cart->is_active = 0;
            $cart->save();

            // Update the size_availability in each Article
            foreach ($cartItems as $cartItem) {
                $article = $cartItem->article;
                $size = $cartItem->size;
                $quantity = $cartItem->quantity;

                // Assuming your Article model has fields like size_S_availability, size_M_availability etc.
                $sizeField = 'size_' . strtoupper($size) . '_availability';

                $article->$sizeField -= $quantity;
                $article->save();
            }

            // Commit the transaction
            DB::commit();

            Mail::to($user->email)->send(new OrderConfirmation($order));

            return response()->success("Order successfully created");
        } catch (\Exception $e) {
            // Rollback the transaction in case of errors
            DB::rollback();

            return response()->error(['message' => 'Order creation failed: ' . $e->getMessage()], 400);
        }
    }

    public function getOrders()
    {
        $user = Auth::user();
        $cartIds = $user->cart->where('is_active', 0)->pluck('cart_id')->toArray();  // Get all cart_ids related to the user

        $orders = Order::with([
            'cart' => function ($query) {
                $query->select(['cart_id', 'price']); // Only select the fields you need from Cart
            },
            'cart.items' => function ($query) {
                $query->select(['cart_id', 'article_id']); // Only select the fields you need from CartItem
            },
            'cart.items.article' => function ($query) {
                $query->select(['id', 'default_image']); // Only select the fields you need from Article
            }
        ])
            ->whereIn('cart_id', $cartIds)
            ->select(['order_id', 'cart_id', 'status', 'created_at']) // Only select the fields you need from Order
            ->get();

        $formattedOrders = $orders->map(function ($order) {
            return [
                'order_id' => $order->order_id,
                'status' => $order->status,
                'order_date' => $order->created_at,
                'price' => $order->cart->price,
                'article_images' => $order->cart->items->map(function ($cartItem) {
                    return $cartItem->article->default_image;
                })->toArray()
            ];
        });

        return response()->success($formattedOrders);
    }

    public function getOrderDetails($order_id)
    {
        $user = Auth::user();
        $order = Order::with([
            'cart' => function ($query) {
                $query->with([
                    'items' => function ($query) {
                        $query->with('article');
                    }
                ]);
            }
        ])->where('order_id', $order_id)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $formattedOrderDetails = [
            'order_id' => $order->order_id,
            'status' => ucfirst($order->status),
            'order_date' => $order->created_at,
            'price' => $order->cart->price,
            'articles' => $order->cart->items->map(function ($item) {
                return [
                    'size' => $item->size,
                    'quantity' => $item->quantity,
                    'article_id' => $item->article->article_id,
                    'article_number' => $item->article->article_number,
                    'display_name' => $item->article->display_name,
                    'brand_name' => $item->article->brand_name,
                    'colour' => $item->article->colour,
                    'default_image' => $item->article->default_image,
                    'price' => $item->article->price,
                ];
            })
        ];

        return response()->json($formattedOrderDetails);
    }
}
