<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\SliderGallery;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SliderController extends Controller
{
    //
    public function index()
    {
        try{

            if(auth()->user()->role == 'admin'){
                return view('backend.pages.slider.add-slider');
            }

        }catch(Exception $ex){

        }

    }

    public function showSlider(){

        try{
            if(auth()->user()->role == 'admin'){
                $sliders = Slider::all();
                $galleries = SliderGallery::all();
                $inc=1;

                return view('backend.pages.slider.manage-slider')->with(['sliders'=>$sliders,'galleries'=>$galleries,'inc'=>$inc]);
            }
        }catch(Exception $ex){

        }



    }

    public function saveSlider(Request $request)
    {


        try{
            if(auth()->user()->role == 'admin'){
                $request->validate([
                'desc1' => 'required',
                'desc2' => 'required',
                ]);

                $slider = new Slider();

                $slider->desc1 = $request->desc1;
                $slider->desc2 = $request->desc2;
                $slider->save();

                $sliderId = Slider::where('id',$slider->id)->first();  //name will be unique
                //$productId = Product::all()->where('id',$request->id)->first();
                if($request->images){

                    $images = $request->file('images');
                    foreach($images as $image){
                        $image = $image;
                        $imageName = rand() . "slider" . $image->getClientOriginalName();
                        $image->move(public_path('assets/backend/img/slider-images'),$imageName);
                        $gallery = new SliderGallery();
                        $gallery->slider_id = $sliderId->id;
                        $gallery->image = "assets/backend/img/slider-images/".$imageName;
                        $gallery->save();
                    }
                }
                return back()->with(['status'=>'The Slider has been successfully saved !!','errCode'=>'402']);

            }




        }catch(Exception $ex){
            print("something wrong");
        }


    }

     public function editSlider($id)
    {
        # code...
        try{
            if(auth()->user()->role == 'admin'){
                $slider = Slider::find($id);
                $galleries = SliderGallery::all();
                return view('backend.pages.slider.edit-slider')->with(['slider'=>$slider,'galleries'=>$galleries]);
            }

        }catch(Exception $ex){

        }

    }


    public function updateSlider(Request $request,$id){

        try{
            if(auth()->user()->role == 'admin'){
                $slider = Slider::find($id);


                $slider->desc1 = $request->desc1;
                $slider->desc2 = $request ->desc2;
                $slider->update();


                $imageUnlink = SliderGallery::all()->where('slider_id',$slider->id)->pluck('image');

                if($request->images){
                    foreach($imageUnlink as $image){
                        if(file_exists($image)){
                            unlink($image);
                            DB::table('slider_galleries')->where('image',$image)->delete();

                        }
                    }


                    $images = $request->file('images');
                    foreach($images as $image){
                       // print($image);
                        $image = $image;
                        $imageName = rand() . "slider" . $image->getClientOriginalName();
                        print($imageName);
                        $image->move(public_path('assets/backend/img/slider-images'),$imageName);
                        $gallery = new SliderGallery();
                        $gallery->slider_id = $slider->id;
                        $gallery->image = "assets/backend/img/slider-images/".$imageName;
                        $gallery->save();
                    }
                }

                return redirect(route('slider'))->with(['status'=>'The slider has been successfully updated !!','errCode'=>'402']);

            }



        }catch(Exception $ex){
            return redirect(route('slider'))->with(['status'=>'The slider not updated !!','errCode'=>'404']);
        }

    }


    public function deleteSlider($id){

        try{
            if(auth()->user()->role == 'admin'){
                $imageUnlink = SliderGallery::all()->where('slider_id',$id)->pluck('image');
                 foreach($imageUnlink as $image){
                     if(file_exists($image)){
                         unlink($image);
                         DB::table('slider_galleries')->where('image',$image)->delete();
                     }
                 }
                 Slider::where('id',$id)->delete();
                 return redirect(route('slider'))->with(['status'=>'The slider has been successfully deleted !!','errCode'=>'402']);

            }



        }catch(Exception $ex){
            return redirect(route('slider'))->with(['status'=>'Error','errCode'=>'404']);
        }

     }



}
