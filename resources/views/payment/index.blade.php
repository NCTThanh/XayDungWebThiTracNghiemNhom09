@extends('layout')
@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h2>💳 Chọn Phương Thức Thanh Toán</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="/dashboard" class="btn btn-secondary">← Quay Lại</a>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger mt-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger mt-3">
        {{ session('error') }}
    </div>
    @endif

    <div class="row mt-4">
        <!-- VNPay -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm payment-option" data-gateway="vnpay">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; margin-bottom: 15px;">🇻🇳</div>
                    <h5 class="card-title">VNPay</h5>
                    <p class="card-text text-muted">Thanh toán bằng thẻ ngân hàng hoặc ví điện tử</p>
                    <small class="text-muted">Phí: Miễn phí</small>
                    <div class="mt-3">
                        <button class="btn btn-primary w-100 pay-btn" data-gateway="vnpay">
                            <i class="fas fa-credit-card"></i> Chọn VNPay
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Momo -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm payment-option" data-gateway="momo">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; margin-bottom: 15px;">💰</div>
                    <h5 class="card-title">Momo</h5>
                    <p class="card-text text-muted">Ví điện tử Momo - Thanh toán nhanh</p>
                    <small class="text-muted">Phí: Miễn phí</small>
                    <div class="mt-3">
                        <button class="btn btn-info w-100 pay-btn" data-gateway="momo">
                            <i class="fas fa-mobile-alt"></i> Chọn Momo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stripe -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm payment-option" data-gateway="stripe">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; margin-bottom: 15px;">💳</div>
                    <h5 class="card-title">Stripe</h5>
                    <p class="card-text text-muted">Thanh toán quốc tế an toàn</p>
                    <small class="text-muted">Phí: 2.9% + 0.3 USD</small>
                    <div class="mt-3">
                        <button class="btn btn-warning w-100 pay-btn" data-gateway="stripe">
                            <i class="fas fa-globe"></i> Chọn Stripe
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- PayPal -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm payment-option" data-gateway="paypal">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; margin-bottom: 15px;">🅿️</div>
                    <h5 class="card-title">PayPal</h5>
                    <p class="card-text text-muted">Tài khoản PayPal hoân toàn an toàn</p>
                    <small class="text-muted">Phí: 3.49% + 0.49 USD</small>
                    <div class="mt-3">
                        <button class="btn btn-danger w-100 pay-btn" data-gateway="paypal">
                            <i class="fas fa-paypal"></i> Chọn PayPal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2Checkout -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm payment-option" data-gateway="2checkout">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; margin-bottom: 15px;">🌐</div>
                    <h5 class="card-title">Altapay</h5>
                    <p class="card-text text-muted">Thanh toán toàn cầu, 195+ quốc gia</p>
                    <small class="text-muted">Phí: Linh hoạt theo đơn vị</small>
                    <div class="mt-3">
                        <button class="btn btn-success w-100 pay-btn" data-gateway="2checkout">
                            <i class="fas fa-shopping-cart"></i> Chọn Altapay
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form ẩn để submit -->
    <form id="paymentForm" method="POST" action="/initiate-payment" style="display: none;">
        @csrf
        <input type="hidden" id="gateway" name="gateway">
        <input type="hidden" id="amount" name="amount" value="{{ $amount ?? 0 }}">
        <input type="hidden" id="type" name="type" value="{{ $type ?? 'subscription' }}">
        <input type="hidden" id="item_id" name="item_id" value="{{ $item_id ?? '' }}">
    </form>
</div>

<script>
    document.querySelectorAll('.pay-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const gateway = this.getAttribute('data-gateway');
            document.getElementById('gateway').value = gateway;
            document.getElementById('paymentForm').submit();
        });
    });

    // Highlight card on hover
    document.querySelectorAll('.payment-option').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 0 1rem rgba(0,0,0,0.1)';
        });
    });
</script>

<style>
    .payment-option {
        border-radius: 10px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .payment-option:hover {
        border-color: #007bff;
    }
    .btn {
        border-radius: 5px;
        font-weight: 500;
    }
</style>
@endsection
