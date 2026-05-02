<footer>


  <div class="d-block">
    <div class="container-fluid px-5 py-5" style="background:#fff;">
      <div class="">
        <div class="col-md-12 col-sm-12 col-lg-12">
          <div class="footer-data">
            <div class="row justify-content-center px-5">

              <div class="col-md-10">
                <div class="row">
              {{-- <div class="col-md-6 col-sm-6 col-lg-3">
                <div class="widget about_widget wow fadeIn">
                  <div class="logo" style="margin-top:20px;">
                    <h1 itemprop="headline"><a href="#" title="Home" itemprop="url">
                        <img src="{{ asset('img/247-Drank-Logo.png') }}" width="130" alt="logo.png"
                          itemprop="image"></a></h1>
                  </div>
                  <div>
                  <p itemprop="description" style="margin-top:10px;font-size:15px;">247DRANK Nu Online Bier, Wijn en Sterke dranken bestellen en Direct laten leveren!</p>
                  </div>

                </div>
              </div> --}}

              <div class="col-md-6 col-sm-6 col-lg-4">
                <div class="widget information_links wow fadeIn footer_ul_div">
                  <h4 class="widget-title" itemprop="headline">Information</h4>
                  <ul style="margin-top: -22px;">
                    <li><a href="{{url('/privacy_policy')}}" title="" itemprop="url">{{ __('messages.privacypolicy') }}</a></li>
                    <li><a href="{{url('/terms_condition')}}" title="" itemprop="url">{{ __('messages.termsandcondition') }}</a></li>
                    <li><a href="{{url('/colophone')}}" title="" itemprop="url">{{ __('messages.colophone') }}</a></li>
                    <li><a href="{{url('/cookiestatement')}}" title="" itemprop="url">{{ __('messages.cookiestatement') }}</a></li>
                    <li><a href="{{url('/alcohol_law')}}" title="" itemprop="url">{{ __('messages.alcohollaw') }}</a></li>
                    {{-- <li><a href="{{url('/technology')}}" title="" itemprop="url">{{ __('messages.technology') }}</a></li> --}}
                  </ul>
                </div>
                <div class="widget customer_care wow fadeIn footer_ul_div">
                  <h4 class="widget-title" itemprop="headline">{{ __('messages.customercare') }}</h4>
                  <ul style="margin-top: -22px;">
                    <li><a href="{{url('/contact_us')}}" title="" itemprop="url">{{ __('messages.contactus') }}</a></li>
                    {{-- <li><a href="#" title="" itemprop="url">Shipping Info</a></li>
                    <li><a href="#" title="" itemprop="url">Gift Cards</a></li>
                    <li><a href="#" title="" itemprop="url">Size Guide</a></li> --}}
                  </ul>
                </div>
              </div>
              @if(($global['settings']['time_schedule'])=="1")
              <div class="col-md-6 col-sm-6 col-lg-4">
                <div class="widget information_links wow fadeIn footer_ul_div">
                  <h4 class="widget-title" itemprop="headline">{{ __('messages.deliverytime') }}</h4>
                  @foreach($global['dayschedule'] as $key=>$value)
                    @if($value['is_checked']=="1")
                    <div class="row">
                      <div class="col-md-6 col-6" style="color:#e91362;line-height:30px;">{{$value['day']}}</div>
                      <div class="col-md-6 col-6" style="line-height:30px;">{{$value['start_time_0'].':'.$value['start_time_1'].'-'.$value['end_time_0'].':'.$value['end_time_1']}}</div>
                    </div>
                    @endif
                    @endforeach

                </div>
              </div>
              @endif
              <div class="col-md-6 col-sm-6 col-lg-4">
              <div class="row">
              <div class="col-12">
                <div class="widget get_in_touch wow fadeIn footer_ul_div">
                  <h4 class="widget-title" itemprop="headline">{{ __('messages.getintouch') }}</h4>
                  <ul>
                    <li>
                      <a>
                      <span> <img src="{{ asset('images/side_menu/map.svg') }}"  style="width: 1.4rem;margin-right: .5rem;"></span>
                      <span>{{$global['settings']['address']}} </span>  </a>
                     </li>
                    <li>
                      <a href="tel:{{$global['settings']['contact_no']}}">
                      <span><img src="{{ asset('images/side_menu/call.svg') }}"  style="width: 1.4rem;margin-right: .5rem;"></span>
                      <span>{{$global['settings']['contact_no']}}</span> </a>
                    </li>
                      @if($global['settings']['email_show']=='1')
                    <li>
                      <a>
                      <span><img src="{{ asset('images/side_menu/email.svg') }}"  style="width: 1.4rem;margin-right: .5rem;"></span>
                      <span>{{$global['settings']['email']}}</span>
                      </a> </li>
                      @endif
                  </ul>
                </div>
              </div>
              <div class="col-12 mt-3">
                  <a>
                    <img src="{{ asset('images/side_menu/facebook.svg') }}"  style="width: 1.8rem;margin-right: .5rem;">
                  </a>
                  <a>
                    <img src="{{ asset('images/side_menu/youtube.svg') }}"  style="width: 1.8rem;margin-right: .5rem;">
                  </a>
                  <a>
                    <img src="{{ asset('images/side_menu/instagram.svg') }}"  style="width: 1.8rem;margin-right: .5rem;">
                  </a>
                  {{-- <a>
                    <img src="{{ asset('images/side_menu/google plus.svg') }}"  style="width: 1.8rem;margin-right: .5rem;">
                  </a> --}}
                  <a>
                    <img src="{{ asset('images/side_menu/teligram.svg') }}"  style="width: 1.8rem;margin-right: .5rem;">
                  </a>
                  <a>
                    <img src="{{ asset('images/side_menu/whatsapp.svg') }}"  style="width: 1.8rem;margin-right: .5rem;">
                  </a>
                  <a>
                    <img src="{{ asset('images/side_menu/tiktok.svg') }}"  style="width: 1.8rem;margin-right: .5rem;">
                  </a>

                </div>
                <div class="col-12 mt-3">
                  <a>
                    <img src="{{ asset('images/side_menu/logo_nix_18.png') }}"  style="width: 11.8rem;">
                  </a>
                </div>
              </div>
              </div>
            </div>
           </div>


          </div>
          <!-- Footer Data -->
        </div>
      </div>
    </div>
  </div>

</footer>
<div class="dark-bg text-center bg-primary footer_button">
  <div class="container">
    <p class="m-0 p-2">&copy; <?= date('Y') ?> <a class="red-clr" href="javascript:;"></a>. All Rights Reserved</p>
  </div>
</div>
@if(Request::segment(1)!='cart')
<div id="CartButton" class="btn btn-primary mt-2">
  <a href='{{url('/cart')}}'>
  <i class="fas fa-cart-arrow-down" style="font-size: 1.3rem"></i>
  <span class="badge  navbar-badge" id="cart_total_item_footer">@auth{{$global['cart_item_total']}}@else{{ Cart::content()->count() }}@endauth</span>
  &nbsp;&nbsp;Winkelmandje (<span id="final_amount_footer">€&nbsp;{{$global['final_amount_footer']}}</span>)
  </a>
</div>
@endif

