<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Province;
use App\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $address = Auth::user()->addresses;
        return ResponseFormatter::success($address->pluck('api_response'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->getValidation());
        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }
        $address = Auth::user()->addresses()->create($this->prepairData());
        $address->refresh();
        return $this->show($address->uuid);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        $address = Auth::user()->addresses()->where('uuid', $uuid)->firstOrFail();
        return ResponseFormatter::success($address->api_response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        $validator = Validator::make($request->all(), $this->getValidation());
        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $address = Auth::user()->addresses()->where('uuid', $uuid)->firstOrFail();
        $address->update($this->prepairData());
        $address->refresh();
        return $this->show($address->uuid);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $address = Auth::user()->addresses()->where('uuid', $uuid)->firstOrFail();
        $address->delete();
        return ResponseFormatter::success([
            'is_deleted' => true
        ]);
    }
    public function setDefault(string $uuid)
    {   
        $user = Auth::user();
        $address = $user->addresses()->where('uuid', $uuid)->firstOrFail();
        $address->update([
            'is_default' => true
        ]);
        $user->addresses()->where('id', '!=', $address->id)->update([
            'is_default' => false
        ]);
        return ResponseFormatter::success([
            'is_default' => true
        ]);
    }

    protected function getValidation() {
        return [
            'is_default' => 'required|in:1,0',
            'receiver_name' => 'required|min:2|max:30',
            'receiver_phone' => 'required|min:2|max:30',
            'city_uuid' => 'required|exists:cities,uuid',
            'district' => 'required|min:3|max:30',
            'postal_code' => 'required|numeric',
            'detail_address' => 'required|max:255',
            'address_note' => 'required|max:255',
            'type' => 'required|in:office,home'
        ];
    }

    public function prepairData() {
        $payload = request()->only([
            'is_default',
            'receiver_name',
            'receiver_phone',
            'city_uuid',
            'district',
            'postal_code',
            'detail_address',
            'address_note',
            'type'
        ]);
        $payload['city_id'] = City::where('uuid', $payload['city_uuid'])->firstOrFail()->id;
        if (!empty($payload['is_default']) && $payload['is_default'] == 1) {
            Auth::user()->addresses()->update([
                'is_default' => false
            ]);
        }
        return $payload;
    }

    public function getProvince(){
        $province = Province::all();
        return ResponseFormatter::success($province->pluck('api_response'));
    }
    public function getCity(){
        $query = City::query();
        if(request()->province_uuid) {
            $query = $query->whereIn('province_id', function($q) {
                $q->from('provinces')->where('uuid', request()->province_uuid)->select('id');
            });
        }

        if(request()->search)
        {
            $query = $query->where('name','LIKE', '%'.request()->search.'%');
        }
        $cities = $query->get();

        return ResponseFormatter::success($cities->pluck('api_response'));
    }
}
