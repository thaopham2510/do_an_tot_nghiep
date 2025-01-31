<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\CategoryProductProduct;
use App\Models\Comment;
use App\Models\Gallery;
use App\Models\Post;
use App\Models\PostType;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\Rating;
use App\Models\Slider;
use App\Models\Tag;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\VarDumper;

class SiteController extends Controller
{
    //
    //===================Function cho frontend======================
    public function products_by_category($code, Request $request){
        $post_types = PostType::where('status',1)->get();

        $categories = Category::where('status',1)->get();

        $category_products = CategoryProduct::where('category_product_status',1)->get();
        $brands = Brand::where('brand_status',1)->get();
        $category_product = CategoryProduct::where('code',$code)->first();

//        $sanphams_by_category = $category_product->product_groups;
        $sliders = Slider::where('an_hien',1)->get();

        $meta_desc = $category_product->category_product_desc;
        $meta_keywords = $category_product->meta_keywords;
        $meta_title = $category_product->category_product_name;
        $url_canonical = $request->url();

        $min_price = Product::min('gia_ban');
        $max_price = Product::max('gia_ban') +1000;
        if(isset($_GET['sort_by'])){
            $sort_by = $_GET['sort_by'];
            if($sort_by=='giam_dan'){
                $sanphams_by_category = ProductGroup::join('products', 'products.product_group_id', '=', 'product_groups.id')
                    ->join('category_product_product_group as cp','cp.product_group_id','product_groups.id')
                    ->where('cp.category_product_id',$category_product->id)
                    ->orderBy('products.gia_ban', 'DESC')->select('product_groups.*')
                    ->distinct()->paginate(6)->appends(request()->query());
//                ->appends(request()->query()) là để khi phân trang vẫn giữ nguyên lọc
            }elseif ($sort_by=='tang_dan'){
                $sanphams_by_category = ProductGroup::join('products', 'products.product_group_id', '=', 'product_groups.id')
                    ->join('category_product_product_group as cp','cp.product_group_id','product_groups.id')
                    ->where('cp.category_product_id',$category_product->id)
                    ->orderBy('products.gia_ban', 'ASC')->select('product_groups.*')
                    ->distinct()->paginate(6)->appends(request()->query());
            }elseif ($sort_by=='kytu_za'){
                $sanphams_by_category = ProductGroup::join('products', 'products.product_group_id', '=', 'product_groups.id')
                    ->join('category_product_product_group as cp','cp.product_group_id','product_groups.id')
                    ->where('cp.category_product_id',$category_product->id)
                    ->orderBy('product_groups.name', 'DESC')->select('product_groups.*')
                    ->distinct()->paginate(6)->appends(request()->query());
            }elseif ($sort_by=='kytu_az'){
                $sanphams_by_category = ProductGroup::join('products', 'products.product_group_id', '=', 'product_groups.id')
                    ->join('category_product_product_group as cp','cp.product_group_id','product_groups.id')
                    ->where('cp.category_product_id',$category_product->id)
                    ->orderBy('product_groups.name', 'ASC')->select('product_groups.*')
                    ->distinct()->paginate(6)->appends(request()->query());
            }
        }else if (isset($_GET['start_price']) && isset($_GET['end_price'])){
            $min_price = $_GET['start_price'];
            $max_price = $_GET['end_price'];
            $sanphams_by_category = ProductGroup::join('products', 'products.product_group_id', '=', 'product_groups.id')
                ->join('category_product_product_group as cp','cp.product_group_id','product_groups.id')
                ->where('cp.category_product_id',$category_product->id)
                ->whereBetween('products.gia_ban',[$min_price,$max_price])->orderBy('products.gia_ban','ASC')->select('product_groups.*')
                ->distinct()->paginate(6)->appends(request()->query());
        }else{
            $sanphams_by_category = $category_product->product_groups;
        }


        return view('frontend.category.products_by_category')
            ->with('min_price',$min_price)
            ->with('max_price',$max_price)
            ->with('post_types',$post_types)
            ->with('categories',$categories)
            ->with('category_product',$category_product)
            ->with('category_products',$category_products)
            ->with('brands',$brands)
            ->with('sanphams_by_category',$sanphams_by_category)
            ->with('sliders',$sliders)
            ->with('meta_desc',$meta_desc)
            ->with('meta_keywords',$meta_keywords)
            ->with('meta_title',$meta_title)
            ->with('url_canonical',$url_canonical);
    }

