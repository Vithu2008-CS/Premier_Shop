{{--
    admin/delivery_zones/_form.blade.php — Shared zone form fields.
    Expects optional $zone (DeliveryZone) when editing.
    Fields: name, min_miles, max_miles, is_free, free_over_amount, delivery_fee.
--}}
@php($zone = $zone ?? null)

<div class="card border-0 shadow-sm theme-card-bg" style="border-radius:18px !important;">
    <div class="card-body p-4 p-md-5">
        <div class="d-flex align-items-center mb-4 pb-2 border-bottom-subtle">
            <h5 class="card-title mb-0 font-weight-bold text-theme-dark-bold d-flex align-items-center" style="font-family:'Outfit',sans-serif;">
                <i class="bi bi-geo-alt-fill text-primary" style="font-size:1.25rem;margin-right:8px;"></i>
                Zone Configuration
            </h5>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label small text-muted font-weight-bold">Zone Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="zone_name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $zone->name ?? '') }}" required placeholder="e.g. Local — Town Centre">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label small text-muted font-weight-bold">From (miles) <span class="text-danger">*</span></label>
                <input type="number" name="min_miles" id="min_miles" class="form-control @error('min_miles') is-invalid @enderror"
                       value="{{ old('min_miles', $zone->min_miles ?? '0') }}" step="0.01" min="0" required>
                @error('min_miles')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label small text-muted font-weight-bold">To (miles) <span class="text-danger">*</span></label>
                <input type="number" name="max_miles" id="max_miles" class="form-control @error('max_miles') is-invalid @enderror"
                       value="{{ old('max_miles', $zone->max_miles ?? '') }}" step="0.01" min="0" required placeholder="e.g. 1.5">
                @error('max_miles')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <div class="p-3 rounded-3" style="background: rgba(16,185,129,0.06); border: 1.5px dashed rgba(16,185,129,0.3);">
                    <div class="form-check mb-0">
                        <label class="form-check-label font-weight-bold text-theme-dark-bold small cursor-pointer">
                            <input type="checkbox" name="is_free" id="is_free" class="form-check-input" value="1"
                                   {{ old('is_free', ($zone->is_free ?? false) ? '1' : '') ? 'checked' : '' }}>
                            Fully free zone — every order in this radius delivers free
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label small text-muted font-weight-bold">Free Over — Minimum Order Value (£)</label>
                <input type="number" name="free_over_amount" id="free_over_amount" class="form-control @error('free_over_amount') is-invalid @enderror"
                       value="{{ old('free_over_amount', $zone->free_over_amount ?? '') }}" step="0.01" min="0" placeholder="e.g. 20.00 — leave empty for no threshold">
                @error('free_over_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label small text-muted font-weight-bold">Delivery Fee (£)</label>
                <input type="number" name="delivery_fee" id="delivery_fee" class="form-control @error('delivery_fee') is-invalid @enderror"
                       value="{{ old('delivery_fee', $zone->delivery_fee ?? '') }}" step="0.01" min="0" placeholder="e.g. 2.50 — charged when order is under the threshold">
                @error('delivery_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="p-3 rounded-3 bg-soft-primary d-flex align-items-center" style="gap:8px;">
            <i class="bi bi-lightbulb" style="font-size:1rem;"></i>
            <span class="small mb-0" id="rule_preview">Rule preview</span>
        </div>
    </div>
</div>
