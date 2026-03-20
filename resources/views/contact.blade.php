@extends('layouts.app')
@section('title', 'Contact Us — Premier Shop')

@section('content')
<section class="contact-page">
    {{-- Animated Background Orbs --}}
    <div class="contact-bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="container position-relative" style="z-index: 2;">
        {{-- Header --}}
        <div class="text-center mb-5 reveal-3d">
            <span class="glass-pill mb-3 d-inline-flex">
                <i class="bi bi-envelope-heart-fill text-primary"></i>
                <span class="fw-bold small">We'd love to hear from you</span>
            </span>
            <h1 class="section-title mt-3">Get In <span class="gradient-text">Touch</span></h1>
            <p class="section-subtitle text-muted mx-auto" style="max-width: 560px;">
                Have a question, feedback, or need assistance? Our team is here to help you with anything you need.
            </p>
        </div>

        <div class="row g-4 g-lg-5">
            {{-- Contact Form --}}
            <div class="col-lg-7 reveal-slide-left">
                <div class="contact-form-card glass-card">
                    <div class="contact-form-header">
                        <h4 class="fw-bold mb-1"><i class="bi bi-chat-dots me-2 text-primary"></i>Send us a message</h4>
                        <p class="text-muted small mb-0">Fill out the form below and we'll respond within 24 hours.</p>
                    </div>
                    <form action="{{ route('contact.send') }}" method="POST" class="contact-form">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating-custom">
                                    <input type="text" name="name" id="contact-name" class="form-control @error('name') is-invalid @enderror" placeholder="Your Name" value="{{ old('name') }}" required>
                                    <label for="contact-name"><i class="bi bi-person me-1"></i>Your Name</label>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-custom">
                                    <input type="email" name="email" id="contact-email" class="form-control @error('email') is-invalid @enderror" placeholder="Email Address" value="{{ old('email') }}" required>
                                    <label for="contact-email"><i class="bi bi-envelope me-1"></i>Email Address</label>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-custom">
                                    <input type="tel" name="phone" id="contact-phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Phone (Optional)" value="{{ old('phone') }}">
                                    <label for="contact-phone"><i class="bi bi-telephone me-1"></i>Phone (Optional)</label>
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-custom">
                                    <select name="subject" id="contact-subject" class="form-select @error('subject') is-invalid @enderror" required>
                                        <option value="">Choose a topic</option>
                                        <option value="General Inquiry" {{ old('subject') == 'General Inquiry' ? 'selected' : '' }}>General Inquiry</option>
                                        <option value="Order Support" {{ old('subject') == 'Order Support' ? 'selected' : '' }}>Order Support</option>
                                        <option value="Product Question" {{ old('subject') == 'Product Question' ? 'selected' : '' }}>Product Question</option>
                                        <option value="Returns & Refunds" {{ old('subject') == 'Returns & Refunds' ? 'selected' : '' }}>Returns & Refunds</option>
                                        <option value="Partnership" {{ old('subject') == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                        <option value="Other" {{ old('subject') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <label for="contact-subject"><i class="bi bi-tag me-1"></i>Subject</label>
                                    @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating-custom">
                                    <textarea name="message" id="contact-message" class="form-control @error('message') is-invalid @enderror" rows="5" placeholder="Your message..." style="min-height: 140px;" required>{{ old('message') }}</textarea>
                                    <label for="contact-message"><i class="bi bi-pencil me-1"></i>Your Message</label>
                                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-contact-submit w-100">
                                    <i class="bi bi-send-fill me-2"></i>Send Message
                                    <span class="btn-shine"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Contact Info --}}
            <div class="col-lg-5 reveal-slide-right">
                <div class="d-flex flex-column gap-3">
                    {{-- Address Card --}}
                    <div class="contact-info-card">
                        <div class="contact-info-icon" style="background: rgba(108, 92, 231, 0.12); color: #6C5CE7;">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Visit Our Store</h6>
                            <p class="text-muted mb-0 small">London, United Kingdom<br>Open Mon–Sat, 9 AM – 6 PM</p>
                        </div>
                    </div>

                    {{-- Phone Card --}}
                    <div class="contact-info-card">
                        <div class="contact-info-icon" style="background: rgba(0, 206, 201, 0.12); color: #00CEC9;">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Call Us</h6>
                            <p class="text-muted mb-0 small">+44 770 000 0000<br>Available 24/7 for support</p>
                        </div>
                    </div>

                    {{-- Email Card --}}
                    <div class="contact-info-card">
                        <div class="contact-info-icon" style="background: rgba(0, 184, 148, 0.12); color: #00B894;">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Email Us</h6>
                            <p class="text-muted mb-0 small">info@premiershop.com<br>We reply within 24 hours</p>
                        </div>
                    </div>

                    {{-- Social Card --}}
                    <div class="contact-info-card">
                        <div class="contact-info-icon" style="background: rgba(253, 203, 110, 0.12); color: #FDCB6E;">
                            <i class="bi bi-share-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Follow Us</h6>
                            <div class="d-flex gap-2 mt-1">
                                <a href="#" class="contact-social-btn"><i class="bi bi-facebook"></i></a>
                                <a href="#" class="contact-social-btn"><i class="bi bi-instagram"></i></a>
                                <a href="#" class="contact-social-btn"><i class="bi bi-twitter-x"></i></a>
                                <a href="#" class="contact-social-btn"><i class="bi bi-tiktok"></i></a>
                            </div>
                        </div>
                    </div>

                    {{-- Map Placeholder --}}
                    <div class="contact-map-card">
                        <div class="contact-map-overlay">
                            <i class="bi bi-pin-map-fill"></i>
                            <span>London, UK</span>
                        </div>
                        <img src="https://api.mapbox.com/styles/v1/mapbox/dark-v11/static/-0.1276,51.5074,12,0/600x300@2x?access_token=placeholder" 
                             alt="Store location map" 
                             class="w-100 rounded-4"
                             style="height: 180px; object-fit: cover; filter: brightness(0.7) saturate(0.8);"
                             onerror="this.parentElement.style.background='linear-gradient(135deg, #1a1a2e, #16213e)'; this.style.display='none';">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