    public function products_by_brand($code, Request $request){
        $post_types = PostType::where('status',1)->get();

        $categories = Category::where('status',1)->get();

        $category_products = CategoryProduct::where('category_product_status',1)->get();
        $brands = Brand::where('brand_status',1)->get();
        $brand = Brand::where('brand_slug',$code)->first();
        $sanphams_by_brand = $brand->product_groups;
        $sliders = Slider::where('an_hien',1)->get();


        $meta_desc = $brand->brand_desc;
        $meta_keywords = $brand->meta_keywords;
        $meta_title = $brand->brand_name;
        $url_canonical = $request->url();
//        foreach ($category_product->products as $product)
//            dd($product);
        return view('frontend.brand.products_by_brand')
            ->with('post_types',$post_types)
            ->with('categories',$categories)
            ->with('brand',$brand)
            ->with('category_products',$category_products)
            ->with('brands',$brands)
            ->with('sanphams_by_brand',$sanphams_by_brand)
            ->with('sliders',$sliders)
            ->with('meta_desc',$meta_desc)
            ->with('meta_keywords',$meta_keywords)
            ->with('meta_title',$meta_title)
            ->with('url_canonical',$url_canonical);
    }

    public function product_by_id($code, Request $request){
        $post_types = PostType::where('status',1)->get();

        $categories = Category::where('status',1)->get();

        $category_products = CategoryProduct::where('category_product_status',1)->get();
        $brands = Brand::where('brand_status',1)->get();

//        $san_pham_chi_tiet = ProductGroup::where('code',$code)->first();
        $phien_ban_san_pham = Product::where('code',$code)->first();
        $nhom_san_pham = ProductGroup::where('id',$phien_ban_san_pham->product_group_id)->first();
        $sliders = Slider::where('an_hien',1)->get();

        //Rating sản phẩm
        $rating = Rating::where('product_id',$phien_ban_san_pham->id)->avg('rating');//tính trung bình rating
        $rating = round($rating);


        $meta_desc = $nhom_san_pham->mo_ta_ngan_gon;
        $meta_keywords = $nhom_san_pham->meta_keywords;
        $meta_title = $nhom_san_pham->name;
        $url_canonical = $request->url();


        $san_pham_lien_quan = array();
        foreach ($nhom_san_pham->category_products as $category_product){
            $san_pham_lien_quan[] = CategoryProductProduct::where('category_product_id',$category_product->id)
            ->where('product_group_id','!=',$nhom_san_pham->id)->get();
        }
        $id_san_pham = array();
//        VarDumper::dump($san_pham_lien_quan);
//        exit();
        foreach ($san_pham_lien_quan as $san_pham)
            foreach ($san_pham as $item)
                $id_san_pham[]=$item->product_group_id;

        //VarDumper::dump(array_unique($id_san_pham));
        $san_phams_lien_quan = ProductGroup::find($id_san_pham);
//        VarDumper::dump($san_phams_lien_quan);
//        foreach ($san_phams_lien_quan as $san_pham)
//            VarDumper::dump($san_pham->anh_dai_dien);
//        exit();

        //update view of product
        $phien_ban_san_pham->views+=1;
        $phien_ban_san_pham->save();


        return view('frontend.product.chi-tiet-san-pham')
            ->with('post_types',$post_types)
            ->with('categories',$categories)
            ->with('phien_ban_san_pham',$phien_ban_san_pham)
            ->with('rating',$rating)
            ->with('nhom_san_pham',$nhom_san_pham)
            ->with('category_products',$category_products)
            ->with('brands',$brands)
            ->with('san_phams_lien_quan',$san_phams_lien_quan)
            ->with('sliders',$sliders)
            ->with('meta_desc',$meta_desc)
            ->with('meta_keywords',$meta_keywords)
            ->with('meta_title',$meta_title)
            ->with('url_canonical',$url_canonical);
    }

    public function danh_muc_bai_viet($code, Request $request){
        $post_types = PostType::where('status',1)->get();
        $categories = Category::where('status',1)->get();

        $category_products = CategoryProduct::where('category_product_status',1)->get();
        $brands = Brand::where('brand_status',1)->get();

        $sliders = Slider::where('an_hien',1)->get();
        $post_type = PostType::where('code',$code)->first();

        Paginator::useBootstrap();
        $posts = Post::where('post_type_id',$post_type->id)->where('status',1)->paginate(4);

        $meta_desc = $post_type->desc;
        $meta_keywords = $post_type->meta_keywords;
        $meta_title = $post_type->name;
        $url_canonical = $request->url();
        return view('frontend.post.post_type')->with(compact('post_type'))
            ->with('post_types',$post_types)
            ->with('posts',$posts)
            ->with('categories',$categories)
            ->with('category_products',$category_products)
            ->with('brands',$brands)
            ->with('sliders',$sliders)
            ->with('meta_desc',$meta_desc)
            ->with('meta_keywords',$meta_keywords)
            ->with('meta_title',$meta_title)
            ->with('url_canonical',$url_canonical);
    }

