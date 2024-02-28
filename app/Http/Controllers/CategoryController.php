<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Components\Recusive;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    //chứa dữ liệu của category
    private $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }
    public function create()
    {
        $htmlOption = $this->getCategory($parent_id = '');
        return view('admin.category.add', compact('htmlOption'));
    }

    public function index()
    {
        $categories = $this->category->latest()->paginate(5);
        //latest(): sắp xếp
        //paginate(): phân trang
        return view("admin.category.index", compact('categories'));
    }
    public function store(Request $request)
    {
        $this->category->create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'slug' => Str::slug($request->name)
            //Str::slug: để tạo ra một slug cho tiêu đề đó, ví dụ: "cach-su-dung-str-slug-trong-laravel"
        ]);
        return redirect()->route('categories.index');
    }
    public function getCategory($parent_id)
    {
        $data = $this->category->all();
        // Recusive là 1 class component dc tạo trong app/component
        // Trả về 1 thẻ option theo dạng nối chuỗi
        $recusive = new Recusive($data);
        $htmlOption = $recusive->categoryRecusive($parent_id);
        return $htmlOption;
    }
    public function edit($id)
    {
        $category = $this->category->find($id);
        $htmlOption = $this->getCategory($category->parent_id);
        return view('admin.category.edit', compact('category', 'htmlOption'));
    }

    public function update($id, Request $request)
    {
        $this->category->find($id)->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'slug' => Str::slug($request->name)
        ]);
        return redirect()->route('categories.index');
    }

    public function delete($id)
    {
        // Này dùng soft delete
        $this->category->find($id)->delete();
        return redirect()->route('categories.index');
    }
}