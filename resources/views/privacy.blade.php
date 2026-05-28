{{--
    privacy.blade.php — Privacy Policy
    ==================================
    Publicly accessible page detailing data collection, processing bases,
    cookies, security and user rights under UK GDPR and Data Protection Act 2018.
--}}
@extends('layouts.app')
@section('title', 'Privacy Policy — Premier Shop')

@section('content')
<section class="contact-page py-5">
    {{-- Animated Background Orbs for premium visual layout --}}
    <div class="contact-bg-orbs">
        <div class="orb orb-1" style="background: radial-gradient(circle, rgba(108, 92, 231, 0.15) 0%, rgba(255,255,255,0) 70%);"></div>
        <div class="orb orb-2" style="background: radial-gradient(circle, rgba(0, 206, 201, 0.15) 0%, rgba(255,255,255,0) 70%);"></div>
        <div class="orb orb-3" style="background: radial-gradient(circle, rgba(0, 184, 148, 0.15) 0%, rgba(255,255,255,0) 70%);"></div>
    </div>

    <div class="container position-relative" style="z-index: 2;">
        {{-- Header Section --}}
        <div class="text-center mb-5 reveal-3d">
            <span class="glass-pill mb-3 d-inline-flex">
                <i class="bi bi-shield-lock-fill text-primary"></i>
                <span class="fw-bold small">Data Protection & Privacy Compliance</span>
            </span>
            <h1 class="section-title mt-3">Privacy <span class="gradient-text">Policy</span></h1>
            <p class="section-subtitle text-muted mx-auto" style="max-width: 600px;">
                Premier Shop is committed to protecting your personal data in accordance with the UK GDPR, Data Protection Act 2018, and applicable electronic communications regulations.
            </p>
        </div>

        {{-- Main Content --}}
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="glass-card p-4 p-md-5 shadow-lg rounded-4" style="backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.08);">
                    
                    {{-- Effective Date --}}
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-4 mb-4 text-muted small">
                        <span><strong>Effective Date:</strong> May 28, 2026</span>
                        <span><strong>Region:</strong> United Kingdom</span>
                    </div>

                    <div class="privacy-sections" style="line-height: 1.7; color: rgba(255,255,255,0.85);">
                        
                        {{-- Section 1 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>1. Who We Are (Data Controller)</h4>
                            <p>
                                Premier Shop (trading as "Premier Shop", "We", "Our", or "Us") is the **Data Controller** responsible for your personal data. We are located at:
                            </p>
                            <div class="alert alert-dark bg-soft-primary border-0 rounded-3 p-3 mt-3 text-white small d-flex align-items-start gap-2">
                                <i class="bi bi-geo-alt-fill text-primary mt-1"></i>
                                <div>
                                    <strong>Registered Store Address (Origin Depot):</strong><br>
                                    {{ \App\Models\Setting::get('origin_address', 'London, United Kingdom') }}<br>
                                    <strong>Data Protection Officer (DPO) Contact:</strong> privacy@premiershop.com
                                </div>
                            </div>
                        </div>

                        {{-- Section 2 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-database me-2 text-primary"></i>2. The Data We Collect About You</h4>
                            <p>
                                Personal data means any information about an individual from which that person can be identified. We may collect, use, store, and transfer different kinds of personal data:
                            </p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>Identity & Profile Data:</strong> First name, last name, username or email identifier, date of birth (DOB) to verify age-restricted products, passwords, purchase histories, product reviews, and loyalty rewards history.</li>
                                <li class="mb-2"><strong>Contact Data:</strong> Billing address, delivery/shipping address, email address, and telephone number.</li>
                                <li class="mb-2"><strong>Financial & Transaction Data:</strong> Payment details (processed securely via regulated third-party payment gateways; we do not store raw credit/debit card numbers), order history details, coupons claimed, and shipping surcharges.</li>
                                <li class="mb-2"><strong>Geolocation & Spatial Data:</strong> 
                                    To calculate accurate shipping distances and miles-based delivery surcharges under our shipping policies, our servers map your delivery address against our store coordinates using the Google Maps Distance Matrix API. We also process real-time simulated courier tracking metrics and dispatch location points for courier tracking updates.
                                </li>
                                <li class="mb-2"><strong>Interaction Logs:</strong> Inputs provided to our global **"Premier Assist"** virtual chatbot shopping companion, custom support tickets, and contact form messages.</li>
                                <li class="mb-2"><strong>Technical & Cookie Data:</strong> IP address, browser type, device information, login session state tokens, and local storage indicators (e.g. secure stock reservation timers).</li>
                            </ul>
                        </div>

                        {{-- Section 3 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-file-earmark-ruled me-2 text-primary"></i>3. Legal Bases for Processing Data</h4>
                            <p>
                                Under the **UK GDPR**, we only process your data when we have a valid legal basis to do so:
                            </p>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered text-white small" style="border-color: rgba(255,255,255,0.08);">
                                    <thead>
                                        <tr class="bg-dark">
                                            <th>Purpose / Processing Activity</th>
                                            <th>Types of Data Processed</th>
                                            <th>Legal Basis for Processing</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold">Creating your account and authentication</td>
                                            <td>Identity, Contact, Tech</td>
                                            <td>Contract Performance (T&Cs)</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Processing purchases, checkout and calculating shipping fees based on physical distance metrics</td>
                                            <td>Identity, Contact, Transaction, Geolocation</td>
                                            <td>Contract Performance</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Operating and updating loyalty reward points and active coupon ticket claims</td>
                                            <td>Identity, Transaction</td>
                                            <td>Contract Performance</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Newsletter subscriptions & promotional notifications</td>
                                            <td>Contact</td>
                                            <td>Consent (Withdraw at any time)</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Providing support through customer support and "Premier Assist" companion chatbot logging</td>
                                            <td>Profile, Interaction logs</td>
                                            <td>Legitimate Interests (Assisting shoppers)</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Preventing fraud and maintaining system security (session and local storage monitoring)</td>
                                            <td>Technical, Transaction</td>
                                            <td>Legal Obligation / Legitimate Interests</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Section 4 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-share me-2 text-primary"></i>4. Who We Share Your Data With</h4>
                            <p>
                                We do not sell your personal data. We may share your information with trusted third parties who perform services for us in accordance with data processor agreements:
                            </p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>Couriers & Drivers:</strong> Assigned drivers receive your name, address line, and phone number to execute and verify deliveries, including secure photographic Proof-of-Delivery uploads.</li>
                                <li class="mb-2"><strong>Google Maps Platform:</strong> Delivery address strings are transmitted securely to compute route mileage; no personal identity attributes are shared with these requests.</li>
                                <li class="mb-2"><strong>Payment Processors:</strong> Regulated, PCI-DSS compliant providers handling credit/debit card cardholder data.</li>
                                <li class="mb-2"><strong>Email and Infrastructure Providers:</strong> Regulated SMTP hosts handling order invoices and OTP signup verification emails.</li>
                            </ul>
                        </div>

                        {{-- Section 5 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-clock me-2 text-primary"></i>5. Data Retention</h4>
                            <p>
                                We only retain your personal data for as long as necessary to fulfill the purposes we collected it for, including satisfying legal, accounting, or HMRC financial reporting requirements. 
                            </p>
                            <p>
                                By law, we must keep basic transaction and financial records (including Contact, Identity, Financial, and Transaction Data) for **six (6) tax years** after they purchase products from us to comply with UK HM Revenue & Customs (HMRC) requirements. Support inquiries and newsletter lists are retained until you request deletion or withdraw consent.
                            </p>
                        </div>

                        {{-- Section 6 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-cookie me-2 text-primary"></i>6. Cookies & Local Storage</h4>
                            <p>
                                Our application uses essential cookies and local storage items to perform critical e-commerce functions:
                            </p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>Laravel Session Cookies:</strong> Essential for keeping you logged in and preserving your shopping cart items as you navigate pages.</li>
                                <li class="mb-2"><strong>Theme Toggle Switcher:</strong> Saves your `admin_theme` or customer preference in local storage to keep pages styled consistently.</li>
                                <li class="mb-2"><strong>Stock Reservation Timer:</strong> Saves checkout stock reservation thresholds inside LocalStorage (`15:00` countdown) to prompt timely purchases.</li>
                            </ul>
                        </div>

                        {{-- Section 7 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-person-check me-2 text-primary"></i>7. Your Legal Rights Under UK Law</h4>
                            <p>
                                Under the UK GDPR, you have the following rights in relation to your personal data:
                            </p>
                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 h-100" style="border-color: rgba(255,255,255,0.06); background: rgba(255,255,255,0.02);">
                                        <h6 class="fw-bold text-primary"><i class="bi bi-eye me-1"></i> Right of Access & Portability</h6>
                                        <p class="small text-muted mb-0">You can request a copy of the personal data we hold about you at any time, delivered in a structured, machine-readable format.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 h-100" style="border-color: rgba(255,255,255,0.06); background: rgba(255,255,255,0.02);">
                                        <h6 class="fw-bold text-primary"><i class="bi bi-pencil-square me-1"></i> Right to Rectification</h6>
                                        <p class="small text-muted mb-0">You have the right to request that we correct any incomplete, outdated, or inaccurate information we hold about you.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 h-100" style="border-color: rgba(255,255,255,0.06); background: rgba(255,255,255,0.02);">
                                        <h6 class="fw-bold text-primary"><i class="bi bi-trash me-1"></i> Right to Erasure ("Forgotten")</h6>
                                        <p class="small text-muted mb-0">You can request that we delete or remove your personal data where there is no legal requirement for us to continue processing it.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 h-100" style="border-color: rgba(255,255,255,0.06); background: rgba(255,255,255,0.02);">
                                        <h6 class="fw-bold text-primary"><i class="bi bi-hand-thumbs-down me-1"></i> Right to Object & Restrict</h6>
                                        <p class="small text-muted mb-0">You can object to direct marketing or request that we restrict processing in certain circumstances under UK GDPR.</p>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-4 small">
                                To exercise any of these rights, please email us directly at **privacy@premiershop.com**. If you are unhappy with how we process your data, you have the right to make a complaint at any time to the **Information Commissioner's Office (ICO)**, the UK supervisory authority for data protection issues (see <a href="https://ico.org.uk" target="_blank" class="text-primary text-decoration-none">ico.org.uk</a>).
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
