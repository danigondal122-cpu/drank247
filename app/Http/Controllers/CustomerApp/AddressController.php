<?php

namespace App\Http\Controllers\CustomerApp;

use App\Http\Controllers\Base\BaseController as BaseBaseController;

use App\Models\CustomerAddress;
use App\Models\Pool;
use App\Models\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends BaseBaseController
{
  public function addressList(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');

    $data['address'] = CustomerAddress::whereNull('deleted_at')->Where('customer_id', $id)->orderBy('default', 'DESC')->get(['address_id', 'address', 'post_code', 'default', 'manual', 'house_no']);


    return response()->json([
      'status' => true,
      'data' => $data,
    ]);
  }

  public function addUpdateAddress(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $address_id = $request->input('address_id');
    $houseno = $request->input('houseno');
    $postcode = $request->input('postcode');
    $language = $request->input('language');


    $rules = [
      'houseno' => 'required',
      'postcode' => 'required',
      // 'postcode' => 'regex:"[0-9]{4}[A-Z]{2}"',
    ];
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()
        ->json([
          'status' => false,
          'type' => 'VALIDATION',
          'errors' => $validator->errors()
        ]);
    } else {
      $is_update = false;
      $postData = $request->all();
      $pool = Pool::whereNull('deleted_at')->get();
      $array = [];

      foreach ($pool as $value) {
        $code = preg_replace('/[^0-9.]+/', '', $postcode);
        if ($code >= $value->from_postcode && $code <= $value->to_postcode) {
          $array[] = $value['pool_id'];
        }
      }
      if (count($array) > 0) {
        $addressdata = $this->getAddressDetails(trim(str_replace(' ', '', $postData['postcode'])), $postData['houseno']);

        if (isset($addressdata['city']) && isset($addressdata['street']) && trim($addressdata['city']) != "" && trim($addressdata['city']) != "") {
          $fulladdress = $postData['houseno'] . ', ' . $addressdata['street'] . ', ' . $addressdata['city'] . ', ' . $addressdata['province'];
          //check if first address
          $checkfirst = CustomerAddress::where("customer_id", $id)->get()->count();

          if ($address_id) {
            $customer = CustomerAddress::find($address_id);
            $message = ($language == 'nl') ?  'Adres succesvol bijgewerkt'  : 'Address Updated successfully';
            $is_update = true;
          } else {
            $customer = new CustomerAddress();
            $customer->default = ($checkfirst == 0) ? '1' : '0';
            $message = ($language == 'nl') ?  'Adres succesvol toegevoegd'  : 'Address Added successfully';
          }
          $customer->customer_id = $id;
          $customer->address = $fulladdress;
          $customer->post_code = $addressdata['postcode'];
          $customer->latitude = $addressdata["geo"]["lat"];
          $customer->longitude = $addressdata["geo"]["lon"];
          $customer->house_no = $addressdata['number'];
          $customer->save();
          $address = CustomerAddress::select(['address_id', 'address', 'post_code', 'default', 'manual', 'house_no'])->Where('address_id', $customer->address_id)->first();

          return response()
            ->json([
              'status' => true,
              'message' => $message,
              'data' => $address,
            ]);
        } else {
          $address['address_id'] = 0;
          $address['address'] = '';
          $address['post_code'] = '';
          $address['default'] = 0;
          $address['manual'] = 0;
          $address['house_no'] = '';

          $message = ($language == 'nl') ?  'We konden uw adres niet ophalen, probeer het opnieuw met de juiste gegevens of voer het handmatig in'  : "We couldn't fetch your address, please try again with correct details or enter it manually";
          return response()
            ->json([
              'status' => false,
              'message' => $message,
              'data' => $address,
            ]);
        }
      } else {
        $address['address_id'] = 0;
        $address['address'] = '';
        $address['post_code'] = '';
        $address['default'] = 0;
        $address['manual'] = 0;
        $address['house_no'] = '';
        $message = ($language == 'nl') ?  'Helaas bezorgen wij nog niet in uw omgeving. Neem contact op met de klantenservice'  : 'We do not provide our service on your postcode area. Please call customer service for more details.';

        return response()
          ->json([
            'status' => false,
            'message' => $message,
            'data' =>  $address,
          ]);
      }
      $message = ($language == 'nl') ?  'Something went wrong!'  : 'Something went wrong!';
      return response()->json([
        'status' => false,
        'type' => 'SYSTEM',
        'message' => $message,
        'data' => [],
      ]);
    }
  }
  public function addManualAddress(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $houseno = $request->input('houseno');
    $postcode = $request->input('postcode');
    $street = $request->input('street');
    $city = $request->input('city');
    $state = $request->input('state');
    $language = $request->input('language');

    $rules = [
      'houseno' => 'required',
      'postcode' => 'required',
      'street' => 'required',
      'city' => 'required',
      'state' => 'required',

    ];
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()
        ->json([
          'status' => false,
          'type' => 'VALIDATION',
          'errors' => $validator->errors()
        ]);
    } else {
      $fulladdress = $request->houseno . ', ' . $request->street . ', ' . $request->city . ', ' . $request->state;
      $checkfirst = CustomerAddress::where("customer_id", $id)->get()->count();
      $customer = new CustomerAddress();
      $customer->default = ($checkfirst == 0) ? '1' : '0';
      $customer->customer_id = $id;
      $customer->address = $fulladdress;
      $customer->post_code = $request->postcode;
      $customer->house_no = $request->houseno;
      $customer->manual = '1';
      $customer->save();
      $message = ($language == 'nl') ?  'Handmatig adres succesvol toegevoegd'  : 'Manually Address Added successfully';
      $address = CustomerAddress::select(['address_id', 'address', 'post_code', 'default', 'manual', 'house_no'])->Where('address_id', $customer->address_id)->first();
      return response()
        ->json([
          'status' => true,
          'message' => $message,
          'data' => $address,

        ]);
    }
  }
  public function deleteAddress(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $address_id = $request->input('address_id');
    $language = $request->input('language');

    $rules = [
      'address_id' => 'required',
    ];
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()
        ->json([
          'status' => false,
          'type' => 'VALIDATION',
          'errors' => $validator->errors()
        ]);
    } else {

      $address_details = CustomerAddress::find($address_id);
      $defaultcount = CustomerAddress::where('customer_id', $id)->where('default', '1')->get()->count();
      CustomerAddress::where('address_id', $address_id)->delete();
      if ($address_details->default == '1') {
        CustomerAddress::where('customer_id', $id)->orderBy('address_id', 'DESC')->take(1)->update(['default' => '1']);
      }

      $message = ($language == 'nl') ?  'Adres verwijderd'  : 'Address deleted';
      return response()
        ->json([
          'status' => true,
          'message' => $message,
        ]);
    }
  }
  public function setDefaultAddress(Request $request)
  {
    $token = $request->input('token');
    $id = $request->input('id');
    $address_id = $request->input('address_id');
    $language = $request->input('language');

    $rules = [
      'address_id' => 'required',
    ];
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()
        ->json([
          'status' => false,
          'type' => 'VALIDATION',
          'errors' => $validator->errors()
        ]);
    } else {
      CustomerAddress::where('customer_id', $id)->whereNull('deleted_at')->update(['default' => '0']);
      Customer::where('customer_id', $id)->whereNull('deleted_at')->update(['customer_address' => $address_id]);

      $address = CustomerAddress::find($address_id);
      $address->default = '1';
      $address->save();

      $message = ($language == 'nl') ?  'Standaardadres instellen'  : 'Set Default Address';

      return response()
        ->json([
          'status' => true,
          'message' => $message,
        ]);
    }
  }
}
