<x-app-layout>
    <x-page-header title="GPM / Cost Report" subtitle="Laporan Gross Profit Margin — Rumus: 100% - (Harga Inventory Out (HPP) / Harga Penjualan Bersih)">
        <x-slot name="breadcrumb">
            <a href="{{ route('report.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">Reports</a>
            <span class="text-gray-300 mx-2">/</span>
            <span class="text-gray-800 font-medium">GPM / Cost</span>
        </x-slot>
    </x-page-header>

    <div class="flex flex-col gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <form action="{{ route('report.gpm-cost') }}" method="GET">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="w-72">
                            <label for="period" class="block text-sm font-medium text-gray-700 mb-1">Filter Period</label>
                            <select id="period" name="period" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                <option value="">All Periods</option>
                                @foreach($periods as $p)
                                    <option value="{{ $p }}" {{ $period === $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="bg-primary-700 text-white hover:bg-primary-800 rounded-lg px-4 py-2 text-sm font-medium inline-flex items-center">
                            Filter
                        </button>
                    </div>

                    <a href="{{ route('report.gpm', array_filter(['period' => $period])) }}"
                       class="border border-primary-500 text-primary-700 hover:bg-primary-50 rounded-lg px-4 py-2 text-sm font-medium inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Excel
                    </a>
                </div>
            </form>
        </div>

        @php
            $gpmCardClasses = match (true) {
                $total_selling <= 0 => 'bg-gray-50 border-gray-200',
                $total_gpm >= 20 => 'bg-green-50 border-green-200',
                $total_gpm >= 10 => 'bg-amber-50 border-amber-200',
                default => 'bg-red-50 border-red-200',
            };
            $gpmTextClasses = match (true) {
                $total_selling <= 0 => 'text-gray-900',
                $total_gpm >= 20 => 'text-green-600',
                $total_gpm >= 10 => 'text-amber-600',
                default => 'text-red-600',
            };
        @endphp

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Summary</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Qty Sold</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalQty, 0, ',', '.') }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Inventory Out (HPP)</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($total_hpp, 0, ',', '.') }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Penjualan Bersih</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($total_selling, 0, ',', '.') }}</p>
                </div>
                <div class="p-4 rounded-xl border {{ $total_laba_rugi >= 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Laba Kotor</p>
                    <p class="text-2xl font-bold mt-1 {{ $total_laba_rugi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $total_laba_rugi >= 0 ? '+' : '' }}Rp {{ number_format($total_laba_rugi, 0, ',', '.') }}
                    </p>
                </div>
                <div class="p-4 rounded-xl border {{ $gpmCardClasses }}">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">GPM (%)</p>
                    <p class="text-2xl font-bold mt-1 {{ $gpmTextClasses }}">
                        {{ number_format($total_gpm, 2, ',', '.') }}%
                    </p>
                    <p class="text-[11px] text-gray-500 mt-1">100% - (HPP / Penjualan Bersih)</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-base font-semibold text-gray-800 mb-4">GPM per Category</h3>
            @if($gpmByCategory->isEmpty())
                <x-empty-state title="No GPM Data Yet" description="Belum ada data distribusi pada periode yang dipilih." />
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty Sold</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Inventory Out (HPP)</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Penjualan Bersih</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Laba Kotor</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">GPM (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($gpmByCategory as $index => $cat)
                                @php
                                    $catBadgeType = match (true) {
                                        $cat['total_selling_price'] <= 0 => 'neutral',
                                        $cat['gpm_percentage'] >= 20 => 'success',
                                        $cat['gpm_percentage'] >= 10 => 'warning',
                                        default => 'danger',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $cat['category'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 text-right">{{ number_format($cat['total_qty'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 text-right">Rp {{ number_format($cat['total_hpp'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 text-right">Rp {{ number_format($cat['total_selling_price'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-semibold {{ $cat['laba_rugi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $cat['laba_rugi'] >= 0 ? '+' : '' }}Rp {{ number_format($cat['laba_rugi'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">
                                        <x-badge :type="$catBadgeType">{{ number_format($cat['gpm_percentage'], 2, ',', '.') }}%</x-badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            @php
                                $totalBadgeType = match (true) {
                                    $total_selling <= 0 => 'neutral',
                                    $total_gpm >= 20 => 'success',
                                    $total_gpm >= 10 => 'warning',
                                    default => 'danger',
                                };
                            @endphp
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-sm font-semibold text-gray-900">TOTAL</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">{{ number_format($totalQty, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">Rp {{ number_format($total_hpp, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">Rp {{ number_format($total_selling, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm font-bold text-right {{ $total_laba_rugi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $total_laba_rugi >= 0 ? '+' : '' }}Rp {{ number_format($total_laba_rugi, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-right">
                                    <x-badge :type="$totalBadgeType">{{ number_format($total_gpm, 2, ',', '.') }}%</x-badge>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-base font-semibold text-gray-800 mb-4">GPM Detail per Item</h3>

            @php $gpmArray = $gpmData->values()->toArray(); @endphp

            <div x-data="{
                search: '',
                items: {{ Js::from($gpmArray) }},
                get filtered() {
                    if (!this.search.trim()) return this.items;
                    const q = this.search.toLowerCase();
                    return this.items.filter(i =>
                        String(i.item_name ?? '').toLowerCase().includes(q) ||
                        String(i.category_name ?? '').toLowerCase().includes(q) ||
                        String(i.item_code ?? '').toLowerCase().includes(q)
                    );
                },
                get totalQty() { return this.filtered.reduce((s, i) => s + Number(i.qty_sold ?? 0), 0); },
                get totalHpp() { return this.filtered.reduce((s, i) => s + Number(i.total_hpp ?? 0), 0); },
                get totalSell() { return this.filtered.reduce((s, i) => s + Number(i.total_selling_price ?? 0), 0); },
                get totalPl() { return this.totalSell - this.totalHpp; },
                get totalGpm() { return this.totalSell > 0 ? (1 - (this.totalHpp / this.totalSell)) * 100 : 0; },
                fmt(n) { return 'Rp ' + Number(n ?? 0).toLocaleString('id-ID'); },
                num(n) { return Number(n ?? 0).toLocaleString('id-ID'); },
                pct(n) { return Number(n ?? 0).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '%'; },
                badgeClass(gpm, sell) {
                    if (Number(sell ?? 0) <= 0) return 'bg-gray-100 text-gray-700';
                    if (Number(gpm ?? 0) >= 20) return 'bg-green-100 text-green-800';
                    if (Number(gpm ?? 0) >= 10) return 'bg-amber-100 text-amber-800';
                    return 'bg-red-100 text-red-800';
                },
            }">
                <div class="mb-4 flex items-center gap-3">
                    <input type="text"
                           x-model="search"
                           placeholder="Search items..."
                           class="w-72 border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                    <span class="text-xs text-gray-400" x-text="`${filtered.length} items`"></span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Sold</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg HPP</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Selling</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Inventory Out (HPP)</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Penjualan Bersih</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Laba Kotor</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">GPM (%)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(item, index) in filtered" :key="item.item_id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <div x-text="item.item_name"></div>
                                        <div class="text-xs text-gray-400" x-text="item.item_code"></div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500" x-text="item.category_name"></td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="num(item.qty_sold)"></td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="fmt(item.hpp)"></td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="fmt(item.selling_price)"></td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="fmt(item.total_hpp)"></td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="fmt(item.total_selling_price)"></td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-right font-semibold"
                                        :class="item.laba_rugi >= 0 ? 'text-green-600' : 'text-red-600'"
                                        x-text="(item.laba_rugi >= 0 ? '+' : '') + fmt(item.laba_rugi)"></td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="badgeClass(item.gpm_percentage, item.total_selling_price)"
                                              x-text="pct(item.gpm_percentage)"></span>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filtered.length === 0">
                                <td colspan="10" class="px-4 py-6 text-center text-sm text-gray-500">No GPM data yet.</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50" x-show="filtered.length > 0">
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-sm font-semibold text-gray-900">TOTAL</td>
                                <td class="px-4 py-4 text-sm font-semibold text-gray-900 text-right" x-text="num(totalQty)"></td>
                                <td colspan="2"></td>
                                <td class="px-4 py-4 text-sm font-semibold text-gray-900 text-right" x-text="fmt(totalHpp)"></td>
                                <td class="px-4 py-4 text-sm font-semibold text-gray-900 text-right" x-text="fmt(totalSell)"></td>
                                <td class="px-4 py-4 text-sm font-bold text-right"
                                    :class="totalPl >= 0 ? 'text-green-600' : 'text-red-600'"
                                    x-text="(totalPl >= 0 ? '+' : '') + fmt(totalPl)"></td>
                                <td class="px-4 py-4 text-sm text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          :class="badgeClass(totalGpm, totalSell)"
                                          x-text="pct(totalGpm)"></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
