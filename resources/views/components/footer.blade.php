<footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl">
        <div class="footer-inner d-flex flex-column flex-md-row align-items-center justify-content-between gap-2 py-3">
            <p class="footer-copy mb-0 text-center text-md-start">
                <span class="footer-year">&copy; {{ date('Y') }}</span>
                <span class="footer-dot d-none d-sm-inline" aria-hidden="true">&middot;</span>
                <span class="footer-made">{{ __('footer.made_by') }}</span>
                <a href="https://www.instagram.com/muhammad_raihan0307" target="_blank" rel="noopener" class="footer-author">Muhammad Raihan - Indonesia</a>
            </p>
            <span class="footer-brand">
                <i class="bx bx-envelope-open"></i>
                <span>{{ config('app.name') }}</span>
            </span>
        </div>
    </div>
</footer>
