<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CustomerController extends Controller
{
	public function profile(): mixed
	{
		$customer = auth('customer')->user();
		$contact = explode('-', $customer?->customer_contact_no);
		$data = [
			'customer' => $customer,
			'order' => $customer?->orders()
				->orderBy('id', 'desc')
				->get(),
			'countries' => Country::all()
		];

		if (isset($contact[1])) {
			$data['customer']->country_code = $contact[0] ?? null;
			$data['customer']->contact = $contact[1];
		}

		return view('customer.profile', $data);
	}

	public function favourite(): View
	{
		return view('customer.favourite', [
			'favourites' => auth('customer')
				->user()
				?->favourites()
				->with('product')
				->orderBy('id', 'desc')
				->get()
		]);
	}

	public function ageValidation()
	{
		return view('frontend.age-validation');
	}

	public function validateAge(Request $request)
	{
		$rules = [
			'date' => 'required',
			'month' => 'required',
			'year' => 'required',
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
			$date = $request['date'];
			$month = $request['month'];
			$year = $request['year'];

			$birthDate = $year . '-' . $month . '-' . $date;

			$years = Carbon::parse($birthDate)->age;

			if ($years > 18) {

				return response()->json([
					'status' => true,
					'msg' => 'Success !'
				]);
			} else {
				return response()->json([
					'status' => false,
					'type' => 'LESS_AGE',
					'msg' => 'You must be at least 18 years of age to enter this website.'
				]);
			}
		}
	}

	public function adminAdd()
	{
		$data['row'] = [];
		return view('admin.customer.create', $data);
	}

	public function adminEdit($id)
	{
		$data['row'] = [];
		if ($id) {
			$data['row'] = Customer::findOrFail($id);
		}
		return view('admin.customer.create', $data);
	}

	public function adminIndex()
	{
		return view('admin.customer.list');
	}

	public function getList(Request $request)
	{
		// Initialize the query with Eloquent
		$query = Customer::with('address') // Assuming you have a relationship defined in the Customer model
			->whereNull('customers.deleted_at');

		// Define columns for ordering and searching
		$column_order = ['customer_name', 'customer_email', 'customer_contact_no', 'address.address']; // Updated to match the correct relationship
		$column_search = ['customer_name', 'customer_email', 'customer_contact_no', 'address.address']; // Updated to match the correct relationship

		// Get pagination parameters with defaults
		$start_from = $request->start ?? 0; // Default to 0 if not set
		$per_page = $request->length ?? 10; // Default to 10 if not set

		// Search functionality
		if ($request->has('search') && !empty($request->search['value'])) {
			$search = $request->search['value'];
			$query->where(function ($q) use ($search, $column_search) {
				foreach ($column_search as $column) {
					$q->orWhere($column, 'LIKE', "%$search%");
				}
			});
		}

		// Sorting functionality
		if ($request->has('order') && isset($request->order[0]['column'])) {
			$columnIndex = $request->order[0]['column'];
			if (isset($column_order[$columnIndex])) {
				$query->orderBy($column_order[$columnIndex], $request->order[0]['dir'] ?? 'asc');
			}
		} else {
			$query->orderBy('customers.id', 'DESC'); // Specify the table for the id column
		}

		// Get total count and paginated data
		$total = $query->count(); // Get total count
		$data = $query->skip($start_from)->take($per_page)->get(); // Get paginated data

		return response()->json([
			'data' => $data,
			'total' => $total,
		]);
	}

	public function deleteCustomer(Request $request)
	{
		$rules = [
			'id' => 'required',
		];
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return response()
				->json([
					'status' => false,
					'type' => 'validation',
					'errors' => $validator->errors()
				]);
		} else {
			Customer::where('id', $request->id)->delete();
			return response()
				->json([
					'status' => true,
					'msg' => 'Customer deleted !',
				]);
		}
	}
}
