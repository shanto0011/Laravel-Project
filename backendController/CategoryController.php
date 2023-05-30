<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class CategoryController extends Controller
{
    //
    public function addCategory()
    {
        # code...
        try{

            if(auth()->user()->role == 'admin'){
                return view('backend.pages.category.add-category');
            }

        }catch(Exception $ex){
            print('Shanto');
        }

    }

    public function saveCategory(Request $request)
    {

        try{
            if(auth()->user()->role == 'admin'){
                $request->validate(['category_name'=>'required | unique:categories']);
                $category = new Category();
                $category->category_name = $request->category_name;
                $category->save();
                return back()->with(["status"=>'The Category added successfully','errCode'=>'402']);
            }



        }catch(Exception $ex){
            return back()->with(['status'=>'The Category already have','errCode'=>'404'],);
        }
    }

    public function showCategory()
    {
        # code...
        try{
            if(auth()->user()->role == 'admin'){
                $categories = Category::all();
                $incremnt =0;
                return view('backend.pages.category.manage-category')->with(['categories'=>$categories,'incremnt'=>$incremnt]);
            }

        }catch(Exception $ex){
            print('try catch error');
        }

    }

    public function editCategory($id)
    {
            if(auth()->user()->role == 'admin'){
                $category = Category::find($id);
                return view('backend.pages.category.edit-category')->with('category',$category);
            }

    }
    public function updateCategory(Request $request)
    {
        try{
            if(auth()->user()->role == 'admin'){
                $this->validate($request,['category_name' =>'required| unique:categories']);

                $category = Category::find($request->input('id'));
                $category->category_name = $request->input('category_name');
                $category->update();
                return redirect('/manage-category')->with(['status'=>'The Category updated successfully','errCode'=>'402']);
            }

        }catch(Exception $ex){
            return redirect('/manage-category')->with(['status'=>'The Category updated not complete','errCode'=>'404']);
        }
        //print('The category id is'. $request->id . 'And the Category name is '.$request->category_name);

        //return redirect('/category')->with('status','The category name has been successfully updated');
    }

    public function deleteCategory($id)
    {
        try{

            if(auth()->user()->role == 'admin'){
                $category = Category::find($id);
                //$category->delete();


                /* $deleteElement = Product::all()->where('product_category',$category->category_name);
                foreach($deleteElement as $element){
                    $deleteImage = Gallery::all()->where('product_id',$element->id);
                    foreach($deleteImage as $imgElement){
                         DB::table('galleries')->where('id', $imgElement->id )->delete();
                    }
                    DB::table('products')->where('id', $element->id )->delete();
                } */
                $category->delete();

                return back()->with('status','The category name has been successfully deleted');

            }


        }catch(Exception $ex){

        }


    }
}

