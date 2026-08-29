@extends('layouts.marketing')

@section('title', 'Late Fee Calculator | Calculate Interest on Overdue Invoices | Inveqi')
@section('meta_description', 'Free late fee calculator. Calculate how much interest to charge on an overdue invoice, with support for daily or monthly rates and a one-time fee.')
@section('meta_keywords', 'late fee calculator, late payment fee calculator, invoice interest calculator, overdue invoice calculator, late fee interest, payment penalty calculator')
@section('og_title', 'Late Fee Calculator | Inveqi')
@section('og_description', 'Calculate the interest and late fees to charge on an overdue invoice with this free tool.')

@push('jsonld')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebApplication",
    "name": "Late Fee Calculator",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web",
    "url": "{{ url()->current() }}",
    "description": "Calculate the interest and late fees to charge on an overdue invoice."
}
</script>
@endpush

@section('content')
@include('landing.partials.nav')

<section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10" style="background: var(--gradient-hero)"></div>
    <div class="absolute inset-0 -z-10 bg-grid opacity-40" style="mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%); -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%);"></div>
    <div class="mx-auto max-w-4xl px-6 py-20 text-center">
        <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">{{ $tool['kicker'] }}</p>
        <h1 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl">Late fee calculator</h1>
        <p class="mx-auto mt-5 max-w-2xl text-lg text-gray-500">Work out how much to charge on an overdue invoice. Set an interest rate and a one-time fee, and see the total due right away.</p>
    </div>
</section>

<section class="border-t border-gray-200/60 py-16">
    <div class="mx-auto max-w-3xl px-6">
        <div class="rounded-3xl border border-gray-200 bg-white p-8 shadow-soft">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Invoice amount ($)</label>
                    <input id="amount" type="number" value="1000" min="0" step="0.01" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Days overdue</label>
                    <input id="days" type="number" value="30" min="0" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Interest rate (%)</label>
                    <input id="rate" type="number" value="12" min="0" step="0.01" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Rate period</label>
                    <select id="period" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none">
                        <option value="annual">Annual</option>
                        <option value="monthly">Monthly</option>
                        <option value="daily">Daily</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium text-gray-700">One-time late fee ($)</label>
                    <input id="flatFee" type="number" value="0" min="0" step="0.01" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none">
                    <p class="mt-1 text-xs text-gray-400">A fixed penalty charged once the invoice becomes overdue.</p>
                </div>
            </div>

            <div class="mt-8 rounded-2xl bg-gray-50 p-6">
                <div class="flex justify-between text-sm text-gray-500"><span>Original amount</span><span id="original">$0.00</span></div>
                <div class="mt-2 flex justify-between text-sm text-gray-500"><span>Interest</span><span id="interest">$0.00</span></div>
                <div class="mt-2 flex justify-between text-sm text-gray-500"><span>One-time fee</span><span id="flat">$0.00</span></div>
                <div class="mt-4 flex justify-between border-t border-gray-200 pt-4 text-lg font-bold text-gray-900"><span>Total due</span><span id="total">$0.00</span></div>
            </div>

            <p class="mt-6 text-xs text-gray-400">Free to use, no account required. Inveqi can send automatic payment reminders so invoices get paid on time.</p>
        </div>
    </div>
</section>

<section class="border-t border-gray-200/60 bg-gray-50/50 py-20">
    <div class="mx-auto max-w-4xl px-6">
        <h2 class="text-3xl font-bold tracking-tight">About charging late fees</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-calendar-check text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">State your terms</h3>
                <p class="mt-2 text-sm text-gray-500">Put your late-fee policy on the invoice or your contract so customers know upfront.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-bell text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">Remind, don't punish</h3>
                <p class="mt-2 text-sm text-gray-500">Send a friendly reminder first. Most late payments are simply forgotten.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600"><i class="fas fa-shield-halved text-sm"></i></div>
                <h3 class="mt-4 text-lg font-semibold">Enforce fairly</h3>
                <p class="mt-2 text-sm text-gray-500">Keep rates reasonable and consistent. Check local rules where required.</p>
            </div>
        </div>
    </div>
</section>

@include('tools.partials.related', ['related' => $related])

@include('landing.partials.section-cta')
@include('landing.partials.footer')

<button id="scrollTopBtn" class="hidden fixed bottom-6 right-6 z-50 flex h-11 w-11 items-center justify-center rounded-full gradient-primary text-white shadow-glow transition hover:scale-110">
    <i class="fas fa-arrow-up"></i>
</button>
@endsection

<script>
(function () {
    var amount = document.getElementById('amount');
    var days = document.getElementById('days');
    var rate = document.getElementById('rate');
    var period = document.getElementById('period');
    var flatFee = document.getElementById('flatFee');

    function money(n) {
        return n.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
    }

    function recalc() {
        var a = parseFloat(amount.value) || 0;
        var d = parseFloat(days.value) || 0;
        var r = parseFloat(rate.value) || 0;
        var flat = parseFloat(flatFee.value) || 0;
        var p = period.value;
        var interest = 0;
        if (r > 0 && d > 0) {
            if (p === 'annual') interest = a * (r / 100) * (d / 365);
            else if (p === 'monthly') interest = a * (r / 100) * (d / 30);
            else interest = a * (r / 100) * d;
        }
        document.getElementById('original').textContent = money(a);
        document.getElementById('interest').textContent = money(interest);
        document.getElementById('flat').textContent = money(flat);
        document.getElementById('total').textContent = money(a + interest + flat);
    }

    ['amount','days','rate','flatFee'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', recalc);
    });
    period.addEventListener('change', recalc);
    recalc();
})();
</script>
