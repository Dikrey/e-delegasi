<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================
 -->
<!-- beautify ignore:start -->
<html
    lang="id"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="{{ asset('public/sneat/') }}"
    data-template="vertical-menu-template-free"
>
<head>
    <meta charset="utf-8"/>
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>{{ config('app.name') }}</title>

    <meta name="description" content=""/>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('logo-black.png') }}"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{asset('sneat/vendor/fonts/boxicons.css')}}"/>

    <!-- Core CSS -->
    <link rel="stylesheet" class="template-customizer-core-css" href="{{asset('sneat/vendor/css/core.css')}}"/>
    <link rel="stylesheet" class="template-customizer-theme-css"
          href="{{asset('sneat/vendor/css/theme-default.css')}}"/>
    <link rel="stylesheet" href="{{asset('sneat/css/demo.css')}}"/>

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{asset('sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}"/>
    <link rel="stylesheet" href="{{asset('sneat/vendor/libs/sweetalert2/sweetalert2.min.css')}}"/>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}"/>

    <!-- Page CSS -->
    @stack('style')

    <!-- Helpers -->
    <script src="{{ asset('sneat/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('sneat/js/config.js') }}"></script>
</head>

<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->
        @include('components.sidebar')
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Navbar -->
            @include('components.navbar')
            <!-- / Navbar -->

            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    @yield('content')
                </div>
                <!-- / Content -->

                <!-- Footer -->
                @include('components.footer')
                <!-- / Footer -->

                <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{ asset('sneat/vendor/libs/jquery/jquery.js')}}"></script>
<script src="{{ asset('sneat/vendor/libs/popper/popper.js')}}"></script>
<script src="{{ asset('sneat/vendor/js/bootstrap.js')}}"></script>
<script src="{{ asset('sneat/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>

<script src="{{ asset('sneat/vendor/js/menu.js')}}"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{ asset('sneat/vendor/libs/masonry/masonry.js')}}"></script>
<script src="{{ asset('sneat/vendor/libs/sweetalert2/sweetalert2.all.min.js')}}"></script>

<!-- Main JS -->
<script src="{{ asset('sneat/js/main.js')}}"></script>
<script>
    $(document).on('click', '.btn-delete', function (req) {
        Swal.fire({
            title: '{{ __('menu.general.delete_confirm') }}',
            text: "{{ __('menu.general.delete_warning') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#696cff',
            confirmButtonText: '{{ __('menu.general.delete') }}',
            cancelButtonText: '{{ __('menu.general.cancel') }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $(this).parent('form').submit();
            }
        })
    });
</script>

<!-- Page JS -->
@stack('script')

<!-- Global file preview modal -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content file-preview-modal">
            <div class="modal-header align-items-center py-2">
                <h6 class="modal-title fw-bold text-truncate" id="filePreviewTitle">Preview</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-light" id="filePreviewBody"></div>
            <div class="modal-footer py-2">
                <a href="#" id="filePreviewDownload" target="_blank" class="btn btn-primary btn-sm">
                    <i class="bx bx-download me-1"></i>{{ __('task.download_work_document') }}
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">{{ __('menu.general.cancel') }}</button>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <script>
        Toast.fire({
            icon: 'success',
            title: '{{ session('success') }}'
        })
    </script>
@elseif(session('error'))
    <script>
        Toast.fire({
            icon: 'error',
            title: '{{ session('error') }}'
        })
    </script>
@elseif(session('info'))
    <script>
        Toast.fire({
            icon: 'info',
            title: '{{ session('info') }}'
        })
    </script>
@endif

<script>
    (function () {
        var nav = document.getElementById('layout-navbar');
        if (!nav) return;
        var lastY = window.scrollY, ticking = false, TH = 90;
        function update() {
            var y = window.scrollY;
            nav.classList.toggle('navbar-elevated', y > 40);
            if (y < TH) {
                nav.classList.remove('navbar-hidden');
            } else if (y > lastY) {
                nav.classList.add('navbar-hidden');
            } else {
                nav.classList.remove('navbar-hidden');
            }
            lastY = y;
            ticking = false;
        }
        window.addEventListener('scroll', function () {
            if (!ticking) { requestAnimationFrame(update); ticking = true; }
        }, { passive: true });
        update();
    })();
</script>

<script>
    (function () {
        // Bebaskan dropdown dari container ber-scroll (mis. .table-responsive)
        // agar menu aksi tidak terpotong / harus di-scroll dulu.
        function expandScrollAncestors(el) {
            var node = el ? el.parentElement : null;
            while (node && node !== document.body) {
                var style = window.getComputedStyle(node);
                var ox = style.overflowX, oy = style.overflowY;
                if (ox === 'auto' || ox === 'scroll' || oy === 'auto' || oy === 'scroll') {
                    node.classList.add('dropdown-scroll-free');
                }
                node = node.parentElement;
            }
        }

        document.addEventListener('show.bs.dropdown', function (e) {
            expandScrollAncestors(e.target);
        });

        document.addEventListener('hidden.bs.dropdown', function () {
            window.requestAnimationFrame(function () {
                document.querySelectorAll('.dropdown-scroll-free').forEach(function (node) {
                    if (!node.querySelector('.dropdown-menu.show')) {
                        node.classList.remove('dropdown-scroll-free');
                    }
                });
            });
        });
    })();
</script>

<script>
    (function () {
        var modalEl = document.getElementById('filePreviewModal');
        if (!modalEl) return;

        function getExt(url) {
            var m = (url.split(/[?#]/)[0].split('.').pop() || '').toLowerCase();
            return m;
        }

        window.previewFile = function (url, name) {
            var body = document.getElementById('filePreviewBody');
            var title = document.getElementById('filePreviewTitle');
            var dl = document.getElementById('filePreviewDownload');
            var ext = getExt(url);
            var content = '';

            if (['png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp', 'svg', 'avif'].indexOf(ext) !== -1) {
                content = '<div class="d-flex align-items-center justify-content-center p-3"><img src="' + url + '" alt="' + name + '" style="max-width:100%;max-height:70vh;object-fit:contain;border-radius:8px;"></div>';
            } else if (ext === 'pdf') {
                content = '<iframe src="' + url + '" style="width:100%;height:70vh;border:0;" title="PDF"></iframe>';
            } else {
                content = '<div class="p-5 text-center"><i class="bx bx-file text-muted" style="font-size:2.6rem;"></i><p class="text-muted mt-2 mb-0">' + name + '</p></div>';
            }

            body.innerHTML = content;
            if (title) title.textContent = name || 'Preview';
            if (dl) dl.href = url;

            if (window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        };

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-preview-url]');
            if (btn) {
                e.preventDefault();
                window.previewFile(btn.getAttribute('data-preview-url'), btn.getAttribute('data-preview-name') || 'File');
            }
        });
    })();
</script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html>

