<?php

namespace App\Http\Controllers\CustomerApp;

use App\Http\Controllers\Controller;

use App\Mail\ForgotPasswordForDelivery;

use App\Models\Customer;
use App\Models\Pool;
use App\Models\PromoCode;
use App\Models\UsedPromoCode;
use App\Models\Franchise;
use App\Models\DeliveryTimeSchedule;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
  public function login(Request $request)
  {

    $email = $request->input('email');
    $password = $request->input('password');
    $device = $request->input('device');
    $device_token = $request->input('device_token');
    $language = $request->input('language');

    $detail = Customer::where('customer_email', $email)->whereNull('deleted_at')->first();
    if ($detail) {
      if (!(Hash::check($password, $detail->password))) {
        $message = ($language == 'nl') ?  'incorrect Password'  : 'incorrect Password';
        return response()->json(['status' => false, 'message' => $message]);
      } else {
        $customer_hash = time();
        Customer::where('customer_email', $email)->update(['customer_devicetoken' => $device_token, 'customer_device' => $device, 'customer_hash' => $customer_hash]);
        $detail = Customer::where('customer_email', $email)->whereNull('deleted_at')->first(['customer_id', 'customer_name', 'customer_email', 'customer_type', 'customer_phone', 'password', 'profile', 'customer_address', 'customer_devicetoken', 'customer_device', 'customer_hash']);
        $detail['customer_address'] = $detail['customer_address'] == null ? "" : $detail['customer_address'];
        $message = ($language == 'nl') ?  'Succesvol inloggen'  : 'Login Successfully!!';
        return response()->json(['status' => true, 'message' => $message, 'data' => $detail]);
      }
    } else {
      $message = ($language == 'nl') ?  'Email bestaat al'  : 'Email does not exist';
      return response()->json(['status' => false, 'message' => $message]);
    }
  }
  public function forgotPassword(Request $request)
  {
    $email = $request->input('email');
    $language = $request->input('language');
    $checkmail = Customer::where('customer_email', $email)->whereNull('deleted_at')->first();

    if ($checkmail) {
      $token = Str::random(6);

      $checkmail->password = Hash::make(($token));
      $checkmail->save();
      $maildata = [];
      $maildata['name'] = $checkmail['customer_name'];
      $maildata['email'] = $email;
      $maildata['password'] = $token;
      Mail::to($email)
        ->send(new ForgotPasswordForDelivery($maildata));

      $message = ($language == 'nl') ?  'Wachtwoord verzonden naar uw e-mail'  : 'Password sent to your Email';
      return response()
        ->json(['status' => true, 'message' => $message]);
    } else {
      $message = ($language == 'nl') ?  'Email bestaat al'  : 'Email does not exist';
      return response()->json(['status' => false, 'message' => $message]);
    }
  }
  public function logout(Request $request)
  {
    $id = $request->input('id');
    $language = $request->input('language');
    Customer::where('customer_id', $id)->update(['customer_devicetoken' => ""]);
    $message = ($language == 'nl') ?  'Uitloggen succesvol'  : 'Logout Successfully';
    return response()->json(['status' => true, 'message' => $message]);
  }
  public function checkPostCode(Request $request)
  {
    $language = $request->input('language');
    $rules = [
      'postcode' => 'required',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      $message=($language== 'nl') ?  'Voer a.u.b. postcode in'  : 'Please enter PostCode' ;
      return response()
        ->json([
          'status' => false,
          'type' => 'validation',
          'message' => $message
        ]);
    } else {
      $postcode = $request->input('postcode');
      $assign_pool=Franchise::select('franchise_pool')->where('fs_on_off','online')->whereNull('deleted_at')->get();

      $ass_pools = [];
      foreach($assign_pool as $key=>$value){
          $value=explode(',',$value['franchise_pool']);
          $ass_pools[]=$value;
      }
      $total_ass = call_user_func_array("array_merge", $ass_pools);
      // $total_ass=implode(',',$total_ass);

      $pool = Pool::whereIN('pool_id',$total_ass)->whereNull('deleted_at')->get();
      $array = [];

      foreach ($pool as $value) {
        $code = preg_replace('/[^0-9.]+/', '', $postcode);
        if ($code >= $value->from_postcode && $code <= $value->to_postcode) {
          $array[] = $value['pool_id'];
        }
      }

      $count = count($array);

      $query = Pool::whereIN('pool_id',[$total_ass])->whereNull('deleted_at');

      $column_search = ['area'];
      $rawQuery = '';
      $search = $request->input('postcode');
      $i = 0;
      foreach ($column_search as $key => $value) {
        // dd($value);
        if ($i === 0) // first loop
        {
          $rawQuery .= '('; // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
          $rawQuery .= $value . ' LIKE "%' . $search . '%"';
        } else {
          $rawQuery .= ' OR ' . $value . ' LIKE "%' . $search . '%"';
        }
        if (count($column_search) - 1 == $i) {
          //last loop
          $rawQuery .= ')'; //close bracket
        }
        $i++;
      }
      $query = $query->whereRaw($rawQuery);

      $total = $query->get()->count();
      if ($count == 0 && $total == 0) {
        $message = ($language == 'nl') ?  'wij bewijzen onze service niet op uw postcodegebied. Bel de klantenservice voor meer informatie.'  : 'we do not provie our service on your postcode area. Please call customer service for more details.';
        return response()
          ->json([
            'status' => false,
            'type' => 'invalidPostcode',
            'message' => $message,
          ]);
      } else {
        $message = ($language == 'nl') ?  'Wij bezorgen op jouw postcode!'  : 'We deliver at your postcode !';

        return response()
          ->json([
            'status' => true,
            'type' => 'success',
            'message' => $message,
          ]);
      }
    }
  }
  public function checkPromoCode(Request $request)
  {
    $id = $request->input('id');
    $token = $request->input('token');
    $total = $request->input('total');
    $promocode = $request->input('promocode');
    $language = $request->input('language');

    $rules = [
      'promocode' => 'required',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      $message = ($language == 'nl') ?  'Voer a.u.b. PromoCode in'  : 'Please enter PromoCode';

      return response()
        ->json([
          'status' => false,
          'type' => 'validation',
          'errors' => $message
        ]);
    } else {
      $data['discount'] = '';
      $data['final_ordertotal'] = '';
      $data['promo_code'] = '';

      $date = (Carbon::now())->toDateString();
      $promocode = $request->input('promocode');

      $code_detail = PromoCode::where('code_text', "$promocode")->where('code_status', '1')->first();
      if ($code_detail) {
        if ($code_detail['expiration_type'] == "0") {

          $isExit = PromoCode::where('code_text', "$promocode")->where('code_status', '1')->where('start_date', '<=', $date)->first();
        }
        if ($code_detail['expiration_type'] == "1") {
          $isExit = PromoCode::where('code_text', "$promocode")->where('code_status', '1')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)->first();
        }
        if ($isExit) {
          $customer_id = $id;
          $code_id = $code_detail['code_id'];
          $discount_type = $code_detail['discount_type'];
          $discount = $code_detail['discount'];
          $limitation_type = $code_detail['limitation_type'];
          $max_users = $code_detail['max_users'];
          $max_peruser = $code_detail['max_peruser'];
          $expiration_type = $code_detail['expiration_type'];
          $start_date = $code_detail['start_date'];
          $end_date = $code_detail['end_date'];
          $code_status = $code_detail['code_status'];

          $custom_used = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id', $code_id)->get()->count();

          if ($custom_used) {
            $custuser = UsedPromoCode::where('c_id', $customer_id)->where('pcode_id', $code_id)->first();
            $customcount = $custuser['used_count'];
          } else {
            $customcount = 0;
          }
          $usedlimatation = UsedPromoCode::where('pcode_id', $code_id)->get()->sum('used_count');

          if ($code_detail['expiration_type'] == "0") {
            if ($customcount < $max_peruser) {
              if ($discount_type == 0) {
                $discount = $discount;
              } else {
                $discount = str_replace(',', '', $total) * $discount / 100;
              }
              if(str_replace(',', '', $total)>$discount){
                $data['discount'] = number_format($discount, 2);
                $data['final_ordertotal'] = number_format(str_replace(',', '', $total) - $discount, 2);
                $data['promo_code'] = "$code_id";
                $message = ($language == 'nl') ?  'Promotiecode succesvol toegepast!'  : 'Promo code applied successfully!';
                return response()
                  ->json([
                    'status' => true,
                    'message' => $message,
                    'data' => $data,
                  ]);
              }else{
                $data['discount'] ='0.00';
                $data['final_ordertotal'] = $total;
                $data['promo_code'] = "$code_id";
                $message = ($language == 'nl') ?  "Final Price should be greater than Discount ($discount)" :  "Final Price should be greater than Discount ($discount)";
                return response()
                ->json([
                  'status' => false,
                  'message' => $message,
                  'data' => $data,
                ]);
              }

            } else {

              $message = ($language == 'nl') ?  'Voer een geldige promotiecode in!'  : 'Please enter valid Promo code!';
              return response()
                ->json([
                  'status' => false,
                  'message' => $message,
                  'data' => $data,
                ]);
            }
          }
          if ($code_detail['expiration_type'] == "1") {

            if ($customcount < $max_peruser && $usedlimatation < $max_users) {
              if ($discount_type == 0) {
                $discount = $discount;
              } else {
                $discount = str_replace(',', '', $total) * $discount / 100;
              }
              if(str_replace(',', '', $total)>$data['discount']){
                $data['discount'] = number_format($discount, 2);
                $data['final_ordertotal'] = number_format(str_replace(',', '', $total) - $data['discount'], 2);
                $data['promo_code'] = $code_id;
                $message = ($language == 'nl') ?  'Promotiecode succesvol toegepast!'  : 'Promo code applied successfully!';
                return response()
                  ->json([
                    'status' => true,
                    'message' => $message,
                    'data' => $data,

                  ]);
              }else{
                $data['discount'] ='0.00';
                $data['final_ordertotal'] = $total;
                $data['promo_code'] = "$code_id";
                $message = ($language == 'nl') ?  "Final Price should be greater than Discount ($discount)" :  "Final Price should be greater than Discount ($discount)";
                return response()
                ->json([
                  'status' => false,
                  'message' => $message,
                  'data' => $data,
                ]);
              }
            } else {
              $message = ($language == 'nl') ?  'Voer een geldige promotiecode in!'  : 'Please enter valid Promo code!';
              return response()
                ->json([
                  'status' => false,
                  'message' => $message,
                  'data' => $data,
                ]);
            }
          }
        } else {
          $message = ($language == 'nl') ?  'Voer een geldige promotiecode in!'  : 'Please enter valid Promo code!';
          return response()
            ->json([
              'status' => false,
              'message' => $message,
              'data' => $data,
            ]);
        }
      } else {
        $message = ($language == 'nl') ?  'Voer een geldige promotiecode in!'  : 'Please enter valid Promo code!';
        return response()
          ->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
          ]);
      }
    }
  }
  public function getDeliveryTimeList(Request $request){
    $language = $request->input('language');
    $list=DeliveryTimeSchedule::get()->all();
    $data=[];
    $eng_array=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    $dutch_array=['Zondag','Maandag','Dinsdag','Woensdag','Donderdag','Vrijdag','Zaterdag'];
    foreach($list as $key=>$value){
      if($language=='nl'){
        $data[$key]['day']=$dutch_array[$key];
      }else{
        $data[$key]['day']=$eng_array[$key];
      }


      $data[$key]['start_time']=$value['start_time_0'].':'.$value['start_time_1'];
      $data[$key]['end_time']=$value['end_time_0'].':'.$value['end_time_1'];
    }

    return response()
    ->json([
      'status' => true,
      'message' => '',
      'data' => $data,
    ]);
  }
}
