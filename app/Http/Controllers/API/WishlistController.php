<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Whishlist;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 6);
        $wishlist = Whishlist::with(['product'])->where('users_id', '=', $request->user()->id);
        return ResponseFormatter::success($wishlist->paginate($limit), 'Success add to whishlist');
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function wishlistByProduct(Request $request)
    {
        $product_id = $request->input('product_id');
        $wishlist = Whishlist::where('users_id', $request->user()->id)->where('products_id', $product_id)->first();
        return ResponseFormatter::success($wishlist, 'wishlist data');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'exists:products,id'
        ]);

        $wishlist = Whishlist::where('products_id', $request->product_id)->where('users_id',$request->user()->id)->first();
        if ($wishlist) {
            $wishlist->delete();
            return ResponseFormatter::success([
                'product_id' => null
            ], 'Success add to whishlist');
        }else{
            Whishlist::create(['users_id'=> $request->user()->id, 'products_id'=> $request->product_id]);
            return ResponseFormatter::success([
                'product_id' => $request->product_id
            ], 'Success add to whishlist');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $result = Whishlist::destroy($id);
        if ($result) {
            return ResponseFormatter::success([
                'product_id' => null,
            ],'Success remove wishlist');
        }else{
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ],'Failed remove wishlist', 500);
        }
    }
}
