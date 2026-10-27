<x-app-layout>
    <x-page-header title="GPM / Cost Report" subtitle="Laporan margin laba kotor (Gross Profit Margin) per kategori dan item">
        <x-slot name="breadcrumb">
            <a href="{{ route('report.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">Reports</a>
            <span class="text-gray-300 mx-2">/</span>
            <span class="text-gray-800 font-medium">GPM / Cost</span>
        </x-slot>
    </x-page-header>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <form action="{{ route('report.gpm-cost') }}" method="GET">
                <div class="flex flex-wrap gap-4 items-end">
                    <div class="w-72">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter Period</label>
                        <select name="period" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="">All Periods</option>
                            @foreach($periods as $p)
                                <option value="{{ $p }}" {{ $period === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="bg-primary-700 text-white hover:bg-primary-800 rounded-lg px-4 py-2 text-sm font-medium inline-flex items-center">
                            Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Qty Sold</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalQty) }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total COGS</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($total_hpp, 0, ',', '.') }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Sales</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($total_selling, 0, ',', '.') }}</p>
                </div>
                <div class="p-4 rounded-xl border {{ $total_laba_rugi >= 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Profit / Loss</p>
                    <p class="text-2xl font-bold mt-1 {{ $total_laba_rugi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $total_laba_rugi >= 0 ? '+' : '' }}Rp {{ number_format($total_laba_rugi, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-base font-semibold text-gray-800 mb-4">GPM per Category</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty Sold</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total COGS</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Sales</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Profit / Loss</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($gpmByCategory as $index => $cat)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $cat['category'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 text-right">{{ number_format($cat['total_qty']) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 text-right">Rp {{ number_format($cat['total_hpp'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 text-right">Rp {{ number_format($cat['total_selling_price'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-right font-semibold {{ $cat['laba_rugi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $cat['laba_rugi'] >= 0 ? '+' : '' }}Rp {{ number_format($cat['laba_rugi'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No GPM data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                                i.item_name.toLowerCase().includes(q) ||
                                i.category_name.toLowerCase().includes(q) ||
                                i.item_code.toLowerCase().includes(q)
                            );
                        },
                        get totalQty() { return this.filtered.reduce((s, i) => s + Number(i.qty_sold), 0); },
                        get totalHpp() { return this.filtered.reduce((s, i) => s + Number(i.total_hpp), 0); },
                        get totalSell() { return this.filtered.reduce((s, i) => s + Number(i.total_selling_price), 0); },
                        get totalPl() { return this.totalSell - this.totalHpp; },
                        fmt(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); },
                        num(n) { return Number(n).toLocaleString('id-ID'); },
                    }">
                        <div class="mb-4 flex items-center gap-3">
                            <input type="text"
                                   x-model="search"
                                   placeholder="Search items..."
                                   class="w-72 border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            <span class="text-xs text-gray-400" x-text="`${filtered.length} items`"></span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Sold</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">COGS</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total COGS</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Profit / Loss</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="(item, index) in filtered" :key="item.item_id">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="index + 1"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="item.item_name"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="item.category_name"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="num(item.qty_sold)"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="fmt(item.hpp)"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="fmt(item.selling_price)"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="fmt(item.total_hpp)"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right" x-text="fmt(item.total_selling_price)"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold"
                                                :class="item.laba_rugi >= 0 ? 'text-green-600' : 'text-red-600'"
                                                x-text="(item.laba_rugi >= 0 ? '+' : '') + fmt(item.laba_rugi)"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="filtered.length === 0">
                                        <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">No GPM data yet.</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50" x-show="filtered.length > 0">
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-sm font-semibold text-gray-900">TOTAL</td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right" x-text="num(totalQty)"></td>
                                        <td colspan="2"></td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right" x-text="fmt(totalHpp)"></td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right" x-text="fmt(totalSell)"></td>
                                        <td class="px-6 py-4 text-sm font-bold text-right"
                                            :class="totalPl >= 0 ? 'text-green-600' : 'text-red-600'"
                                            x-text="(totalPl >= 0 ? '+' : '') + fmt(totalPl)"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
        </div>
    </div>
</x-app-layout>
