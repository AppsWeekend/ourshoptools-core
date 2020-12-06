<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Jobs\ProvisionSSL;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\HtmlService;
use App\Http\Requests\CreateStore;
use Illuminate\Support\Facades\Http;
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
        $store = Store::firstOrCreate([
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

    public function index(Request $request, HtmlService $service)
    {
        $domain = $request->getHttpHost();

        if (! $store = Store::fromDomain($this->strip($domain))) {
            return redirect()->away('https://ourshop.tools');
        }

        if (! ($response = Http::get($store->store_url))->successful()) {
            return redirect()->away('https://ourshop.tools/error');
        }

        $paytackStoreFrontBody = $service->setHtml($response->body());

        return view('index', [
            'store_url' => $store->store_url, 
            'title' => $paytackStoreFrontBody->getTitle(),
            'metas' => $paytackStoreFrontBody->getMetas()
        ]);
    }

    protected function strip($domain = "")
    {
        if (Str::contains($domain, "https://")) {
            $domain = str_replace("https://", '', $domain);
        }

        if (Str::contains($domain, "http://")) {
            $domain = str_replace("http://", '', $domain);
        }

        if (Str::contains($domain, "www.")) {
            $domain = str_replace("www.", '', $domain);
        }

        return $domain;
    }
}
