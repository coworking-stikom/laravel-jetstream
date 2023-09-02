<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\PopularProduct;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class PopularProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', 4);
        $popular = PopularProduct::with(['product'])->orderBy('volume', 'DESC');
        return ResponseFormatter::success($popular->paginate($limit), 'Popular product found');
    }
}
