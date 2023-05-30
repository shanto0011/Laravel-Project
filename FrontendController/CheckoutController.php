<?php

namespace App\Http\Controllers\fontend;

use App\Cart;
use App\Http\Controllers\Controller;
use App\Models\OrderTracking;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Session;

class CheckoutController extends Controller
{
    //
    public function checkout()
    {
        # code...
        try{
            if(auth()->user()->role == 'user'){
                return view('fontend-view.checkout');
            }else{
                return redirect(route('login'));
            }

        }catch(Exception $ex){

        }
    }

    public function postCheckout(Request $request){
        try{

            if(auth()->user()->role == 'user'){
                $oldCart = Session::has('cart') ? Session::get('cart') : null;
                $cart = new Cart($oldCart);


                $trackOrder = new OrderTracking();
                $trackOrder->name = $request->name;
                $trackOrder->address = $request->address;
                $trackOrder->cart = serialize($cart);
                $trackOrder->customer_id = auth()->user()->id;
                $trackOrder->user_name = auth()->user()->name;
                $trackOrder->save();
                //dd($cart->items);
                foreach($cart->items as $item){
                    //dd($item);
                    $product =Product::find($item['product_id']);
                    $productQty = $product->availability - $item['qty'];
                    $product->availability = $productQty;
                    $product->update();
                }
                Session::forget('cart');


                return redirect(route('cart'))->with('status',"Your purchase has been successfully accomplished");
            }else{
                return redirect(route('login'));
            }

        }catch(Exception $ex){

        }
    }
}
