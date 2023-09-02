<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Transaction &raquo; #{{ $transaction->id }} {{ $transaction->name }}
        </h2>
    </x-slot>

    <x-slot name="script">
        <script>
            // AJAX DataTable
            var datatable = $('#crudTable').DataTable({
                ajax: {
                    url: '{!! url()->current() !!}',
                },
                columns: [
                    { data: 'id', name: 'id', width: '5%'},
                    { data: 'product.name', name: 'product.name' },
                    { data: 'product.price', name: 'product.price' },
                    { data: 'quantity', name: 'quantity' },
                ],
            });
        </script>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between">
                <h2 class="font-semibold text-lg text-gray-800 leading-tight mb-5">Transaction Details</h2>
                <div>
                    <a href="{{ route('dashboard.transaction.pdf', $transaction->id) }}" target="_blank" class="bg-gray-800 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">Cetak Invoice</a>
                    <a href="{{ route('dashboard.transaction.edit', $transaction->id) }}" class="bg-green-400 hover:bg-green-500 text-white font-bold py-2 px-4 rounded">Proses Pesanan</a>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow sm:rounded-lg mb-10">
                 <div class="p-6 bg-white border-b border-gray-200">
                    <table class="table-auto w-full">
                        <tbody>
                            <tr>
                                <th class="border px-6 py-4 text-right">Invoice Number</th>
                                <td class="border px-6 py-4">{{ $transaction->invoice_number }}</td>
                            </tr>
                            <tr>
                                <th class="border px-6 py-4 text-right">Name</th>
                                <td class="border px-6 py-4">{{ $transaction->user->name }}</td>
                            </tr>
                            <tr>
                                <th class="border px-6 py-4 text-right">Email</th>
                                <td class="border px-6 py-4">{{ $transaction->user->email }}</td>
                            </tr>
                            <tr>
                                <th class="border px-6 py-4 text-right">Address</th>
                                <td class="border px-6 py-4">{{ $transaction->address }}</td>
                            </tr>
                            <tr>
                                <th class="border px-6 py-4 text-right">Payment</th>
                                <td class="border px-6 py-4">{{ $transaction->payment }}</td>
                            </tr>
                            <tr>
                                <th class="border px-6 py-4 text-right">Total Price</th>
                                <td class="border px-6 py-4">{{ number_format($transaction->total_price) }}</td>
                            </tr>
                            <tr>
                                <th class="border px-6 py-4 text-right">Shipping Price</th>
                                <td class="border px-6 py-4">{{ number_format($transaction->shipping_price) }}</td>
                            </tr>
                            <tr>
                                <th class="border px-6 py-4 text-right">Status</th>
                                <td class="border px-6 py-4">{{ $transaction->status == "PENDING" ? "Belum dibayar" : ($transaction->status == "SUCCESS" ? "Diterima" : ($transaction->status == "SHIPPING" ? "Dikemas" : "Dikirim")) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <h2 class="font-semibold text-lg text-gray-800 leading-tight mb-5">Transaction Items</h2>
            <div class="shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 bg-white sm:p-6">
                    <table id="crudTable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
