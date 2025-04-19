<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Takadoin - {{ $title ?? 'Dashboard' }}</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.png" sizes="16x16">

    <x-admin.partials.style />
    {{ $customStyle ?? '' }}
</head>

<body>
    <x-admin.partials.sidebar />

    <main class="dashboard-main">
        <x-admin.partials.navbar />

        <div class="dashboard-main-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
                <h6 class="fw-semibold mb-0">{{ $title ?? 'Dashboard' }}</h6>
                {{ $customButton ?? '' }}
            </div>

            {{ $slot }}
        </div>

        <x-admin.partials.footer />
    </main>

    <x-admin.partials.script />
    {{ $customScript ?? '' }}

</body>

</html>
