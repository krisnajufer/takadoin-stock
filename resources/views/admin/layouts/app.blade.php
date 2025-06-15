<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Takadoin - @yield('title')</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.png" sizes="16x16">

    @include('admin.partials.style')
    @stack('custom-style')
</head>

<body>
    @include('admin.partials.sidebar')

    <main class="dashboard-main">
        @include('admin.partials.navbar')

        <div class="dashboard-main-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
                <h6 class="fw-semibold mb-0">@yield('title')</h6>
                @stack('custom-button')
            </div>

            @yield('content')
        </div>

        @include('admin.partials.footer')
    </main>

    @include('admin.partials.script')
    @stack('custom-script')

</body>

</html>
