<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Requests\CreateStore;
use App\Http\Resources\StoreResource;

class StoreController extends Controller
{
    public function get(Request $request)
    {
        $resource = new StoreResource(Store::fromDomain($request->domain));

        return response()->json($resource->jsonSerialize());
    }

    public function create(CreateStore $request)
    {
        Store::updateOrCreate([
            'domain' => $request->domain], 
            $request->all()
        );

        return response()->json(['a_record' => '3.14.230.101']);
    }

    public function index(Request $request)
    {
        $domain = $request->getHttpHost();

        if (! $store = Store::fromDomain($domain)) {
            return redirect()->away('https://ourshop.tools');
        }

        return view('index', ['store_url' => $store->url]);
    }
}
