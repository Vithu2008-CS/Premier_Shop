{{--
    terms.blade.php — Terms of Service
    ==================================
    Publicly accessible page detailing e-commerce terms, pricing, deliveries,
    distance shipping calculations, loyalty points, UK consumer rights, and returns.
--}}
@extends('layouts.app')
@section('title', 'Terms of Service — Premier Shop')

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
                <i class="bi bi-file-earmark-text-fill text-primary"></i>
                <span class="fw-bold small">Online Shopping & Sales Agreement</span>
            </span>
            <h1 class="section-title mt-3">Terms of <span class="gradient-text">Service</span></h1>
            <p class="section-subtitle text-muted mx-auto" style="max-width: 600px;">
                Please read these terms carefully before using our platform or placing orders. They outline your legal rights and obligations as a consumer under UK law.
            </p>
        </div>

        {{-- Main Content --}}
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="glass-card p-4 p-md-5 shadow-lg rounded-4" style="backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.08);">
                    
                    {{-- Effective Date --}}
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-4 mb-4 text-muted small">
                        <span><strong>Last Updated:</strong> May 28, 2026</span>
                        <span><strong>Applicable Jurisdiction:</strong> United Kingdom (England, Wales, Scotland, Northern Ireland)</span>
                    </div>

                    <div class="privacy-sections" style="line-height: 1.7; color: rgba(255,255,255,0.85);">
                        
                        {{-- Section 1 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>1. Terms and Platform Ownership</h4>
                            <p>
                                These Terms of Service govern your use of the Premier Shop online platform and set out the legal terms that apply when we sell goods to you. 
                            </p>
                            <p>
                                By accessing, creating an account on, or purchasing from Premier Shop, you agree to be bound by these terms. If you do not agree to these terms, you must not use our website.
                            </p>
                        </div>

                        {{-- Section 2 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-person-badge me-2 text-primary"></i>2. Registration & Account Security</h4>
                            <p>
                                To place orders, view tracking maps, or earn loyalty benefits, you must register a customer account. By registering:
                            </p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>Eligibility:</strong> You must be a resident of the United Kingdom and at least 18 years old. You must provide a valid Date of Birth (DOB) which we verify upon registration to check compliance for age-restricted products.</li>
                                <li class="mb-2"><strong>Secure Passwords:</strong> To protect your personal data, we enforce strict password criteria (minimum 12 characters, requiring uppercase/lowercase letters, numbers, symbols, and checking against compromised credential databases).</li>
                                <li class="mb-2"><strong>OTP Verification:</strong> Registration requires successful One-Time Password (OTP) verification sent to your email to authenticate your identity.</li>
                                <li class="mb-2"><strong>Accuracy:</strong> You agree to provide current, accurate shipping and contact details (including a UK phone number for courier dispatch).</li>
                            </ul>
                        </div>

                        {{-- Section 3 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-cart-check me-2 text-primary"></i>3. Product sales, Pricing, & VAT</h4>
                            <p>
                                All items offered for sale are subject to availability. 
                            </p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>VAT:</strong> In accordance with UK tax laws, all prices displayed on our website are inclusive of Value Added Tax (VAT) at the standard UK rate, where applicable.</li>
                                <li class="mb-2"><strong>Contract Formation:</strong> A legally binding contract is only formed when we successfully process your checkout purchase and issue your digital order invoice receipt. We reserve the right to cancel orders and refund you fully in cases of stock shortages or obvious pricing errors.</li>
                            </ul>
                        </div>

                        {{-- Section 4 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-truck me-2 text-primary"></i>4. Distance-Based Shipping & Surcharge Policies</h4>
                            <p>
                                We calculate shipping costs dynamically using physical route metrics mapping your delivery address against our central store coordinates:
                            </p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>The Driving Matrix:</strong> Driving distance in miles is computed dynamically via the Google Maps Distance Matrix API.</li>
                                <li class="mb-2"><strong>Delivery Charges Structure:</strong>
                                    - **Free Shipping Threshold:** Triggered automatically if your cart subtotal meets or exceeds the store threshold (e.g. £50.00 or £100.00 as configured by our administrators).
                                    - **Free Local Delivery Radius:** Deliveries within the configured free local delivery radius (e.g., 5.0 miles) are free.
                                    - **Distance & Weight Surcharges:** Outside the free delivery limits, delivery charges comprise a base fee + a per-mile surcharge for extra miles, plus a per-kilogram (kg) weight surcharge based on aggregate product weights.
                                </li>
                                <li class="mb-2"><strong>Fallback Shipping Fee:** If distance matrix calls cannot resolve your address, a standard flat-rate shipping fee (e.g., £5.99) is applied to ensure checkout completion.</li>
                            </ul>
                        </div>

                        {{-- Section 5 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-star-fill me-2 text-primary"></i>5. Loyalty Points & Active Coupon Tickets</h4>
                            <p>
                                We operate an optional Loyalty Rewards Programme and Coupon Hub governed by these rules:
                            </p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>Earning Points:</strong> Customers earn a set number of points per £1.00 spent on qualifying purchases, as configured in the system settings.</li>
                                <li class="mb-2"><strong>VIP Tiers:</strong> VIP statuses (Bronze, Silver, Gold, Platinum Legend) are calculated based on your accumulated lifetime points, granting visual milestones.</li>
                                <li class="mb-2"><strong>Point Value:</strong> Points have no physical cash value, cannot be transferred, and are only redeemable as invoice discounts on checkout at the rate defined in System Settings (e.g., 100 points = £1.00).</li>
                                <li class="mb-2"><strong>Coupon Codes:</strong> Only one coupon code can be applied per order. Coupons must be entered before payment processing and are validated against active coupon dates, minimum purchase subtotals, and usage limits.</li>
                            </ul>
                        </div>

                        {{-- Section 6 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-arrow-left-right me-2 text-primary"></i>6. UK Consumer Rights & Return Policies</h4>
                            <p>
                                As an online consumer shopping in the UK, your purchases are fully protected under the **Consumer Rights Act 2015** and the **Consumer Contracts Regulations 2013**:
                            </p>
                            <div class="alert alert-dark bg-soft-primary border-0 rounded-3 p-3 mt-3 text-white small">
                                <h6 class="fw-bold mb-2"><i class="bi bi-clock-history me-1 text-primary"></i> 14-Day Right to Cancel (Cooling-off Period)</h6>
                                <p class="mb-0">
                                    You have the legal right to cancel your online purchase and request a full refund without giving any reason within **14 days** from the day you (or a third party nominated by you) take physical possession of the goods. 
                                </p>
                            </div>
                            <ul class="ps-3 mt-3" style="list-style-type: square;">
                                <li class="mb-2"><strong>Submitting Returns:</strong> You can submit a return request inside your customer dashboard (`/orders/{order}/returns/create`) on any successfully delivered order, provided there is no existing return pending.</li>
                                <li class="mb-2"><strong>Condition of Goods:</strong> Returned goods must be unused, in their original packaging, and in a fully resaleable condition. You are responsible for the cost of returning the goods to our depot unless they are faulty.</li>
                                <li class="mb-2"><strong>Faulty or Misdescribed Goods:</strong> Under the Consumer Rights Act 2015, if any goods we supply are not of satisfactory quality, fit for purpose, or as described, you have a **30-day short-term right to reject** them and receive a full refund.</li>
                                <li class="mb-2"><strong>Refund Processing:</strong> Approved refunds will be issued to your original payment method within 14 days of our depot receiving the returned items.</li>
                            </ul>
                        </div>

                        {{-- Section 7 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-geo-alt me-2 text-primary"></i>7. Deliveries, Drivers, & Proof-of-Delivery</h4>
                            <p>
                                Deliveries are carried out by our authorized drivers or couriers:
                            </p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2"><strong>Delivery Attempts:</strong> Drivers will attempt delivery to the address provided. If no one is available and no safe place/neighbour is specified, the items will be returned to our depot, and we will contact you to arrange redelivery.</li>
                                <li class="mb-2"><strong>Proof-of-Delivery (PoD):</strong> Upon successful delivery, the driver uploads a photographic Proof-of-Delivery displaying the delivered parcel at your premises, which is archived and visible in your customer account.</li>
                                <li class="mb-2"><strong>Live Map Tracking:</strong> The dynamic Leaflet routes shown during active delivery states are for visual convenience and estimation only. Traffic and street conditions may affect active times.</li>
                            </ul>
                        </div>

                        {{-- Section 8 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-exclamation-triangle me-2 text-primary"></i>8. Limitation of Liability</h4>
                            <p>
                                Nothing in these terms excludes or limits our liability for:
                            </p>
                            <ul class="ps-3 mt-2" style="list-style-type: square;">
                                <li class="mb-2">Death or personal injury caused by our negligence;</li>
                                <li class="mb-2">Fraud or fraudulent misrepresentation;</li>
                                <li class="mb-2">Breach of your legal rights as a consumer under the Consumer Rights Act 2015 (including supplying goods of satisfactory quality, fit for purpose, and matching descriptions); or</li>
                                <li class="mb-2">Any other matter for which it would be unlawful for us to exclude or limit liability.</li>
                            </ul>
                        </div>

                        {{-- Section 9 --}}
                        <div class="mb-5">
                            <h4 class="fw-bold text-white mb-3"><i class="bi bi-gavel me-2 text-primary"></i>9. Governing Law and Jurisdiction</h4>
                            <p>
                                These Terms of Service, their subject matter, and their formation are governed by **English Law**. You and we both agree that the **courts of England and Wales** will have exclusive jurisdiction, except that if you are a resident of Northern Ireland you may also bring proceedings in Northern Ireland, and if you are a resident of Scotland you may also bring proceedings in Scotland.
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
