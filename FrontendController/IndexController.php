<?php

namespace App\Http\Controllers\fontend;

use App\Cart;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGallery;
use App\Models\Slider;
use App\Models\SliderGallery;
use Exception;
//use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//use Symfony\Component\HttpFoundation\Session\Session;
use Session;

class IndexController extends Controller
{
    //

    public function index(){

        try{
            $userType = auth()->user()?auth()->user():'noUser';
            if($userType=='noUser' ||  $userType->role != 'admin'){
                $sliders = Slider::all();
                $galleries = SliderGallery::all();
                $products = Product::all();
                $prdctGlary = ProductGallery::all();
                $categories = Category::all();

                return view('fontend-view.index')->with(['sliders'=>$sliders,'galleries'=>$galleries,'products'=>$products,'prdctGlary'=>$prdctGlary,'categories'=>$categories]);
            }else if($userType->role == 'admin'){
                return view('backend.pages.dashboard');
            }else{
                 $sliders = Slider::all();
                 $galleries = SliderGallery::all();
                 $products = Product::all();
                 $prdctGlary = ProductGallery::all();
                 $categories = Category::all();

                 return view('fontend-view.index')->with(['sliders'=>$sliders,'galleries'=>$galleries,'products'=>$products,'prdctGlary'=>$prdctGlary,'categories'=>$categories]);

            }

        }catch(Exception $ex){
            print('Shanto');

        }


    }

    public function shop(){

        try{
            $userType = auth()->user()?auth()->user():'noUser';
            if($userType=='noUser' ||  $userType->role != 'admin'){
                $sliders = Slider::all();
                $galleries = SliderGallery::all();
                $products = Product::all();
                $prdctGlary = ProductGallery::all();
                $categories = Category::all();

                return view('fontend-view.shop')->with(['sliders'=>$sliders,'galleries'=>$galleries,'products'=>$products,'prdctGlary'=>$prdctGlary,'categories'=>$categories]);

            }else if($userType->role == 'admin'){

                return view('backend.pages.dashboard');
            }
        }catch(Exception $ex){
            print('Shanto');
        }

    }

    public function categoryWise($category){
        try{
            $userType = auth()->user()?auth()->user():'noUser';
            if($userType=='noUser' ||  $userType->role != 'admin'){
                 $products = Product::all()->where('product_category',$category);
                 $prdctGlary = ProductGallery::all();
                 $categories = Category::all();

        //print($products);

                return view('fontend-view.shop')->with(['products'=>$products,'prdctGlary'=>$prdctGlary,'categories'=>$categories]);


            }else if($userType->role == 'admin'){

                return view('backend.pages.dashboard');
            }
        }catch(Exception $ex){
                print('Shanto');
        }

    }

    public function cart(){
        try{
            $userType = auth()->user()?auth()->user():'noUser';
            if($userType=='noUser' ||  $userType->role != 'admin'){
                if(!Session::has('cart')){
                    return view('fontend-view.cart');
                }

                $oldCart = Session::has('cart')? Session::get('cart'):null;
                $cart = new Cart($oldCart);
                //dd($cart->items);
                /* foreach($cart->items as $ps){
                    dd($ps['product_price']);
                } */
                return view('fontend-view.cart', ['products' => $cart->items]);
            }else if($userType->role == 'admin'){

                return view('backend.pages.dashboard');
            }
        }catch(Exception $ex){
            print('Shanto');
        }

    }

    public function addToCart($id){
        try{
            $userType = auth()->user()?auth()->user():'noUser';
            $check = false;
            if($userType=='noUser' ||  $userType->role != 'admin'){
                $product = DB::table('products')
                            ->where('id', $id)
                            ->first();
                /* if($product->availability){

                } */
                $img = ProductGallery::all()->where('product_id',$product->id)->first();
                //print($img->image);

                $oldCart = Session::has('cart')? Session::get('cart'):null;
                $cart = new Cart($oldCart);

                $cart->add($product, $id , $img->image);
                //
                    //if($product->availability > $cart)
                    $psr = $cart->items;
                    foreach($psr as $ps){
                        if($ps['product_id'] == $product->id){
                            if($product->availability >= $ps['qty']){
                                $check = true;
                            }

                        }
                    }
                    if($check){
                         Session::put('cart', $cart);
                         return back()->with('satus','product added cart successfully');

                    }else{
                        Session::put('cart', $cart);
                        return redirect()->route('remove-from-cart',['id'=>$product->id])->with('stock','Sorry  thats product we have =>'.$product->availability.'<= quantity on our stock');
                        //return redirect(route('remove-from-cart',['id'=>$product->id]));
                    }
                    //print($check==false ? 'false' :'True');

                //
               // Session::put('cart', $cart);


                //return back();
            }else if($userType->role == 'admin'){

                return view('backend.pages.dashboard');
            }
        }catch(Exception $ex){
            print('Shanto');
        }

    }

    public function updateQty(Request $request,$id){
        try{
            $userType = auth()->user() ? auth()->user() : 'noUser';
            if($userType=='noUser' ||  $userType->role != 'admin'){
                //print('the product id is '.$request->id.' And the product qty is '.$request->quantity);
                $product = DB::table('products')
                            ->where('id', $id)
                            ->first();
                $oldCart = Session::has('cart')? Session::get('cart'):null;
                $cart = new Cart($oldCart);
                $cart->updateQty($id, $request->quantity);
                Session::put('cart', $cart);
                //shanto
                $psr = $cart->items;
                    foreach($psr as $ps){
                        if($ps['product_id'] == $product->id){
                            if($product->availability < $ps['qty']){
                                return redirect(route('remove-from-cart',['id'=>$product->id]))->with('stock','Sorry  thats product we have =>'.$product->availability.'<= quantity on our stock');
                            }

                        }
                    }
                return back();
                    /* if($check){
                        $cart->updateQty($id, (($request->quantity)-1));
                         Session::put('cart', $cart);
                         return back();

                    }else{
                        Session::put('cart', $cart);
                        return back();
                    } */
                //shanto

                //dd(Session::get('cart'));
                //return back();

             }else if($userType->role == 'admin'){

                return view('backend.pages.dashboard');
            }

        }catch(Exception $ex){
                print('hello');
        }

    }

    public function removeFromCart($id){
        try{
            $userType = auth()->user()?auth()->user():'noUser';
            if($userType=='noUser' ||  $userType->role != 'admin'){
                $oldCart = Session::has('cart')? Session::get('cart'):null;
                $cart = new Cart($oldCart);
                $cart->removeItem($id);

                if(count($cart->items) > 0){
                    Session::put('cart', $cart);
                }
                else{
                    Session::forget('cart');
                }

                //dd(Session::get('cart'));
                return back()->with('stock',Session::has('stock')? Session::get('stock') :null);

            }else if($userType->role == 'admin'){

                return view('backend.pages.dashboard');
            }
        }catch(Exception $ex){

        }

    }


}
