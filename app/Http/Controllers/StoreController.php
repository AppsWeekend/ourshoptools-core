<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Jobs\ProvisionSSL;
use Illuminate\Http\Request;
use App\Http\Requests\CreateStore;
use App\Http\Resources\StoreResource;

class StoreController extends Controller
{
    public function get(Request $request)
    {
        if (! $store = Store::fromDomain($this->strip($request->domain))) {
            return response()->json([]);
        }

        $resource = new StoreResource($store);

        return response()->json($resource->jsonSerialize());
    }

    public function create(CreateStore $request)
    {
        $store = Store::updateOrCreate([
            'domain' => $this->strip($request->domain)], 
            $request->except('domain')
        );

        return response()->json(['a_record' => '3.14.230.101']);
    }

    public function provision(Request $request)
    {
        $domain = $this->strip($request->domain);

        if ($store = Store::fromDomain($domain)) {

            ProvisionSSL::dispatch($store->domain);

            return response()->json(['message' => 'success']);
        }

        return response()->json(['message' => 'Store not found'], 404);

    }

    public function index(Request $request)
    {
        $domain = $request->getHttpHost();

        if (! $store = Store::fromDomain($domain)) {
            return redirect()->away('https://ourshop.tools');
        }

        return view('index', ['store_url' => $store->store_url]);
    }

    protected function strip($domain = "")
    {
        if (Str::contains($domain, "https://")) {
            $domain = str_replace("https://", '', $domain);
        }

        if (Str::contains($domain, "http://")) {
            $domain = str_replace("http://", '', $domain);
        }

        return $domain;
    }
}
