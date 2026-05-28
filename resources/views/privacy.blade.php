{{--
    privacy.blade.php — Privacy Policy
    ==================================
    UK GDPR / Data Protection Act 2018 / PECR compliant.
    All content uses HTML strong/em tags — Blade does not process Markdown.
--}}
@extends('layouts.app')
@section('title', 'Privacy Policy — Premier Shop')

@section('content')
<section class="contact-page py-5">
    <div class="contact-bg-orbs">
        <div class="orb orb-1" style="background: radial-gradient(circle, rgba(108, 92, 231, 0.15) 0%, rgba(255,255,255,0) 70%);"></div>
        <div class="orb orb-2" style="background: radial-gradient(circle, rgba(0, 206, 201, 0.15) 0%, rgba(255,255,255,0) 70%);"></div>
        <div class="orb orb-3" style="background: radial-gradient(circle, rgba(0, 184, 148, 0.15) 0%, rgba(255,255,255,0) 70%);"></div>
    </div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="text-center mb-5 reveal-3d">
            <span class="glass-pill mb-3 d-inline-flex">
                <i class="bi bi-shield-lock-fill text-primary"></i>
                <span class="fw-bold small">Data Protection &amp; Privacy Compliance</span>
            </span>
            <h1 class="section-title mt-3">Privacy <span class="gradient-text">Policy</span></h1>
            <p class="section-subtitle text-muted mx-auto" style="max-width: 620px;">
                Premier Shop is committed to protecting your personal data in accordance with the UK GDPR, Data Protection Act 2018, and the Privacy and Electronic Communications Regulations (PECR).
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="glass-card p-4 p-md-5 shadow-lg rounded-4" style="backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.08);">

                    <div class="d-flex align-items-center justify-content-between border-bottom pb-4 mb-4 text-muted small flex-wrap gap-2">
                        <span><strong>Effective Date:</strong> 28 May 2026</span>
                        <span><strong>Region:</strong> United Kingdom</span>
                        <span><strong>Version:</strong> 1.0</span>
                    </div>

                    <div class="privacy-sections" style="line-height: 1.8; color: rgba(255,255,255,0.85);">

                        {{-- Section 1 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>1. Who We Are (Data Controller)</h4>
                            <p>
                                Premier Shop (trading as "Premier Shop", "we", "our", or "us") is the <strong>Data Controller</strong> responsible for your personal data under the UK GDPR and the Data Protection Act 2018. Our registered store address is:
                            </p>
                            <div class="alert alert-dark bg-soft-primary border-0 rounded-3 p-3 mt-3 text-white small d-flex align-items-start gap-2">
                                <i class="bi bi-geo-alt-fill text-primary mt-1 flex-shrink-0"></i>
                                <div>
                                    <strong>Registered Store Address (Origin Depot):</strong><br>
                                    {{ \App\Models\Setting::get('origin_address', 'London, United Kingdom') }}<br>
                                    <strong>Data Protection Enquiries:</strong>
                                    {{ \App\Models\Setting::get('contact_email', 'privacy@premiershop.com') }}
                                </div>
                            </div>
                        </div>

                        {{-- Section 2 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-database me-2 text-primary"></i>2. The Data We Collect About You</h4>
                            <p>Personal data means any information about an individual from which that person can be identified. We may collect, use, store, and transfer the following categories of personal data:</p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>Identity &amp; Profile Data:</strong> First name, last name, username or email identifier, date of birth (used to verify eligibility for age-restricted products), passwords (stored as cryptographic hashes), purchase histories, product reviews and ratings you submit, loyalty rewards history, and VIP tier status.</li>
                                <li class="mb-2"><strong>Profile Photo / Avatar:</strong> If you choose to upload a profile photograph, that image is stored on our servers in WebP format. Uploading a profile photo is entirely optional. You may remove it at any time via your account settings.</li>
                                <li class="mb-2"><strong>Contact Data:</strong> Billing address, delivery/shipping address, email address, and telephone number.</li>
                                <li class="mb-2"><strong>Financial &amp; Transaction Data:</strong> Payment details are processed exclusively by our PCI-DSS compliant third-party payment gateway — we do not store raw card numbers. We retain order history, invoice amounts, coupon codes applied, and shipping charges.</li>
                                <li class="mb-2"><strong>Geolocation &amp; Distance Data:</strong> To calculate shipping fees, your delivery address is submitted to the Google Maps Distance Matrix API to determine the driving distance in miles from our depot. No persistent geolocation tracking is performed.</li>
                                <li class="mb-2"><strong>User-Generated Content:</strong> Product reviews, star ratings, and any content you submit are stored against your account and displayed publicly on our storefront. You may request removal via the contact form.</li>
                                <li class="mb-2"><strong>Premier Assist Interaction Data:</strong> Inputs you type into the Premier Assist shopping companion widget are processed client-side. Queries involving your order status or return history query our servers using your authenticated session — no third-party AI service receives your personal data through this widget.</li>
                                <li class="mb-2"><strong>Technical &amp; Cookie Data:</strong> IP address, browser type, device information, session tokens, and local storage values (e.g., theme preference, checkout countdown timer).</li>
                            </ul>
                        </div>

                        {{-- Section 3 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-file-earmark-ruled me-2 text-primary"></i>3. Legal Bases for Processing Your Data</h4>
                            <p>Under the <strong>UK GDPR</strong>, we only process your personal data when we have a valid legal basis:</p>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered text-white small" style="border-color: rgba(255,255,255,0.1);">
                                    <thead>
                                        <tr class="bg-dark">
                                            <th>Processing Activity</th>
                                            <th>Data Types</th>
                                            <th>Legal Basis</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Creating your account and authentication (OTP, password)</td>
                                            <td>Identity, Contact, Technical</td>
                                            <td>Contract Performance</td>
                                        </tr>
                                        <tr>
                                            <td>Processing purchases, checkout, and calculating distance-based shipping fees</td>
                                            <td>Identity, Contact, Transaction, Geolocation</td>
                                            <td>Contract Performance</td>
                                        </tr>
                                        <tr>
                                            <td>Managing loyalty reward points, VIP tiers, and coupon claims</td>
                                            <td>Identity, Transaction</td>
                                            <td>Contract Performance</td>
                                        </tr>
                                        <tr>
                                            <td>Dispatching drivers and recording Proof-of-Delivery photos</td>
                                            <td>Contact, Transaction</td>
                                            <td>Contract Performance</td>
                                        </tr>
                                        <tr>
                                            <td>Displaying your product reviews and ratings publicly</td>
                                            <td>Identity (username), User-Generated Content</td>
                                            <td>Legitimate Interests / Consent (on submission)</td>
                                        </tr>
                                        <tr>
                                            <td>Storing your optional profile photo</td>
                                            <td>Profile Photo</td>
                                            <td>Consent (explicit upload action)</td>
                                        </tr>
                                        <tr>
                                            <td>Newsletter and promotional email communications</td>
                                            <td>Contact</td>
                                            <td>Consent (withdraw at any time)</td>
                                        </tr>
                                        <tr>
                                            <td>Providing customer support and handling return/refund requests</td>
                                            <td>Identity, Contact, Transaction</td>
                                            <td>Contract Performance / Legal Obligation</td>
                                        </tr>
                                        <tr>
                                            <td>Fraud prevention, session security, and system monitoring</td>
                                            <td>Technical, Transaction</td>
                                            <td>Legal Obligation / Legitimate Interests</td>
                                        </tr>
                                        <tr>
                                            <td>HMRC-required financial record retention</td>
                                            <td>Identity, Contact, Financial</td>
                                            <td>Legal Obligation</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Section 4 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-share me-2 text-primary"></i>4. Who We Share Your Data With</h4>
                            <p>We do not sell your personal data. We share your information only with trusted third parties acting as <strong>data processors</strong> under formal agreements, strictly for the purposes described:</p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>Couriers &amp; Drivers:</strong> Assigned drivers receive your name, delivery address, and phone number to execute and verify deliveries, including photographic Proof-of-Delivery uploads.</li>
                                <li class="mb-2"><strong>Google Maps Platform (Google LLC, USA):</strong> Your delivery address string is transmitted to compute route mileage for shipping fees. No name, email, or payment data is included in these requests. This constitutes an <strong>international data transfer</strong> to the United States — see Section 5 below.</li>
                                <li class="mb-2"><strong>Payment Processors:</strong> PCI-DSS compliant payment gateways handling cardholder data securely. We do not receive or store raw card numbers.</li>
                                <li class="mb-2"><strong>Email Infrastructure Providers (SMTP):</strong> Providers who handle transactional emails (order invoices, OTP verification codes, order status updates) on our behalf.</li>
                            </ul>
                        </div>

                        {{-- Section 5 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-globe me-2 text-primary"></i>5. International Data Transfers</h4>
                            <p>
                                When we submit delivery address strings to the <strong>Google Maps Distance Matrix API</strong>, this data is processed by Google LLC, a company based in the United States. The UK and US do not have an adequacy decision in place, meaning we rely on <strong>Standard Contractual Clauses (SCCs) with the UK International Data Transfer Addendum (IDTA)</strong> as the transfer mechanism, as incorporated in Google's data processing terms.
                            </p>
                            <p>
                                No other international transfers of personal data are performed. All other data is stored on servers within the United Kingdom or European Economic Area (EEA) by our hosting and infrastructure providers.
                            </p>
                        </div>

                        {{-- Section 6 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-clock me-2 text-primary"></i>6. Data Retention</h4>
                            <p>We retain personal data only as long as necessary for the stated purpose or as required by law:</p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>Transaction &amp; Financial Records:</strong> Retained for <strong>6 tax years</strong> after the relevant tax year to comply with UK HMRC requirements (Companies Act 2006 / Finance Act 2007).</li>
                                <li class="mb-2"><strong>Account Data:</strong> Retained for the lifetime of your active account. If you request deletion and no legal retention obligation applies, we will erase your data within 30 days.</li>
                                <li class="mb-2"><strong>Product Reviews:</strong> Retained until you request removal or your account is deleted. Anonymised aggregate ratings may be retained.</li>
                                <li class="mb-2"><strong>Profile Photos:</strong> Retained until you remove them or delete your account.</li>
                                <li class="mb-2"><strong>Support Correspondence:</strong> Retained for 2 years from last contact, then securely deleted.</li>
                                <li class="mb-2"><strong>Proof-of-Delivery Photos:</strong> Retained for 12 months from delivery date for dispute resolution purposes, then deleted.</li>
                            </ul>
                        </div>

                        {{-- Section 7 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-cookie me-2 text-primary"></i>7. Cookies &amp; Local Storage (PECR)</h4>
                            <p>
                                Under the <strong>Privacy and Electronic Communications Regulations (PECR)</strong>, consent is required for non-essential cookies. Premier Shop uses only <strong>essential and functional</strong> cookies and local storage items — no advertising, tracking, or analytics cookies are currently deployed:
                            </p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>Laravel Session Cookie (<code>laravel_session</code>):</strong> Essential — keeps you authenticated and preserves your shopping cart. Without this cookie the site cannot function.</li>
                                <li class="mb-2"><strong>CSRF Token Cookie (<code>XSRF-TOKEN</code>):</strong> Essential security cookie that protects form submissions from cross-site request forgery attacks.</li>
                                <li class="mb-2"><strong>Theme Preference (Local Storage):</strong> Stores your chosen light/dark theme. Not a cookie — stored locally in your browser only. Not transmitted to our servers.</li>
                                <li class="mb-2"><strong>Checkout Countdown Timer (Local Storage):</strong> Stores a stock reservation countdown so you can resume checkout within the time limit. Not transmitted to our servers.</li>
                            </ul>
                            <p class="mt-3 small text-muted">
                                Because we do not use non-essential cookies, a cookie consent banner is not currently required under PECR. If we introduce analytics or advertising tools in future, this policy will be updated and appropriate consent mechanisms will be implemented.
                            </p>
                        </div>

                        {{-- Section 8 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-shield-check me-2 text-primary"></i>8. Data Security</h4>
                            <p>
                                We have implemented appropriate technical and organisational security measures to protect your personal data against accidental loss, unauthorised access, alteration, or disclosure. These include:
                            </p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2">HTTPS encryption for all data in transit via TLS with HTTP Strict Transport Security (HSTS).</li>
                                <li class="mb-2">Passwords stored as Bcrypt cryptographic hashes — plain-text passwords are never stored.</li>
                                <li class="mb-2">One-Time Password (OTP) email verification at registration.</li>
                                <li class="mb-2">HTTP security headers including Content Security Policy (CSP), X-Frame-Options, and Cross-Origin policies.</li>
                                <li class="mb-2">Role-based access controls restricting admin functionality to authorised staff.</li>
                            </ul>
                        </div>

                        {{-- Section 9 — Children --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-person-x me-2 text-primary"></i>9. Children's Data</h4>
                            <p>
                                Our services are intended exclusively for individuals aged <strong>18 years or older</strong> resident in the United Kingdom. We do not knowingly collect personal data from persons under 18. If we become aware that a person under 18 has provided us with personal data, we will delete that data promptly. If you believe a minor has registered an account, please contact us immediately at the address in Section 1.
                            </p>
                        </div>

                        {{-- Section 10 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-person-check me-2 text-primary"></i>10. Your Legal Rights Under UK Law</h4>
                            <p>Under the <strong>UK GDPR</strong>, you have the following rights in relation to your personal data:</p>
                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 h-100" style="border-color: rgba(255,255,255,0.08); background: rgba(255,255,255,0.02);">
                                        <h6 class="fw-bold text-primary"><i class="bi bi-eye me-1"></i>Right of Access (DSAR)</h6>
                                        <p class="small text-muted mb-0">Request a copy of the personal data we hold about you in a structured, machine-readable format. We will respond within <strong>30 days</strong> (extendable by 2 months for complex requests, with notice).</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 h-100" style="border-color: rgba(255,255,255,0.08); background: rgba(255,255,255,0.02);">
                                        <h6 class="fw-bold text-primary"><i class="bi bi-arrow-left-right me-1"></i>Right to Portability</h6>
                                        <p class="small text-muted mb-0">Receive your data in a portable format and transmit it to another controller, where technically feasible and where processing is based on consent or contract.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 h-100" style="border-color: rgba(255,255,255,0.08); background: rgba(255,255,255,0.02);">
                                        <h6 class="fw-bold text-primary"><i class="bi bi-pencil-square me-1"></i>Right to Rectification</h6>
                                        <p class="small text-muted mb-0">Request correction of any inaccurate or incomplete personal data we hold about you. You can update most profile data directly in your account settings.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 h-100" style="border-color: rgba(255,255,255,0.08); background: rgba(255,255,255,0.02);">
                                        <h6 class="fw-bold text-primary"><i class="bi bi-trash me-1"></i>Right to Erasure</h6>
                                        <p class="small text-muted mb-0">Request deletion of your personal data where there is no overriding legal basis to retain it (e.g. HMRC retention obligations may apply to financial records).</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 h-100" style="border-color: rgba(255,255,255,0.08); background: rgba(255,255,255,0.02);">
                                        <h6 class="fw-bold text-primary"><i class="bi bi-hand-thumbs-down me-1"></i>Right to Object &amp; Restrict</h6>
                                        <p class="small text-muted mb-0">Object to processing based on legitimate interests, or request restriction of processing in specific circumstances (e.g. while accuracy is contested).</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 h-100" style="border-color: rgba(255,255,255,0.08); background: rgba(255,255,255,0.02);">
                                        <h6 class="fw-bold text-primary"><i class="bi bi-x-circle me-1"></i>Right to Withdraw Consent</h6>
                                        <p class="small text-muted mb-0">Where processing is based on consent (e.g. newsletters, profile photo), you may withdraw consent at any time. Withdrawal does not affect the lawfulness of prior processing.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-dark bg-soft-primary border-0 rounded-3 p-3 mt-4 text-white small">
                                <strong>How to exercise your rights:</strong> Submit requests to
                                <strong>{{ \App\Models\Setting::get('contact_email', 'privacy@premiershop.com') }}</strong>
                                with your full name and account email. We will verify your identity before processing any request.
                                If you are dissatisfied with our response, you have the right to lodge a complaint with the
                                <strong>Information Commissioner's Office (ICO)</strong> — the UK supervisory authority for data protection:
                                <a href="https://ico.org.uk" target="_blank" rel="noopener noreferrer" class="text-primary">ico.org.uk</a>
                                | Helpline: 0303 123 1113.
                            </div>
                        </div>

                        {{-- Section 11 --}}
                        <div class="mb-4">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-pencil me-2 text-primary"></i>11. Changes to This Policy</h4>
                            <p>
                                We may update this Privacy Policy from time to time to reflect changes in our practices, technology, or legal requirements. We will notify you of material changes by updating the effective date at the top of this page and, where appropriate, by sending an email notification. We encourage you to review this policy periodically.
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
