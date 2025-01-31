<?php

namespace App\Http\Controllers;

use App\Exports\CategoryExport;
use App\Imports\CategoryImport;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        Paginator::useBootstrap();
        //Paginator::useTailwind();
        $categoríes = Category::paginate(5);
//        VarDumper::dump($categorys);
//        exit();
        //$ds_categorys = view('admin.category.all_category')->with('categorys',$categorys);
        return view('admin.category.all_category')->with('categories',$categoríes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        return view('admin.category.add_category');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'category_name' => 'required|min:6|max:50',
            'category_desc' => 'required|max:50',
            'category_status' => 'required',
            'meta_keywords' => 'required',
        ]);
        $category = new Category();
        $data = $request->all();
        $category->name = $data['category_name'];
        $category->desc = $data['category_desc'];
        $category->status = $data['category_status'];
        $category->meta_keywords = $data['meta_keywords'];
        $category->created_at = now();
        $category->updated_at = now();
        $category->save();
        Session::put('message','<p class="text-success">Thêm loại phân loại thành công</p>');
        return Redirect::to('add-category');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $category = Category::where('id', $id)->get();;
        return view('admin.category.view_category')->with('category',$category);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $category = Category::where('id', $id)->get();
        return view('admin.category.edit_category')->with('category',$category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $validated = $request->validate([
            'category_name' => 'required|min:6|max:50',
            'category_desc' => 'required|max:50',
            'category_status' => 'required',
            'meta_keywords' => 'required',
        ]);
        $data = $request->all();

        Category::where('id', $id)
            ->update
            ([
                'name'=> $data['category_name'],
                'desc'=> $data['category_desc'],
                'meta_keywords' => $data['meta_keywords'],
                'updated_at' => now()
            ]);
        Session::put('message','<p class="text-success" ">Sửa loại phân loại thành công</p>');
        return Redirect::to('all-category');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $category = Category::destroy($id);
        Session::put('message','<p class="text-success">Xóa thành công</p>');
        return Redirect::to('all-category');
    }

    public function active_category($id){
        Category::where('id', $id)
            ->update(['status' => 0]);
        Session::put('message','<p class="text-success">Update thành công</p>');
        return Redirect::to('all-category');
    }

    public function unactive_category($id){

        Category::where('id', $id)
            ->update(['status' => 1]);
        Session::put('message','<p class="text-success">Update thành công</p>');
        return Redirect::to('all-category');
    }

}
