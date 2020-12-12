<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreateStore extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'nullable',
            'domain' => 'required|string',
            'store_url' => 'required|string'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        Validator::extend('store_url_exists', function ($attribute, $value) {
            if (! $this->store_type) return true;

            $storeUrlValidator = Str::camel("validate_" . $this->store_type . "_store_url");

            return $this->$storeUrlValidator($value);
        });
    }

    protected function validatePaystackStoreUrl($storeUrl)
    {
        try {

            $htmlResponse = Http::get($storeUrl);
    
            if ($htmlResponse->failed()) return false;
    
            return !Str::contains(strtolower($htmlResponse->body()), "storefront not found");

        } catch (\Throwable $exception) {
            return false;
        }
    }

    protected function validateFlutterwaveStoreUrl($storeUrl)
    {
        try {
            $htmlResponse = Http::get($storeUrl);
    
            if ($htmlResponse->failed()) return false;
    
            return !Str::contains(strtolower($htmlResponse->body()), "an error was encountered");
            
        } catch (\Throwable $exception) {
            return false;
        }
    }
}
