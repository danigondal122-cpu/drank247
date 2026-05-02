<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\CmsPage;
use App\Models\Country;
use App\Models\Product;
use Illuminate\View\View;

class GuestPageController extends Controller
{
	public function home(): View
	{
		$data['banner'] = Banner::get();
		$data['popular_product'] = Product::where('product_type', 0)
			->where('is_show', 1)
			->where('is_popular', 1)
			->whereNull('deleted_at')
			->whereHas('category', function ($query) {
				$query->whereNull('deleted_at');
			})
			->get();
		
		return view('guest.home', $data);
	}

	public function contactUs(): View
	{
		return view('guest.contact-us');
	}

	public function privacyPolicy(): View
	{
		$page = CmsPage::wherePage('Privacy Policy')->first();

		return view('guest.cms-page', ['content' => $page->localeContent()]);
	}

	public function termsAndCondition(): View
	{
		$page = CmsPage::wherePage('Terms & Condition')->first();

		return view('guest.cms-page', ['content' => $page->localeContent()]);
	}

	public function colophone(): View
	{
		$page = CmsPage::wherePage('Colophone')->first();

		return view('guest.cms-page', ['content' => $page->localeContent()]);
	}

	public function cookieStatement(): View
	{
		$page = CmsPage::wherePage('Cookie Statement')->first();

		return view('guest.cms-page', ['content' => $page->localeContent()]);
	}

	public function alcoholLaw(): View
	{
		$page = CmsPage::wherePage('Guaranteed Working Method Alcohol Law')->first();

		return view('guest.cms-page', ['content' => $page->localeContent()]);
	}

	public function technology(): View
	{
		return view('guest.technology');
	}

	public function categoryList(): View
	{
		$data['categories'] = Category::whereNull('category_id')
			->where('is_show', 1)
			->whereNull('deleted_at')
			->orderBy('category_order', 'ASC')
			->get();

		return view('guest.category-list', $data);
	}

	public function productList(string $category): View
	{
		if ($category != 'extra_product')
		{
			$category = str_replace('_',' ',$category);
			$data['category'] = Category::where('category_name',$category)->first();
			$data['products'] = $data['category'] ? Product::where('product_type', 0)
				->where('is_show', 1)
				->whereNull('deleted_at')
				->where('category_id', $data['category']->id)
				->orderBy('product_order', 'ASC')
				->get() : null;
		}
		else
		{
			$data['products'] = Product::where('product_type', 1)
				->where('is_show', 1)
				->whereNull('deleted_at')
				->orderBy('product_order', 'ASC')
				->get();
		}

		return view('guest.product-list', $data);
	}

	public function cart(): View
	{
		$customer = auth('customer')->check() ? auth('customer')->user() : null;
		$contact = explode('-',$customer?->customer_contact_no);
		$data = [
			'address' => $customer?->address()->whereDefault()->first() ?? null,
			'country_code' => $contact[0] ?? null,
			'contact_no' => isset($contact[1]) ? $contact[1] : $customer?->customer_contact_no,
			'countries' => Country::all()
		];
	
		return view('guest.cart', $data);
	}
}