@extends('layouts.user')

@push('extraStyle')
	<style>
		.card {
			border-radius: .25rem;
		}

		table tbody tr th{
			background-color: #f2f2f2;
			font: bold !important;  
			font-size: 16px;
		}
	</style>
@endpush

@section('content')
	<x-user.content>
		<div class="row">
			<div class="col-md-8 mx-auto">
				<div class="card">
					<div class="card-body table-responsive">
						<h1 class="underline" style="display: inline-block;">Technology list</h1>
						<p>In this list you will find all the cookies used on our Takeaway.com websites. These will be sorted by the purposes mentioned in the cookie statement </p>
						<h1 class="underline mt-3" style="display: inline-block;">Functional Technologies</h1>
						<p>We use different Technologies to ensure that our website functions well and is easy to use. The following Technologies are used for functional purposes: </p>
						
						<table id="table" class="table table-bordered table-hover mt-5" style="border: 1px solid #dee2e6;" >
							<tbody>
								<tr>
									<th scope="col">Name supplier (name technology)</th>
									<th scope="col">Purpose of technology</th>
									<th scope="col">Shared with third parties</th>
									<th scope="col">Lifetime of technology</th>
								</tr>
								<tr>
									<td><strong>Takeaway.com</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>PHPSESSID</td>
									<td>SessionID of user, necessary for PHP and set by server.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after session</td>
								</tr>
								<tr>
									<td>geolocator</td>
									<td>lat lng detection.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after session</td>
								</tr>
								<tr>
									<td>entrance</td>
									<td>Load balancer cookie.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 10 days.</td>
								</tr>
								<tr>
									<td>iv</td>
									<td>remember user credentials (iv + verify).</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 3 years 2 months and 3 days.</td>
								</tr>
								<tr>
									<td>postcode</td>
									<td>postcode entered by user.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 3 years 2 months and 3 days.</td>
								</tr>
								<tr>
									<td>privacybanner</td>
									<td>Cookiestatement accepted or not (legacy).</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 1 year.</td>
								</tr>
								<tr>
									<td>cookieConsent</td>
									<td>Cookiestatement accepted or not.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 3 months.</td>
								</tr>
								<tr>
									<td>savedAddressId</td>
									<td>Used for user order experience, referencing stored saved addresses</td>
									<td>No</td>
									<td>Gets deleted after 1 year</td>
								</tr>
								<tr>
									<td>orderRemarks</td>
									<td>Stores remarks that the user providing during a previous order</td>
									<td>No</td>
									<td>Gets deleted after 4 years</td>
								</tr>
								<tr>
									<td>searchstring</td>
									<td>searchstring entered by user, could also be a postcode.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 1 year.</td>
								</tr>
								<tr>
									<td>sortby</td>
									<td>sort preference.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>verify</td>
									<td>remember user credentials (iv + verify).</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 3 years 2 months and 3 days.</td>
								</tr>
								<tr>
									<td>UseLanguage</td>
									<td>Language which is selected by user.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 1 year.</td>
								</tr>
								<tr>
									<td>devicebanner</td>
									<td>show / hide appbanner for mobile devices.</td>
									<td>Not shared with third parties.</td>
									<td>Session based</td>
								</tr>
								<tr>
									<td>lastChosenPaymentDetails</td>
									<td>to pre-select payment method and bank choice which was last used by the customer.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 3 months.</td>
								</tr>
								<tr>
									<td>address_saved</td>
									<td>to pre-select address for the location panel.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 1 year.</td>
								</tr>
								<tr>
									<td>address_geo</td>
									<td>Store address geo location.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>AID</td>
									<td>For usage of back button on Menucard.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>i18next</td>
									<td>Used to detect browser language.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>isLogged</td>
									<td>Checking for user logged in or not.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 3 years 2 months and 3 days.</td>
								</tr>
								<tr>
									<td>latitude, longitude</td>
									<td>Setting cookie for saving last search.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>pickup</td>
									<td>Updating pickup status.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>preferredLanguage</td>
									<td>Registering user prefered language.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>S_*</td>
									<td>Used to randomize the hero images, so they stay the same over the session.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>searchstringmapper</td>
									<td>Store location with lat/lng information for finding geo location.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 60 seconds.</td>
								</tr>
								<tr>
									<td>NID</td>
									<td>
										This cookie is set using Google reCAPTCHA v3 to help protect the Takeaway Pay admin site from
										attacks
										(by “bots” and other malicious software) and making sure users are not abusing it by trying to enter
										wrong credentials many times.
									</td>
									<td>
										The reCAPTCHA works by collecting hardware and software information and sending these data to Google
										for analysis.
									</td>
									<td>Lifetime: 6 months</td>
								</tr>
								<tr>
									<td>activeAddress</td>
									<td>Used to store extended information about currently selected customer address.</td>
									<td>Not shared with third parties.</td>
									<td>Lifetime: 4 years</td>
								</tr>
								<tr>
									<td>jwt</td>
									<td>Api access token</td>
									<td>Not shared with third parties.</td>
									<td>Lifetime: 2 months</td>
								</tr>
								<tr>
									<td>refreshToken</td>
									<td>Token to refresh the access token</td>
									<td>Not shared with third parties.</td>
									<td>Lifetime: 2 months</td>
								</tr>
								<tr>
									<td>visitedUrls</td>
									<td>Used by the application to see how many unique urls have been visited</td>
									<td>Not shared with third parties.</td>
									<td>Lifetime: session</td>
								</tr>
								<tr>
									<td>BTV_{UniqueID}</td>
									<td>used by the application to create an AB test</td>
									<td>Not shared with third parties.</td>
									<td>Lifetime: 4 months</td>
								</tr>
								<tr>
									<td>cwOrderTrackingId</td>
									<td>Used for order tracking deduplication</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td><strong>Tealium</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>Tealium_privacybanner</td>
									<td>Cookiestatement accepted or not</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 1 year.</td>
								</tr>
								<tr>
									<td>Utag_main</td>
									<td>cookie set by Tealium to enable us to use their tag manager solution.The utag_main cookie is a
										1st-party
										cookie provided by Tealium. This cookie is used to record a timestamp of when you visit to our site
										has
										started, the number of pages you have viewed, the number of visits you have had to our site and a
										unique
										ID.This information is used within our analytics tools to enrich the data we’re collecting about
										your
										visit on the website to help us understand our visitors usage of the site to provide a better user
										experience.
									</td>
									<td>Is shared with Tealium that only uses this for reporting to Takeaway.com.</td>
									<td>Gets deleted after 1 year.</td>
								</tr>
								<tr>
									<td>wamlastorder</td>
									<td>Stores the decrypted orderID of the last transaction to prevent duplication tracking.
									</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 3 months.</td>
								</tr>
								<tr>
									<td><strong>Incapsula</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>visid_incap_*, nlbi_*, incap_ses_*</td>
									<td>Incapsula DDoS Protection</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 12 months.</td>
								</tr>
							</tbody>
						</table>
						
						<h1 class="underline" style="display: inline-block;">Analytical Technologies</h1>
						<p>
						We analyze your behavior on our website, using cookies and trackers, to improve our website and to adapt it to
						your wishes. We hope that by analyzing this data our website is as user friendly as possible. The following
						Technologies are used for analytical purposes:
						</p>
						
						<table id="table" class="table table-bordered table-hover mt-5" style="border: 1px solid #dee2e6;" >
							<caption class="hidden">Analytical Technologies</caption>
							<tbody>
								<tr>
									<th scope="col">Name supplier (name technology)</th>
									<th scope="col">Purpose of technology</th>
									<th scope="col">Shared with third parties</th>
									<th scope="col">Lifetime of technology</th>
								</tr>
								<tr>
									<td><strong>Takeaway.com</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>Awin_cookie, zanpid &amp; _aw_m_10510</td>
									<td>Cookie gets placed when utm_source=awin appears in URL to recognize if a visitor is coming from
										related publisher sites and to activate the Awin pixel on the thank you page.
									</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 10 days.</td>
								</tr>
								<tr>
									<td>aff_biscuit_cp</td>
									<td>Cookie gets placed when visitor is hitting the credential page.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 1 hour.</td>
								</tr>
								<tr>
									<td>aff_biscuit_coup</td>
									<td>Cookie gets placed when visitor is coming from a specific publisher site.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 1 hour.</td>
								</tr>
								<tr>
									<td>aff_dedup</td>
									<td>Cookie gets placed when visitor is coming from a specific branding campaign.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 10 days.</td>
								</tr>
								<tr>
									<td>Takeaway.com ( __cfduid)</td>
									<td>Measures the performance of the site.</td>
									<td>Is shared with Cloudfare that only uses this for reporting to takeaway.com.</td>
									<td>Gets deleted after 1 year.</td>
								</tr>
								<tr>
									<td><strong>Tealium</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>pem_abcid</td>
									<td>Used for visitor identification</td>
									<td>Is shared with Google Analytics that only uses this for reporting to Takeaway.com.</td>
									<td>Gets deleted after 30 days.</td>
								</tr>
								<tr>
									<td><strong>Crazy Egg</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>Is_returning_ceir</td>
									<td>To track whether a visitor has been on the website before.</td>
									<td>Is shared with Crazy Egg that only uses this for reporting to takeaway.com.</td>
									<td>Gets deleted after 5 years.</td>
								</tr>
								<tr>
									<td>_ceg.s</td>
									<td>Track visitor sessions.</td>
									<td>Is shared with Crazy Egg that only uses this for reporting to takeaway.com.</td>
									<td>Gets deleted after 3 months.</td>
								</tr>
								<tr>
									<td>_ceg.u</td>
									<td>Track visitor sessions.</td>
									<td>Is shared with Crazy Egg that only uses this for reporting to takeaway.com.</td>
									<td>Gets deleted after 3 months.</td>
								</tr>
								<tr>
									<td>_CEFT</td>
									<td>Store page variants assigned to visitors for A/B performance testing.</td>
									<td>Is shared with Crazy Egg that only uses this for reporting to takeaway.com.</td>
									<td>Gets deleted after 1 year.</td>
								</tr>
								<tr>
									<td><strong>Optimizely</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>OptimizelyBuckets</td>
									<td>Store page variants assigned to visitors for A/B performance testing.</td>
									<td>Is shared with Optimizely that only uses this for reporting to takeaway.com.</td>
									<td>Gets deleted after 10 years.</td>
								</tr>
								<tr>
									<td>OptimizelyEndUserID</td>
									<td>Unique visitor identifier.</td>
									<td>Is shared with Optimizely that only uses this for reporting to takeaway.com.</td>
									<td>Gets deleted after 10 years.</td>
								</tr>
								<tr>
									<td>OptimizelyGeographical</td>
									<td>Stores information certain geographical areas or languages which are set in the visitors browsers.
									</td>
									<td>Is shared with Optimizely that only uses this for reporting to takeaway.com.</td>
									<td>Gets deleted after 10 years.</td>
								</tr>
								<tr>
									<td>OptimizelySegments</td>
									<td>Hold visitor's audience segmentation information.</td>
									<td>Is shared with Optimizely that only uses this for reporting to takeaway.com.</td>
									<td>Gets deleted after 10 years.</td>
								</tr>
								<tr>
									<td>OptimizelyPendingLogEvents</td>
									<td>Record visitor activity.</td>
									<td>Is shared with Optimizely that only uses this for reporting to takeaway.com.</td>
									<td>Gets deleted after 15 seconds.</td>
								</tr>
								<tr>
									<td><strong>Google Analytics</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>_ga, _ga_*</td>
									<td>To differentiate between different user, unique visitor ID.</td>
									<td>Is shared with Google Analytics that only uses this for reporting to Takeaway.com.</td>
									<td>Gets deleted after 26 months.</td>
								</tr>
								<tr>
									<td>_gcl_au</td>
									<td>This cookie is used by Google Ads to attribute the conversion to the right ad click.</td>
									<td>Data shared with Google.</td>
									<td>Gets deleted after 3 months.</td>
								</tr>
								<tr>
									<td>_gat</td>
									<td>To Request rate throttling.</td>
									<td>Is shared with Google Analytics that only uses this for reporting to Takeaway.com.</td>
									<td>Gets deleted after 10 minutes.</td>
								</tr>
								<tr>
									<td>_gid</td>
									<td>To differentiate between different user, unique visitor ID.</td>
									<td>Is shared with Google Analytics that only uses this for reporting to Takeaway.com.</td>
									<td>Gets deleted after 26 months.</td>
								</tr>
								<tr>
									<td>utma</td>
									<td>To distinguish users and sessions.</td>
									<td>Is shared with Google Analytics that only uses this for reporting to Takeaway.com.</td>
									<td>Gets deleted after 26 months.</td>
								</tr>
								<tr>
									<td>utmb</td>
									<td>To determine new sessions/visits.</td>
									<td>Is shared with Google Analytics that only uses this for reporting to Takeaway.com.</td>
									<td>Gets deleted after 26 months.</td>
								</tr>
								<tr>
									<td>utmc</td>
									<td>Used in conjunction with utmb cookie to determine whether the user was in a new session/visit.</td>
									<td> Is shared with Google Analytics that only uses this for reporting to Takeaway.com.</td>
									<td>Gets deleted after 26 months.</td>
								</tr>
								<tr>
									<td>utmv</td>
									<td>To store visitor-level custom variable data.</td>
									<td>Is shared with Google Analytics that only uses this for reporting to Takeaway.com.</td>
									<td>Gets deleted after 26 months.</td>
								</tr>
								<tr>
									<td>utmz</td>
									<td>To store the traffic source or campaign that explains how the user reached the site.</td>
									<td>Is shared with Google Analytics that only uses this for reporting to Takeaway.com.</td>
									<td>Gets deleted after 26 months.</td>
								</tr>
								<tr>
									<td>uid</td>
									<td>This is an identifier for a user provided by the site (user-id)</td>
									<td>Is shared with Google Analytics that only uses this for reporting to Takeaway.com.</td>
									<td>Gets deleted after 60 days.</td>
								</tr>
								<tr>
									<td>cid</td>
									<td>This anonymously identifies a particular user, device or browser (clientid)</td>
									<td>Is shared with Google Analytics that only uses this for reporting to Takeaway.com.</td>
									<td>Gets deleted after 60 days.</td>
								</tr>
								<tr>
									<td><strong>New Relic</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td></td>
									<td>
										New Relic's platform measures and monitors the performance of their applications and infrastructure
										by collecting two main types of data: metric and event.
									</td>
									<td>
										s shared with New Relic that only uses this for reporting to Takeaway.com. Data is aggregated and
										anonymous.
									</td>
									<td></td>
								</tr>
								<tr>
									<td><strong>Hotjar</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>_hjClosedSurveyInvites</td>
									<td>Ensures that the same invite does not re-appear if it has already been shown.</td>
									<td>Is shared with Hotjar that only uses this for reporting to takeaway.com.</td>
									<td>365 days</td>
								</tr>
								<tr>
									<td>_hjDonePolls</td>
									<td>Ensures that the same Poll does not re-appear if it has already been filled in.</td>
									<td>Is shared with Hotjar that only uses this for reporting to takeaway.com.</td>
									<td>365 days</td>
								</tr>
								<tr>
									<td>_hjMinimizedPolls</td>
									<td>Ensures that the widget stays minimized when the visitor navigates through the site.</td>
									<td>Is shared with Hotjar that only uses this for reporting to takeaway.com.</td>
									<td>365 days</td>
								</tr>
								<tr>
									<td>_hjDoneTestersWidgets</td>
									<td>Ensures that the same form does not re-appear if it has already been filled in.</td>
									<td>Is shared with Hotjar that only uses this for reporting to takeaway.com.</td>
									<td>365 days</td>
								</tr>
								<tr>
									<td>_hjMinimizedTestersWidgets</td>
									<td>Ensures that the widget stays minimized when the visitor navigates through the site.</td>
									<td>Is shared with Hotjar that only uses this for reporting to takeaway.com.</td>
									<td>365 days</td>
								</tr>
								<tr>
									<td>_hjDoneSurveys</td>
									<td>Used to only load the survey content if the visitor hasn't completed the survey yet.</td>
									<td>Is shared with Hotjar that only uses this for reporting to takeaway.com.</td>
									<td>365 days</td>
								</tr>
								<tr>
									<td>_hjIncludedInSample</td>
									<td>Let's Hotjar know whether a visitor is included in the sample which is used to generate Heatmaps,
										Funnels, Recordings, etc.
									</td>
									<td>Is shared with Hotjar that only uses this for reporting to takeaway.com.</td>
									<td>365 days</td>
								</tr>
								<tr>
									<td>_hjShownFeedbackMessage</td>
									<td>Ensures that the widget stays minimized when the visitor navigates through the site.</td>
									<td>Is shared with Hotjar that only uses this for reporting to takeaway.com.</td>
									<td>365 days</td>
								</tr>
								<tr>
									<td>_hjid</td>
									<td>This cookie is set to ensure that behavior in subsequent visits to the same site will be attributed
										to the same user ID.
									</td>
									<td>Is shared with Hotjar that only uses this for reporting to takeaway.com.</td>
									<td>Gets deleted after 1 year</td>
								</tr>
							</tbody>
						</table>
						
						<h1 class="underline" style="display: inline-block;">Marketing Technologies</h1>
						<p>Obviously we hope that you use our website as often as possible and therefore we use cookies and trackers to advertise our website to you. The following Technologies are used for marketing purposes:</p>
						
						<table id="table" class="table table-bordered table-hover mt-5" style="border: 1px solid #dee2e6;">
							<caption class="hidden">Marketing technologies</caption>
							<tbody>
								<tr>
									<th scope="col">Name supplier (name technology)</th>
									<th scope="col">Purpose of technology</th>
									<th scope="col">Shared with third parties</th>
									<th scope="col">Lifetime of technology</th>
								</tr>
								<tr>
									<td><strong>Takeaway.com</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>deviceBnnr</td>
									<td>Determine whether the user has viewed and clicked on the banner which advertises the mobile app</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>utm_source=an</td>
									<td>Cookie gets placed when utm_source=an (Affilinet) appears in URL to recognize if a visitor is coming
										from related publisher sites and to activate the Affilinet pixel on the thank you page.
									</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 10 days.</td>
								</tr>
								<tr>
									<td>realrefr</td>
									<td>Cookie gets placed by Takeaway.com to track if visitors are coming from related publisher sites and
										reward them for this.
									</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 14 days.</td>
								</tr>
								<tr>
									<td>refr=kliks</td>
									<td>Cookie gets placed when refr=kliks appears in URL to recognize if a visitor is coming from related
										publisher sites and to activate the Daisycon pixel on the thank you page.
									</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 10 days.</td>
								</tr>
								<tr>
									<td>utm_source=be</td>
									<td>Cookie gets placed when utm_source=be appears in URL to recognize if a visitor is coming from
										related publisher sites and to activate the Belboon pixel on the thank you page.
									</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 10 days.</td>
								</tr>
								<tr>
									<td>last_campaign_source</td>
									<td>Used to identify the channel source of a visitor.</td>
									<td>Not shared with third parties.</td>
									<td>Gets deleted after 2 years.</td>
								</tr>
								<tr>
									<td>Drift chatbot</td>
									<td>Used to answer questions and get in touch with visitors if this is requested.</td>
									<td>Shared with third parties.</td>
									<td>Gets deleted after 2 years.</td>
								</tr>
								<tr>
									<td><strong>Bing</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>Bing Ads, UET</td>
									<td>Bing Ads helps us to provide directed advertising on the basis of your search history.</td>
									<td>Data shared with Bing.</td>
									<td>Gets deleted after 30 days.</td>
								</tr>
								<tr>
									<td>MUID, MUIDB</td>
									<td>This cookie is used to retarget website visitors via Bing.</td>
									<td>Data shared with Bing.</td>
									<td>1 year</td>
								</tr>
								<tr>
									<td><strong>Affilinet</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>Webmasterplan.com (affili_0, ASP.NET_Sessionid, affili_8047)</td>
									<td>Cookie gets placed by Affilinet to track if visitors are coming from publisher sites and reward them
										for this.
									</td>
									<td>Anonymized data is shared with Affilinet.</td>
									<td>Gets deleted after 1 year.</td>
								</tr>
								<tr>
									<td><strong>Daisycon</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>Ds1.nl (cfduid, ci_1968, ci_76, dci, pdc)</td>
									<td>Cookie gets placed by Daisycon to track if visitors are coming from publisher sites and reward them
										for this.
									</td>
									<td>Anonymized data is shared with Daisycon.</td>
									<td>Gets deleted after 1 year.</td>
								</tr>
								<tr>
									<td><strong>Awin</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>Zenaps.com &amp; dwin1.com (aw12109, bld)</td>
									<td>Cookie gets placed by Awin to track if visitors are coming from publisher sites and reward them for
										this.
									</td>
									<td>Anonymized data is shared with Awin.</td>
									<td>Gets deleted after 1 year.</td>
								</tr>
								<tr>
									<td><strong>Belboon</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>Belboon.de (belboon18146), Belboon.com (cfduid, ga)</td>
									<td>Cookie gets placed by Belboon to track if visitors are coming from publisher sites and reward them
										for this.
									</td>
									<td>Anonymized data is shared with Belboon.</td>
									<td>Gets deleted after 1 year.</td>
								</tr>
								<tr>
									<td><strong>Google DoubleClick Floodlight</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>IDE, id,_drt_, __gads, test_cookie</td>
									<td>For measuring conversions. This anonymous information will be used for determining value for the
										advertising partner and for calculating the costs for the advertising partner
									</td>
									<td>Data shared with Google.</td>
									<td>Gets deleted after 24 months.</td>
								</tr>
								<tr>
									<td>doubleclick_net, test_cookie</td>
									<td>For building up anonymous visitor segments</td>
									<td>Data shared with Google.</td>
									<td>Gets deleted after 24 months.</td>
								</tr>
								<tr>
									<td>DSID</td>
									<td>Used to check if the user's browser supports cookies.</td>
									<td>Data shared with Google.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td><strong>Google Adwords Conversion</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>NID</td>
									<td>
										Used by Google to remember your preferences and other information, such as your preferred language,
										how many search results you wish to have shown per page and whether or not you wish to have Google’s
										SafeSearch filter turned on.
									</td>
									<td>Data shared with Google.</td>
									<td>Gets deleted after 30 days.</td>
								</tr>
								<tr>
									<td>HSID</td>
									<td>
										Used to authenticate users, prevent fraudulent use of login credentials, and protect user data
										from unauthorized parties.
									</td>
									<td>Data shared with Google.</td>
									<td>Gets deleted after 30 days.</td>
								</tr>
								<tr>
									<td>SSID</td>
									<td>Used in combination with HSID to block attacks from unauthorized parties.</td>
									<td>Data shared with Google.</td>
									<td>Gets deleted after 30 days.</td>
								</tr>
								<tr>
									<td>APISID</td>
									<td>Used for advertisement campaigns.</td>
									<td>Data shared with Google.</td>
									<td>Gets deleted after 30 days.</td>
								</tr>
								<tr>
									<td>SAPISID</td>
									<td>Used for advertisement campaigns.</td>
									<td>Data shared with Google.</td>
									<td>Gets deleted after 30 days.</td>
								</tr>
								<tr>
									<td><strong>Indeed</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>INDEED_CSRF_TOKEN</td>
									<td>Ensures visitor browsing-security by preventing cross-site request forgery</td>
									<td>Data shared with Indeed</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>jasx_pool_id</td>
									<td>Saves information of actions that have been carried out by the user during the current visit to the
										website
									</td>
									<td>Data shared with Indeed</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>JSESSIONID</td>
									<td>Preserves users states across page requests.</td>
									<td>Data shared with Indeed</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>ctkgen</td>
									<td>This cookie determines which job advertisements the visitor is shown, and which are clicked on -
										this info
									</td>
									<td>Data shared with Indeed</td>
									<td>Gets deleted after 1 day.</td>
								</tr>
								<tr>
									<td>CTK</td>
									<td>This cookie determines which job advertisements the visitor is shown, and which are clicked on -
										this info
									</td>
									<td>Data shared with Indeed</td>
									<td>Gets deleted after 17 years.</td>
								</tr>
								<tr>
									<td><strong>Linkedin</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>UserMatchHistory</td>
									<td>Used to track visitors on multiple websites, in order to present relevant advertisement based on the
										visitor’s preferences.
									</td>
									<td>Data shared with LinkedIn</td>
									<td>Gets deleted after 30 days.</td>
								</tr>
								<tr>
									<td>bcookie</td>
									<td>Used by the social networking service, LinkedIn, for tracking the use of embedded services.</td>
									<td>Data shared with LinkedIn</td>
									<td>Gets deleted after 2 years.</td>
								</tr>
								<tr>
									<td>bscookie</td>
									<td>Used by the social networking service, LinkedIn, for tracking the use of embedded services.</td>
									<td>Data shared with LinkedIn</td>
									<td>Gets deleted after 2 years.</td>
								</tr>
								<tr>
									<td>lang</td>
									<td>Remembers the user’s selected language version of a website</td>
									<td>Data shared with LinkedIn</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>lang</td>
									<td>Set by LinkedIn when a web page contains an embedded “Follow us” panel.</td>
									<td>Data shared with LinkedIn</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>lidc</td>
									<td>Used by the social networking service, LinkedIn, for tracking the use of embedded services.</td>
									<td>Data shared with LinkedIn</td>
									<td>Gets deleted after 1 day.</td>
								</tr>
								<tr>
									<td>lissc</td>
									<td>Used by the social networking service, LinkedIn, for tracking the use of embedded services.</td>
									<td>Data shared with LinkedIn</td>
									<td>Gets deleted after 1 year.</td>
								</tr>
								<tr>
									<td><strong>Facebook</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>sb</td>
									<td>Identifies browser for login authentication purposes.</td>
									<td>Data shared with Facebook.</td>
									<td>Gets deleted after 680 days.</td>
								</tr>
								<tr>
									<td>pl</td>
									<td>Used to record that a device or browser logged in via Facebook platform.</td>
									<td>Data shared with Facebook.</td>
									<td>Gets deleted after 40 days.</td>
								</tr>
								<tr>
									<td>wd</td>
									<td>Measures the performance of the website.</td>
									<td>Data shared with Facebook.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>fr</td>
									<td>Used by Facebook to deliver a series of advertisement products such as real time bidding from third
										party advertisers.
									</td>
									<td>Data shared with Facebook.</td>
									<td>Gets deleted after 90 days.</td>
								</tr>
								<tr>
									<td>xs</td>
									<td>Used in conjunction with the c_user cookie to authenticate your identity to Facebook</td>
									<td>Data shared with Facebook.</td>
									<td>Gets deleted after 90 days.</td>
								</tr>
								<tr>
									<td>datr</td>
									<td>Identifies browsers for purposes of security and site integrity, including for account recovery, and
										identification of potentially compromised accounts.
									</td>
									<td>Data shared with Facebook.</td>
									<td>Gets deleted after 680 days.</td>
								</tr>
								<tr>
									<td>c_user</td>
									<td>Used in conjunction with the xs cookie to authenticate your identity to Facebook</td>
									<td>Data shared with Facebook.</td>
									<td>Gets deleted after 90 days.</td>
								</tr>
								<tr>
									<td>act</td>
									<td>Analyses website behaviour.</td>
									<td>Data shared with Facebook.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>reg_fb_gate</td>
									<td>Analyses website behaviour.</td>
									<td>Data shared with Facebook.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>reg_fb_ref</td>
									<td>Analyses website behaviour.</td>
									<td>Data shared with Facebook.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>reg_ext_ref</td>
									<td>Analyses website behaviour.</td>
									<td>Data shared with Facebook.</td>
									<td>Gets deleted after session.</td>
								</tr>
								<tr>
									<td>_fbp</td>
									<td>This cookie is used by Facebook to deliver a series of advertisement products such as real time
										bidding from third party advertisers
									</td>
									<td>Data shared with Facebook.</td>
									<td>90 days</td>
								</tr>
								<tr>
									<td><strong>TradeDesk</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>TDID</td>
									<td>Contains a unique randomly-generated value that enables the platform to distinguish browsers and
										devices.
									</td>
									<td>yes</td>
									<td>1 year</td>
								</tr>
								<tr>
									<td>TDCPM</td>
									<td>Contains data denoting whether a cookie ID is synced, enabling the data to be used outside the
										trading platform.
									</td>
									<td>yes</td>
									<td>1 year</td>
								</tr>
								<tr>
									<td><strong>JobBoost.io</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>jobboostio</td>
									<td>To differentiate between different user, unique visitor ID.</td>
									<td>Is shared with JobBoost.io that only uses this for reporting to Takeaway.com.</td>
									<td>2 hours</td>
								</tr>
								<tr>
									<td><strong>Realytics</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>ry_*_realytics</td>
									<td>Allows Realytics to anonymously recognise a visitor.</td>
									<td>Data shared with Realytics.</td>
									<td>Gets deleted after 1 year.</td>
								</tr>
								<tr>
									<td>ry_*_so_realytics</td>
									<td>Allows Realytics to anonymously know more about the current session state.</td>
									<td>Data shared with Realytics.</td>
									<td>Gets deleted after 30 minutes.</td>
								</tr>
								<tr>
									<td>ry-optout</td>
									<td>Allows Realytics to know if a web visitor declined to be tracked.</td>
									<td>Data shared with Realytics.</td>
									<td>Gets deleted after 6 months.</td>
								</tr>
								<tr>
									<td><strong>Snapchat</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td>sc_at</td>
									<td>Used to identify a visitor across multiple domains.</td>
									<td>Shared with Snapchat</td>
									<td>13 months</td>
								</tr>
								<tr>
									<td>_sctr</td>
									<td>Used to determine whether a third party tag will be called in Snap Ads Pixel.</td>
									<td>Shared with Snapchat</td>
									<td>7 days</td>
								</tr>
								<tr>
									<td>_scid</td>
									<td>Used to help identify a visitor.</td>
									<td>Shared with Snapchat</td>
									<td>13 months</td>
								</tr>
								<tr>
									<td><strong>Fountain</strong></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td></td>
									<td>By applying on takeaway.com/drivers your data will be collected in our ATS tool, which is Fountain.com we only use Google Analytics (first party cookie)</td>
									<td>Is shared with Fountain that only uses this for reporting to Takeaway.com.</td>
									<td>364 days after the application all candidate information in Fountain gets anonymized</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</x-user.content>
@endsection