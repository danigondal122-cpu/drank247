<?php

namespace App\Http\Controllers\CustomerApp;

use App\Http\Controllers\Base\BaseController;

use App\Mail\ContactUsMail;
use App\Models\ContactUs;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactUsController extends BaseController
{
  public function contactUs(Request $request)
  {
    $language = $request->input('language');
    $rules = [
      'name' => 'required',
      'email' => 'required|email',
      'contact_no' => 'required',
      'subject' => 'required',
      'message' => 'required',
    ];
    if ($request->subject == "other") {
      $rules = [
        'other_subject' => 'required',
      ];
    }
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()
        ->json([
          'status' => false,
          'message' => $validator->errors()
        ]);
    } else {
      if ($request->subject == '0') {
        $subject = 'Question about, Delivery, Price, Work, Allergies, other ..';
        $to_send = 'Customer Service';
        $adminmail = env('CUSTOMERCARE_EMAIL');
        $maildata['adminname'] = 'Customer Service';
      } else if ($request->subject == '1') {
        $subject = 'How long will it take for your order to arrive';
        $to_send = 'Customer Service';
        $adminmail = env('CUSTOMERCARE_EMAIL');
        $maildata['adminname'] = 'Customer Service';
      } else if ($request->subject == '2') {
        $subject = 'Whether the order has arrived safely';
        $to_send = 'Customer Service';
        $adminmail = env('CUSTOMERCARE_EMAIL');
        $maildata['adminname'] = 'Customer Service';
      } else if ($request->subject == '3') {
        $subject = 'Something went wrong with the payment or other system error';
        $to_send = 'Admin';
        $adminmail = env('ADMIN_EMAIL');
        $maildata['adminname'] = 'Admin';
      } else if ($request->subject == '4') {
        $subject = 'interested in franchise or delivery options';
        $to_send = 'Admin';
        $adminmail = env('ADMIN_EMAIL');
        $maildata['adminname'] = 'Admin';
      } else {
        $subject = $request->other_subject;
        $to_send = 'Customer Service';
        $adminmail = env('CUSTOMERCARE_EMAIL');
        $maildata['adminname'] = 'Customer Service';
      }
      $user = new ContactUs();
      $user->name = $request->name;
      $user->email = $request->email;
      $user->contact_no = $request->contact_no;
      $user->subject = $subject;
      $user->to_send = $to_send;
      $user->message = $request->message;
      $user->save();
      if ($user) {
        $maildata['name'] = $request->name;
        $maildata['email'] = $request->email;
        $maildata['contact_no'] = $request->contact_no;
        $maildata['subject'] = $subject;
        $maildata['messagefrom'] = $request->message;

        $adminmail=$adminmail;
        Mail::to($adminmail)
          ->send(new ContactUsMail($maildata));
      }
      $message = ($language == 'nl') ?  'Feedback succesvol verzonden'  : 'Feedback sent successfully!';
      return response()->json([
        'status' => true,
        'message' => $message,
      ]);
    }
  }
}
