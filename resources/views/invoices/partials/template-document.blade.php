<div class="invoice-template-document rounded-xl border border-gray-200 bg-white shadow-sm">
    <iframe
        class="w-full border-0 bg-white block"
        style="min-height: 960px; height: 90vh;"
        title="Invoice {{ $invoiceNumber ?? 'preview' }}"
        srcdoc="{!! $invoiceDocumentSrcdoc !!}"
        scrolling="auto"
        sandbox="allow-same-origin"
    ></iframe>
</div>
