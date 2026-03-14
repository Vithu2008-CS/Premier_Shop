@props([
    'topText' => '',
    'mainText' => '',
    'subMainText' => '',
    'buttonText' => '',
    'buttonLink' => '#',
    'slides' => []
])

<section class="diced-hero-container position-relative">
    <div class="container-fluid" style="max-width: 1536px;">
        <div class="row align-items-center min-vh-75 py-5">
            
            <!-- Left Content Column -->
            <div class="col-lg-6 mb-5 mb-lg-0 text-center text-lg-start z-1 relative">
                @if($topText)
                    <span class="d-block text-uppercase fw-bold mb-3 fade-in-up" 
                          style="color: var(--diced-hero-section-top-text); letter-spacing: 2px; font-size: 0.875rem;">
                        {{ $topText }}
                    </span>
                @endif
                
                @if($mainText)
                    <h1 class="display-2 fw-black mb-4 fade-in-up delay-100" style="line-height: 1.1;">
                        <span class="text-gradient-diced">{{ $mainText }}</span>
                    </h1>
                @endif
                
                <hr class="diced-separator mx-auto mx-lg-0 my-4 fade-in-up delay-200">
                
                @if($subMainText)
                    <p class="lead mb-5 fade-in-up delay-300" style="color: var(--diced-hero-section-sub-text); max-width: 600px;">
                        {{ $subMainText }}
                    </p>
                @endif
                
                @if($buttonText)
                    <div class="d-flex justify-content-center justify-content-lg-start fade-in-up delay-400">
                        <x-chronicle-button :text="$buttonText" :href="$buttonLink" />
                    </div>
                @endif
            </div>

            <!-- Right Image Grid Column -->
            <div class="col-lg-6 px-lg-5">
                <div class="row g-4 position-relative">
                    @foreach(array_reverse(array_slice($slides, 0, 4)) as $index => $slide)
                        @php
                            $cornerClasses = ['bottom-right', 'bottom-left', 'top-right', 'top-left'];
                            $cornerClass = $cornerClasses[$index] ?? '';
                            $delayClass = 'delay-' . (($index + 1) * 100);
                        @endphp
                        <div class="col-6">
                            <div class="position-relative w-100 fade-in-up {{ $delayClass }}" style="padding-bottom: 100%;">
                                <img src="{{ $slide['image'] }}" 
                                     alt="{{ $slide['title'] }}" 
                                     class="warped-image {{ $cornerClass }} shadow-lg"
                                     loading="lazy">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>