    public function chi_tiet_bai_viet($danh_muc, $bai_viet,Request $request){
        $post_types = PostType::where('status',1)->get();
        $categories = Category::where('status',1)->get();

        $category_products = CategoryProduct::where('category_product_status',1)->get();
        $brands = Brand::where('brand_status',1)->get();

        $sliders = Slider::where('an_hien',1)->get();

        $post_type = PostType::where('code',$danh_muc)->first();
        $post = Post::where('code',$bai_viet)->first();

        $related_posts = Post::where('post_type_id',$post_type->id)
            ->where('id','!=',$post->id)
            ->get();
        //update view of post
        $post->views+=1;
        $post->save();

        $meta_desc = $post->desc;
        $meta_keywords = $post->meta_keywords;
        $meta_title = $post->name;
        $url_canonical = $request->url();
        return view('frontend.post.post')
            ->with('post_types',$post_types)
            ->with('categories',$categories)
            ->with('category_products',$category_products)
            ->with('brands',$brands)
            ->with('sliders',$sliders)
            ->with(compact('post_type'))
            ->with('post',$post)
            ->with('related_posts',$related_posts)
            ->with('meta_desc',$meta_desc)
            ->with('meta_keywords',$meta_keywords)
            ->with('meta_title',$meta_title)
            ->with('url_canonical',$url_canonical);
    }

    public function watch_video(Request $request){
        $data = $request->all();
        $video_id = $data['video_id'];
        $video = Video::find($video_id);
        $output['video_title'] = $video->title;
        $output['video_desc'] = $video->desc;
//        $output['video_link'] = ' <iframe width="100%"
//                                height="315"
//                                src="https://www.youtube.com/embed/'.$video->link.'?autoplay=1"
//                                title="YouTube video player"
//                                frameborder="0"
//                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
//                                allowfullscreen>
//                                </iframe>';
        $output['video_link']='<div id="youtube_review" class="vlite-js" data-youtube-id="'.$video->link.'"></div>';
        echo json_encode($output);
    }

    public function products_by_tag(Request $request, $code){
        $post_types = PostType::where('status',1)->get();

        $categories = Category::where('status',1)->get();

        $category_products = CategoryProduct::where('category_product_status',1)->get();
        $brands = Brand::where('brand_status',1)->get();
        $tag = Tag::where('code',$code)->first();
        $sanphams_by_tag = $tag->products;
        $sliders = Slider::where('an_hien',1)->get();


        $meta_desc = $tag->desc;
        $meta_keywords = $tag->meta_keywords;
        $meta_title = $tag->name;
        $url_canonical = $request->url();
//        foreach ($category_product->products as $product)
//            dd($product);
        return view('frontend.product.products_by_tag')
            ->with('post_types',$post_types)
            ->with('categories',$categories)
            ->with('tag',$tag)
            ->with('category_products',$category_products)
            ->with('brands',$brands)
            ->with('sanphams_by_tag',$sanphams_by_tag)
            ->with('sliders',$sliders)
            ->with('meta_desc',$meta_desc)
            ->with('meta_keywords',$meta_keywords)
            ->with('meta_title',$meta_title)
            ->with('url_canonical',$url_canonical);
    }

    public function quick_view(Request $request){
        $product_id = $request->product_id;
        $product = ProductGroup::find($product_id);

        $gallery = Gallery::where('product_id',$product_id)->get();

        $output['product_gallery']='';
        foreach ($gallery as $gal){
            $output['product_gallery'].='<p><img width="100%" src="'.asset('public/uploads/gallery/'.$gal->image).'" alt="'.$gal->name.'"></p>';
        }

        $output['product_name'] = $product->name;
        $output['product_id'] = $product->id;
        $output['product_desc'] = $product->mo_ta_ngan_gon;
        $output['product_content'] = $product->mo_ta_chi_tiet;
        $output['product_price']= number_format($product->gia_ban,0,',','.').' VND';
        $output['product_image']='<p><img width="100%" src="'.asset('public/uploads/products/'.$product->anh_dai_dien).'" alt="'.$product->name.'"></p>';

        $output['product_quickview_button'] = '
             <input type="button" value="Mua ngay" class="btn btn-primary  add-to-cart-quickview"
                                   data-product_id="'.$product->id.'" name="add-to-cart">
        ';

        $output['product_quickview_value']='
            <input type="hidden"  class="cart_product_id_'.$product->id.'" value="'.$product->id.'">
            <input type="hidden"  class="cart_product_name_'.$product->id.'" value="'.$product->name.'">
            <input type="hidden"  class="cart_product_image_'.$product->id.'" value="'.$product->anh_dai_dien.'">
            <input type="hidden"  class="cart_product_price_'.$product->id.'" value="'.$product->gia_ban.'">
            <input type="hidden"  class="product_qty_'.$product->id.'" value="'.$product->so_luong.'">
        ';

        $output['product_quickview_cartQty'] = '<input name="so_luong" type="number" min="1" class="cart_product_qty_'.$product->id.'" value="1">';

        $output['go_to_product_detail'] = '<a class="btn btn-primary" href="'.route('product',['code'=>$product->code]).'">Đi tới sản phẩm</a>';

        echo json_encode($output);
    }

