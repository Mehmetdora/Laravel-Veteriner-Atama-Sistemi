@if (!empty(session('success')))
    <div class = "alert alert-success "  role="alert">
        @if (is_string(session('success')))
            {{ session('success') }}
        @else
            @foreach (session('success') as $message)
                {{ $message }} <br>
            @endforeach
        @endif
    </div>
@endif

@if (!empty(session('error')))
    <div class = "alert alert-danger " role="alert">
        @if (is_string(session('error')))
            {{ session('error') }}
        @else
            @foreach (session('error') as $message)
                {{ $message }} <br>
            @endforeach
        @endif
    </div>
@endif

@if (!empty(session('payment-error')))
    <div class = "alert alert-error "  role="alert">
        @if (is_string(session('payment-error')))
            {{ session('payment-error') }}
        @else
            @foreach (session('payment-error') as $message)
                {{ $message }} <br>
            @endforeach
        @endif
    </div>
@endif

@if (!empty(session('warning')))
    <div class = "alert alert-warning "  role="alert">
        @if (is_string(session('warning')))
            {{ session('warning') }}
        @else
            @foreach (session('warning') as $message)
                {{ $message }} <br>
            @endforeach
        @endif
    </div>
@endif

@if (!empty(session('info')))
    <div class = "alert alert-info "  role="alert">
        @if (is_string(session('info')))
            {{ session('info') }}
        @else
            @foreach (session('info') as $message)
                {{ $message }} <br>
            @endforeach
        @endif
    </div>
@endif

@if (!empty(session('secondary')))
    <div class = "alert alert-secondary " role="alert">
        {{ session('secondary') }}
    </div>
@endif

@if (!empty(session('primary')))
    <div class = "alert alert-primary "  role="alert">
        {{ session('primary') }}
    </div>
@endif

@if (!empty(session('light')))
    <div class = "alert alert-light "  role="alert">
        {{ session('light') }}
    </div>
@endif
