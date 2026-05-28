<div class="invoice-template-document rounded-xl border border-gray-200 bg-white shadow-sm">
    @if(!empty($templateName))
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 bg-gray-50/80 px-4 py-2.5">
            <span class="text-xs font-medium text-gray-500">
                Design: <span class="text-gray-800">{{ $templateName }}</span>
            </span>
            <span class="text-[11px] text-gray-400">Matches PDF &amp; email attachment</span>
        </div>
    @endif
    <iframe
        class="w-full border-0 bg-white block"
        style="min-height: 960px; height: 90vh;"
        title="Invoice {{ $invoiceNumber ?? 'preview' }}"
        srcdoc="{!! $invoiceDocumentSrcdoc !!}"
        scrolling="auto"
        sandbox="allow-same-origin"
    ></iframe>
</div>
