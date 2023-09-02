<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductGallery;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\ProductGalleryRequest;

class ProductGalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        if (request()->ajax()) {
            $query = ProductGallery::where('products_id', $product->id);

            return DataTables::of($query)
                ->addColumn('action', function ($item) {
                    return '
                        <form class="inline-block" action="' . route('dashboard.gallery.destroy', $item->id) . '" method="POST">
                        <button class="border border-red-500 bg-red-500 text-white rounded-md px-2 py-1 m-2 transition duration-500 ease select-none hover:bg-red-600 focus:outline-none focus:shadow-outline" >
                            Hapus
                        </button>
                            ' . method_field('delete') . csrf_field() . '
                        </form>';
                })
                ->editColumn('url', function ($item) {
                    return '<img style="max-width: 150px; margin:auto;" src="'. $item->url .'"/>';
                })
                ->editColumn('main_url', function ($item) {
                    $className = $item->main_image ? "bg-green-500 text-white": "bg-white text-green-500";
                    $text = $item->main_image ? "✓" : "✗";
                    return '
                    <form class="inline-block" action="' . route('dashboard.gallery.update', $item->id) . '" method="POST">
                        <button class="border border-green-500 '.$className.' hover:bg-green-600 rounded-md px-2 py-1 m-2 transition duration-500 ease select-none focus:outline-none focus:shadow-outline" >
                            '.$text.'
                        </button>
                        ' . method_field('put') . csrf_field() . '
                    </form>';

                    // if ($item->main_image) {
                    //     return '<input class="item-chexbox cursor-pointer bg-red-100 border-red-300 text-red-500 focus:ring-red-200" data-item-id="'.$item->id.'" type="checkbox" value="'.$item->main_image.'" checked />';
                    // }else{
                    //     return '<input class="item-chexbox cursor-pointer bg-red-100 border-red-300 text-red-500 focus:ring-red-200" data-item-id="'.$item->id.'" type="checkbox" value="'.$item->main_image.'" />';
                    // }
                })
                // ->editColumn('is_featured', function ($item) {
                //     return $item->is_featured ? 'Yes' : 'No';
                // })
                ->rawColumns(['main_url','action', 'url'])
                ->make();
        }

        return view('pages.dashboard.gallery.index', compact('product'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Product $product)
    {
        return view('pages.dashboard.gallery.create', compact('product'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductGalleryRequest $request, Product $product)
    {
        $files = $request->file('files');

        if($request->hasFile('files'))
        {
            foreach ($files as $file) {
                $path = $file->store('public/gallery');

                ProductGallery::create([
                    'products_id' => $product->id,
                    'url' => $path
                ]);
            }
        }

        return redirect()->route('dashboard.product.gallery.index', $product->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductGallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function show(ProductGallery $gallery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductGallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductGallery $gallery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductGallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function update(ProductGalleryRequest $request, ProductGallery $gallery)
    {
        $gallery->update(['main_image'=> !$gallery->main_image ? 1 : 0]);
        ProductGallery::where('products_id', '=', $gallery->products_id)->where('id', '!=', $gallery->id)->update(['main_image'=> 0]);

        return redirect()->route('dashboard.product.gallery.index', $gallery->products_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductGallery  $productGallery
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductGallery $gallery)
    {
        $gallery->delete();

        return redirect()->route('dashboard.product.gallery.index', $gallery->products_id);
    }
}
