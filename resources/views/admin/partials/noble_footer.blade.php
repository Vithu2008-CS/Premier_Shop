{{--
    admin/partials/noble_footer.blade.php — Admin layout footer
    =============================================================
    Minimal footer bar: copyright year (dynamic) and shop name link.
    Included by layouts/admin_noble.blade.php.
--}}
<footer class="footer d-flex align-items-center justify-content-start px-4">
  <p class="text-muted mb-0" style="margin-left: 0 !important; margin-right: auto !important; text-align: left !important;">Copyright © {{ date('Y') }} <a href="{{ url('/') }}" target="_blank">Premier Shop</a>. All rights reserved</p>
</footer>
