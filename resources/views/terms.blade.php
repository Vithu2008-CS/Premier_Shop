{{--
    terms.blade.php — Terms of Service
    ==================================
    UK consumer law compliant: Consumer Rights Act 2015, Consumer Contracts
    Regulations 2013, Electronic Commerce Regulations 2002.
    All content uses HTML strong/em tags — Blade does not process Markdown.
--}}
@extends('layouts.app')
@section('title', 'Terms of Service — Premier Shop')

@section('content')
<section class="contact-page py-5">
    <div class="contact-bg-orbs">
        <div class="orb orb-1" style="background: radial-gradient(circle, rgba(116, 48, 137, 0.15) 0%, rgba(255,255,255,0) 70%);"></div>
        <div class="orb orb-2" style="background: radial-gradient(circle, rgba(0, 206, 201, 0.15) 0%, rgba(255,255,255,0) 70%);"></div>
        <div class="orb orb-3" style="background: radial-gradient(circle, rgba(0, 184, 148, 0.15) 0%, rgba(255,255,255,0) 70%);"></div>
    </div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="text-center mb-5 reveal-3d">
            <span class="glass-pill mb-3 d-inline-flex">
                <i class="bi bi-file-earmark-text-fill text-primary"></i>
                <span class="fw-bold small">Online Shopping &amp; Sales Agreement</span>
            </span>
            <h1 class="section-title mt-3">Terms of <span class="gradient-text">Service</span></h1>
            <p class="section-subtitle text-muted mx-auto" style="max-width: 620px;">
                Please read these terms carefully before using our platform or placing orders. They set out your legal rights and obligations as a consumer under UK law.
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="glass-card p-4 p-md-5 shadow-lg rounded-4" style="backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.08);">

                    <div class="d-flex align-items-center justify-content-between border-bottom pb-4 mb-4 text-muted small flex-wrap gap-2">
                        <span><strong>Last Updated:</strong> 28 May 2026</span>
                        <span><strong>Jurisdiction:</strong> United Kingdom (England, Wales, Scotland, Northern Ireland)</span>
                        <span><strong>Version:</strong> 1.0</span>
                    </div>

                    <div class="privacy-sections" style="line-height: 1.8; color: rgba(255,255,255,0.85);">

                        {{-- Section 1 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>1. About These Terms &amp; Platform Ownership</h4>
                            <p>
                                These Terms of Service govern your use of the Premier Shop online platform and constitute the legal agreement between you and Premier Shop when you purchase goods from us. They are governed by <strong>English law</strong> and comply with the <strong>Electronic Commerce (EC Directive) Regulations 2002</strong>.
                            </p>
                            <p>
                                By accessing, creating an account on, or purchasing from Premier Shop, you agree to be bound by these terms. If you do not agree, you must not use our website. We reserve the right to update these terms; the current version is always published at this URL with its effective date.
                            </p>
                        </div>

                        {{-- Section 2 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-person-badge me-2 text-primary"></i>2. Registration &amp; Account Security</h4>
                            <p>To place orders, view order tracking, or earn loyalty benefits, you must register a customer account. By registering, you confirm that:</p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>Eligibility:</strong> You are a resident of the United Kingdom and are at least <strong>18 years old</strong>. We collect your Date of Birth at registration to verify eligibility for age-restricted products. Persons under 18 must not register.</li>
                                <li class="mb-2"><strong>Secure Passwords:</strong> To protect your account, we enforce strong password criteria (minimum 12 characters, requiring uppercase, lowercase, numbers, and symbols).</li>
                                <li class="mb-2"><strong>OTP Email Verification:</strong> Registration requires successful One-Time Password (OTP) verification sent to your email address to authenticate your identity.</li>
                                <li class="mb-2"><strong>Accuracy:</strong> You agree to provide current, accurate contact and shipping details, including a valid UK delivery address and phone number for courier dispatch.</li>
                                <li class="mb-2"><strong>Account Security:</strong> You are responsible for maintaining the confidentiality of your login credentials and for all activities that occur under your account. Notify us immediately of any unauthorised access.</li>
                            </ul>
                        </div>

                        {{-- Section 3 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-cart-check me-2 text-primary"></i>3. Products, Pricing &amp; VAT</h4>
                            <p>All items offered for sale are subject to availability.</p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>VAT:</strong> All prices displayed on our website are <strong>inclusive of Value Added Tax (VAT)</strong> at the applicable UK standard rate, where VAT applies.</li>
                                <li class="mb-2"><strong>Pricing Errors:</strong> We take reasonable care to ensure correct pricing. In the event of an obvious pricing error, we reserve the right to cancel the order and issue a full refund before dispatch.</li>
                                <li class="mb-2"><strong>Product Descriptions:</strong> We take all reasonable steps to ensure product descriptions, specifications, and images are accurate. However, colours may vary slightly depending on your display settings.</li>
                                <li class="mb-2"><strong>Contract Formation:</strong> A legally binding contract of sale is formed only when we successfully process your checkout payment and issue a digital order invoice. An acknowledgement email confirming receipt of your order does not constitute acceptance.</li>
                                <li class="mb-2"><strong>Stock Reservation:</strong> During checkout, stock is temporarily reserved via a timer. If the timer expires before payment completion, reserved items are released back to general availability.</li>
                            </ul>
                        </div>

                        {{-- Section 4 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-truck me-2 text-primary"></i>4. Distance-Based Shipping &amp; Surcharge Policy</h4>
                            <p>Shipping costs are calculated dynamically using physical route metrics, mapping your delivery address against our depot coordinates:</p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>Driving Distance Calculation:</strong> The driving distance in miles is computed via the Google Maps Distance Matrix API at the time of checkout.</li>
                                <li class="mb-2"><strong>Free Shipping Threshold:</strong> Automatically applied when your cart subtotal meets or exceeds the store-configured minimum (displayed at checkout).</li>
                                <li class="mb-2"><strong>Free Local Delivery Radius:</strong> Deliveries within the configured local radius (displayed in the shipping policy at checkout) are free of charge.</li>
                                <li class="mb-2"><strong>Distance &amp; Weight Surcharges:</strong> Outside the free delivery limits, charges comprise a base fee plus a per-mile rate for additional miles, and a per-kilogram surcharge based on the aggregate weight of your order.</li>
                                <li class="mb-2"><strong>Fallback Flat Rate:</strong> If our distance calculation cannot resolve your address, a standard flat-rate shipping fee (shown at checkout) will be applied to allow checkout to proceed.</li>
                            </ul>
                            <p class="mt-2 small text-muted">The full breakdown of any shipping fees applicable to your order is always shown before you confirm payment, in accordance with the Consumer Contracts Regulations 2013.</p>
                        </div>

                        {{-- Section 5 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-star-fill me-2 text-primary"></i>5. Loyalty Points &amp; Coupon Codes</h4>
                            <p>We operate an optional Loyalty Rewards Programme and Coupon Hub subject to the following rules:</p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>Earning Points:</strong> Customers earn reward points per £1.00 spent on qualifying purchases at the rate configured in system settings (displayed in your account dashboard).</li>
                                <li class="mb-2"><strong>VIP Tiers:</strong> Bronze, Silver, Gold, and Platinum Legend statuses are calculated from your accumulated lifetime points and provide visual recognition milestones.</li>
                                <li class="mb-2"><strong>Redemption:</strong> Points have <strong>no monetary value</strong>, cannot be transferred, sold, or exchanged for cash, and are redeemable only as checkout discounts at the rate defined in settings (e.g., 100 points = £1.00).</li>
                                <li class="mb-2"><strong>Expiry:</strong> Points may expire if your account is inactive. Current expiry terms are displayed in your rewards dashboard.</li>
                                <li class="mb-2"><strong>Coupon Codes:</strong> Only one coupon may be applied per order. Coupons are validated against active dates, minimum purchase subtotals, and usage limits. Expired or invalid codes will not be accepted.</li>
                            </ul>
                        </div>

                        {{-- Section 6 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-arrow-left-right me-2 text-primary"></i>6. UK Consumer Rights &amp; Returns</h4>
                            <p>As an online consumer in the UK, your purchases are protected under the <strong>Consumer Rights Act 2015</strong> and the <strong>Consumer Contracts (Information, Cancellation and Additional Charges) Regulations 2013</strong>:</p>
                            <div class="alert alert-dark bg-soft-primary border-0 rounded-3 p-3 mt-3 text-white small">
                                <h6 class="fw-bold mb-2"><i class="bi bi-clock-history me-1 text-primary"></i>14-Day Right to Cancel (Cooling-Off Period)</h6>
                                <p class="mb-0">
                                    You have the legal right to cancel your online order without giving any reason within <strong>14 days</strong> from the day you (or a nominated third party, other than the carrier) take physical possession of the goods. To exercise this right, you must clearly inform us of your decision to cancel before the 14-day period expires.
                                </p>
                            </div>
                            <ul class="ps-3 mt-3" style="list-style-type: square;">
                                <li class="mb-2"><strong>Cancellation Before Dispatch:</strong> You may cancel your order for a full refund at any time before it is dispatched for delivery by contacting us immediately.</li>
                                <li class="mb-2"><strong>Submitting a Return:</strong> After delivery, initiate a return request from your customer dashboard (Orders → Return Request) within the applicable period.</li>
                                <li class="mb-2"><strong>Condition of Returned Goods:</strong> Returned items must be in unused, original condition and in original packaging. You are responsible for return postage costs unless the goods are faulty or misdescribed.</li>
                                <li class="mb-2"><strong>Faulty or Misdescribed Goods:</strong> Under the Consumer Rights Act 2015, if goods supplied are not of satisfactory quality, fit for purpose, or as described, you have a <strong>30-day short-term right to reject</strong> them for a full refund.</li>
                                <li class="mb-2"><strong>Refund Processing:</strong> Approved refunds will be issued to your original payment method within <strong>14 days</strong> of our depot receiving and inspecting the returned goods.</li>
                                <li class="mb-2"><strong>Non-Returnable Items:</strong> Certain items cannot be returned after they have been unsealed (e.g., software, sealed hygiene products, perishables). These will be clearly indicated on the product listing.</li>
                            </ul>
                        </div>

                        {{-- Section 7 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-geo-alt me-2 text-primary"></i>7. Deliveries, Drivers &amp; Proof-of-Delivery</h4>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>Delivery Attempts:</strong> Our authorised drivers will attempt delivery to the address provided. If delivery cannot be completed, items are returned to our depot and we will contact you to rearrange.</li>
                                <li class="mb-2"><strong>Proof-of-Delivery (PoD):</strong> Upon successful delivery, the driver uploads a photographic Proof-of-Delivery of the parcel at your premises. This record is stored in your account and may be used for dispute resolution.</li>
                                <li class="mb-2"><strong>Live Tracking:</strong> The delivery route map shown during active dispatch is for visual guidance only. Actual delivery times may vary due to traffic, weather, or operational factors.</li>
                                <li class="mb-2"><strong>Delivery Risk:</strong> Risk of loss or damage passes to you when the goods are delivered to the address you specified, or when physically received by you or a nominated person.</li>
                            </ul>
                        </div>

                        {{-- Section 8 — Reviews --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-chat-square-text me-2 text-primary"></i>8. Product Reviews &amp; User-Generated Content</h4>
                            <p>Verified purchasers may submit product reviews and star ratings. By submitting a review, you:</p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2">Grant Premier Shop a non-exclusive, royalty-free licence to display, reproduce, and moderate your review on the platform.</li>
                                <li class="mb-2">Confirm the review is your own honest opinion based on your genuine experience with the product.</li>
                                <li class="mb-2">Agree not to submit content that is defamatory, offensive, misleading, infringing of third-party rights, or in violation of any applicable law.</li>
                            </ul>
                            <p>We reserve the right to remove reviews that violate these guidelines or that we reasonably believe are fraudulent. Aggregate anonymised ratings may be retained after account deletion.</p>
                        </div>

                        {{-- Section 9 — IP --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-c-circle me-2 text-primary"></i>9. Intellectual Property</h4>
                            <p>
                                All content on the Premier Shop platform — including but not limited to logos, graphics, page layouts, text, software code, and product images (excluding images supplied by manufacturers or third parties) — is the intellectual property of Premier Shop or its licensors and is protected by UK and international copyright law.
                            </p>
                            <p>
                                You may not reproduce, distribute, modify, or create derivative works from any part of our platform content without our prior written consent, except as permitted by applicable law (e.g., fair dealing under the Copyright, Designs and Patents Act 1988).
                            </p>
                        </div>

                        {{-- Section 10 — Acceptable Use --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-slash-circle me-2 text-primary"></i>10. Acceptable Use &amp; Prohibited Conduct</h4>
                            <p>You agree not to use Premier Shop to:</p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2">Violate any applicable UK or international law or regulation.</li>
                                <li class="mb-2">Transmit fraudulent, false, or misleading information.</li>
                                <li class="mb-2">Attempt to gain unauthorised access to our systems, accounts, or servers.</li>
                                <li class="mb-2">Place orders using stolen payment credentials or false identities.</li>
                                <li class="mb-2">Abuse the returns system, loyalty programme, or coupon scheme through fraudulent claims.</li>
                                <li class="mb-2">Use automated tools, bots, or scripts to scrape, copy, or interact with our platform in an unauthorised manner.</li>
                            </ul>
                            <p>Breach of these conditions may result in immediate account suspension or termination — see Section 11.</p>
                        </div>

                        {{-- Section 11 — Account Suspension --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-person-dash me-2 text-primary"></i>11. Account Suspension &amp; Termination</h4>
                            <p>
                                We reserve the right to suspend or permanently terminate your account, at our sole discretion, where we have reasonable grounds to believe you have breached these Terms, engaged in fraudulent activity, or where required by law.
                            </p>
                            <p>
                                You may delete your own account at any time via your account settings. Where an account is terminated, any outstanding orders that are in progress will be completed or refunded in accordance with our normal policies. Loyalty points and coupons associated with a terminated account will be forfeited.
                            </p>
                        </div>

                        {{-- Section 12 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-exclamation-triangle me-2 text-primary"></i>12. Limitation of Liability</h4>
                            <p>Nothing in these terms excludes or limits our liability for:</p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2">Death or personal injury caused by our negligence;</li>
                                <li class="mb-2">Fraud or fraudulent misrepresentation;</li>
                                <li class="mb-2">Breach of your statutory rights as a consumer under the Consumer Rights Act 2015; or</li>
                                <li class="mb-2">Any other matter for which it would be unlawful for us to exclude or limit liability.</li>
                            </ul>
                            <p>
                                Subject to the above, our total liability to you for any losses arising from your use of our platform or the purchase of goods will not exceed the total value of your order(s) giving rise to the claim.
                            </p>
                            <p>
                                We do not warrant that the platform will be uninterrupted, error-free, or free of viruses. We will not be liable for any loss of data, profits, or goodwill arising from platform downtime or technical issues beyond our reasonable control.
                            </p>
                        </div>

                        {{-- Section 13 — Complaints --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-envelope-open me-2 text-primary"></i>13. Complaints Procedure</h4>
                            <p>
                                If you have a complaint about our products, services, or these terms, please contact us first so we can try to resolve it:
                            </p>
                            <div class="alert alert-dark bg-soft-primary border-0 rounded-3 p-3 mt-3 text-white small d-flex align-items-start gap-2">
                                <i class="bi bi-envelope-fill text-primary mt-1 flex-shrink-0"></i>
                                <div>
                                    <strong>Contact for Complaints:</strong><br>
                                    Email: {{ \App\Models\Setting::get('contact_email', 'support@premiershop.com') }}<br>
                                    Address: {{ \App\Models\Setting::get('origin_address', 'London, United Kingdom') }}<br>
                                    We aim to acknowledge complaints within <strong>2 business days</strong> and resolve them within <strong>14 days</strong>.
                                </div>
                            </div>
                            <p class="mt-3 small text-muted">
                                If we are unable to resolve your complaint to your satisfaction, you may refer the matter to an approved Alternative Dispute Resolution (ADR) provider or contact the <strong>Citizens Advice consumer helpline</strong> on 0808 223 1133. Online purchases made from businesses in the UK may also be escalated via
                                <a href="https://www.resolver.co.uk" target="_blank" rel="noopener noreferrer" class="text-primary">Resolver.co.uk</a>.
                            </p>
                        </div>

                        {{-- Section 14 --}}
                        <div class="mb-4">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-gavel me-2 text-primary"></i>14. Governing Law &amp; Jurisdiction</h4>
                            <p>
                                These Terms of Service, their subject matter, and formation are governed by <strong>English law</strong>. You and we both agree that the <strong>courts of England and Wales</strong> have exclusive jurisdiction to settle any dispute or claim, except that:
                            </p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2">If you are a resident of <strong>Northern Ireland</strong>, you may also bring proceedings in the courts of Northern Ireland.</li>
                                <li class="mb-2">If you are a resident of <strong>Scotland</strong>, you may also bring proceedings in the courts of Scotland.</li>
                            </ul>
                            <p class="mt-2 small text-muted">
                                These terms do not affect your statutory rights as a UK consumer, which are always available to you regardless of any terms to the contrary.
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