    public function load_comment(Request $request){
        $data = $request->all();
        $comments_by_product = Comment::where('product_id',$data['product_id'])->where('da_duyet',1)->get();
        $output = '';
        foreach ($comments_by_product as $key=>$comment){
            if ($comment->parent_id==null){
                $output .='
            <div class="container">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="post-content">
                                    <div class="post-container">
                                        <img src="'.url('public/uploads/others/customer.png').'" alt="user" class="profile-photo-md pull-left">
                                        <div class="post-detail">
                                            <div class="user-info">
                                                <h5><a href="timeline.html" class="profile-link">'.$comment->name.'</a> </h5>
                                                <p class="text-muted">'.date('d-m-Y H:i:s', strtotime($comment->created_at)).'</p>
                                            </div>
                                            <div class="line-divider"></div>
                                            <div class="post-text">
                                                <p>'.$comment->content.'<i class="em em-anguished"></i> <i class="em em-anguished"></i> <i class="em em-anguished"></i></p>
                                            </div>
                                            <div class="line-divider"></div>';
            }
            foreach ($comments_by_product as $key=>$comment_reply)
                if($comment_reply->parent_id==$comment->id){
                    $output.='
                                            <div class="post-comment">
                                                <img src="'.url('public/uploads/others/admin.jpg').'" alt="" class="profile-photo-sm">
                                                <p class="reply-name"><a href="" class="profile-link">'.$comment_reply->name.'</a></p>
                                                </br>
                                                <p class="reply-content" style="margin-left: 5px">  '.$comment_reply->content.'</p>
                                                </br>
                                            </div>
                                            <p> '.date('d-m-Y H:i:s', strtotime($comment_reply->created_at)).'</p>

            ';
                }
            $output.='                     </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';

        }
        if($output!=''){
            echo $output;
        }
        else{
            $output = "Chưa có bình luận cho sản phẩm này";
            echo $output;
        }
    }

    public function send_comment(Request $request){
        $data = $request->all();
        $comment = new Comment();
        $comment->name = $data['comment_name'];
        $comment->product_id = $data['product_id'];
        $comment->email = $data['comment_email'];
        $comment->content = $data['comment_content'];
        $comment->save();
    }

    public function load_rating(Request $request){
        $data = $request->all();
        $reviews_by_product = Rating::where('product_id',$data['product_id'])->get();
        $output = '';
        foreach ($reviews_by_product as $key=>$review){
                $output .='
            <div class="container">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="post-content">
                                    <div class="post-container">
                                        <img src="'.url('public/uploads/others/customer.png').'" alt="user" class="profile-photo-md pull-left">
                                        <div class="post-detail">
                                            <div class="user-info">
                                                <h5><a href="timeline.html" class="profile-link">'.$review->name.'</a> </h5>
                                                <p class="text-muted">'.date('d-m-Y H:i:s', strtotime($review->created_at)).'</p>
                                            </div>
                                            <div class="line-divider"></div>
                                            <div class="post-text">
                                                <p>'.$review->content.'<i class="em em-anguished"></i> <i class="em em-anguished"></i> <i class="em em-anguished"></i></p>
                                            </div>
                                            <ul class="list-inline" title="Average Rating">';

                    for($count=1;$count<=5;$count++){
                            if ($count<=$review->rating)
                                $color = 'color:#ffcc00;';//nếu count<rating thì hiện màu vàng để hiển thị sao
                            else
                                $color = 'color:#ccc;';//Ngược lại sao màu xám
            $output.='
                        <li title="Đánh giá sao"
                    id=""
                    data-index=""
                    data-product_id=""
                    data-rating=""
                    class=""
                    style="cursor: pointer; '.$color.' font-size: 30px">
                    &#9733;
                    </li>';
          }
                    $output.='
                </ul>
                                            <div class="line-divider"></div>';

            $output.='                     </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';

        }
        if($output!=''){
            echo $output;
        }
        else{
            $output = "Chưa có đánh giá cho sản phẩm này";
            echo $output;
        }
    }

    public function insert_rating(Request $request){
        $data = $request->all();
        $rating = new Rating();
        $rating->product_id = $data['product_id'];
        $rating->rating = $data['index'];
        $rating->name = $data['rating_name'];
        $rating->email = $data['rating_email'];
        $rating->content = $data['rating_content'];
        $rating->save();
    }
}

