<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGallery;
use App\Models\Slider;
use App\Models\SliderGallery;
use Illuminate\Http\Request;

class AdminCustomer extends Controller
{
    //

    /* public function admin()
    {
        # code...
        return view('backend.pages.dashboard');
    } */
    public function adminUser()
    {
        $userType=auth()->user();
        //print($userType->role);
        if($userType->role  == 'user'){
            $sliders = Slider::all();
            $galleries = SliderGallery::all();
            $products = Product::all();
            $prdctGlary = ProductGallery::all();
            $categories = Category::all();

            return view('fontend-view.index')->with(['sliders'=>$sliders,'galleries'=>$galleries,'products'=>$products,'prdctGlary'=>$prdctGlary,'categories'=>$categories]);

        }else if($userType->role   == 'admin'){
            //print($userType->role);

            return view('backend.pages.dashboard');
        }
    }

}
